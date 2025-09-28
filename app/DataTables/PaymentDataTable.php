<?php

namespace App\DataTables;

use App\Models\Payment;
use App\Models\PaymentStatus;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use App\Models\Installment;
use Yajra\DataTables\Services\DataTable;

class PaymentDataTable extends DataTable
{

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $dt= (new EloquentDataTable($query))
            ->addIndexColumn()

            ->editColumn('user_name', function ($row) {
                return '<a href="'.route('admin.student.edit', $row->user_id).'">'. e($row->user_name). '</a>';
            })

            // already formatted, no editColumn needed (optional):
            ->editColumn('registered_text', fn($row) => $row->registered_text ?? '')

            ->filterColumn('registered_text', function ($q, $keyword) {
                // search the alias directly
                $q->havingRaw('registered_text LIKE ?', ["%{$keyword}%"]);
            })

            ->orderColumn('registered_text', fn ($q, $dir) =>
                $q->orderBy('users.created_at', $dir)
            )

            // COURSES badge(s)
            ->addColumn('courses_badges', function ($row) {
                // If you have ONE course_id on payments
                if (isset($row->course_title) && $row->course_title) {
                    return '<span class="badge bg-primary text-primary-fg align-self-start mb-1">'.$row->course_title.' ('.$row->level_name.')</span>';
                }

                // If you have MANY via pivot (eager-loaded below)
                if ($row->relationLoaded('courses')) {
                    return $row->courses->map(function ($c) {
                        return '<span class="badge bg-primary text-primary-fg align-self-start mb-1">'.$c->title.'</span>';
                    })->implode(' ');
                }

                return '';
            })
            ->filterColumn('courses_badges', function ($q, $keyword) {
                $keyword = trim($keyword);

                $q->whereHas('course', function ($cq) use ($keyword) {
                    $cq->where('title', 'like', "%{$keyword}%")
                    ->orWhereHas('level', function ($lq) use ($keyword) {
                        $lq->where('name', 'like', "%{$keyword}%");
                    });
                });
            })

            // TOTAL formatted
            ->editColumn('total', fn($row) => currency_format($row->total))

            // INSTALLMENTS list
            ->addColumn('installments_summary', function ($row) {
                if (!$row->relationLoaded('installments')) return '';
                return $row->installments
                    ->sortBy('due_date')
                    ->map(function ($i) {
                        $state = strtolower($i->status) === 'paid' ? 'success' : 'yellow';
                        $label = ucfirst($i->status);
                        $due_date  = optional(\Carbon\Carbon::parse($i->due_date))->format('d-m-Y');
                        $paid_at  = $i->paid_at ? '('.optional(\Carbon\Carbon::parse($i->paid_at))->format('d-m-Y').')' : null;
                        $amount   = currency_format($i->amount);
                        return "<span class=\"badge bg-{$state} text-{$state}-fg me-1 mb-1\" style=\"width:70px\">{$label}</span>
                                <span class=\"badge bg-light text-muted me-2 mb-1\">{$due_date} : {$amount} {$paid_at}</span><br>";
                    })->implode('');
            })

            // COMMISSIONS summary
            ->addColumn('commissions_summary', function ($row) {
                if (!$row->relationLoaded('commissions') || $row->commissions->isEmpty()) {
                    return 'No commissions';
                }

                $badges = $row->commissions->map(function ($c) {
                    $user = $c->user; // may be null if user_id is null/missing

                    // Role label
                    $roleName = null;
                    if ($user) {
                        $main = $user?->mainRoleRelation?->first();
                        $roleName = $main?->name;
                        if (!$roleName && $user->relationLoaded('roles')) {
                            $roleName = optional($user->roles->first())->name;
                        }
                    } else {
                        $roleName = 'External'; // shown when there is no user
                    }

                    // Name: user name OR payee_name
                    $userName = e($user?->name ?: ($c->payee_name ?: 'Unknown'));

                    // Paid/unpaid -> choose color
                    $status = strtolower((string) $c->status);   // "paid" / "unpaid"
                    $class  = $status === 'paid' ? 'success' : 'danger';

                    // Build label cleanly (no leading/trailing " : ")
                    $label = implode(' : ', array_filter([ucwords($roleName), $userName, currency_format($c->amount)]));

                    return '<span class="mb-1 badge bg-'.$class.' text-'.$class.'-fg">'.$label.'</span><br>';
                })->implode(' ');

                return $badges ?: 'No commissions';
            })


            ->filterColumn('commissions_summary', function ($q, $keyword) {
                $keyword  = trim($keyword);
                if ($keyword === '') return;

                $kwLower = mb_strtolower($keyword, 'UTF-8');

                $q->where(function ($w) use ($keyword, $kwLower) {
                    // User name
                    $w->whereHas('commissions.user', function ($uq) use ($keyword) {
                        $uq->where('name', 'like', "%{$keyword}%");
                    })
                    // Main role name (Agent, Sales, etc.)
                    ->orWhereHas('commissions.user.mainRoleRelation', function ($rq) use ($keyword) {
                        $rq->where('name', 'like', "%{$keyword}%");
                    })
                    // External payee name text
                    ->orWhereHas('commissions', function ($cq) use ($keyword) {
                        $cq->where('payee_name', 'like', "%{$keyword}%");
                    });

                    // If searching "external", also match commissions with no user
                    if (strpos($kwLower, 'external') !== false) {
                        $w->orWhereHas('commissions', function ($cq) {
                            $cq->whereNull('user_id')
                            ->orWhere('user_id', 0); // safety if 0 is ever used
                        });
                    }
                });
            })

            // STATUS badge
            ->editColumn('status_name', function ($row) {
                $label = ucwords($row->status_name) ?? '—';
                $color = $row->status_color ?? null;
                return "<span class='badge bg-{$color} text-{$color}-fg'>{$label}</span>";
            })

            // ACTIONS
            ->addColumn('action', function($row) {
                $btns = '';

                if(Auth::user()?->canResource('admin_payments','edit')){
                    $btns .= '<a href="'.route('admin.payment.edit', $row->id).'"
                               class="btn-sm btn-primary me-2 text-decoration-none">
                               <i class="fa-solid fa-pen-to-square fa-lg"></i></a>';
                }
                if(Auth::user()?->canResource('admin_payments','delete')){
                    $btns .= '<a href="'.route('admin.payment.destroy', $row->id).'"
                               class="text-red delete-item text-decoration-none">
                               <i class="fa-solid fa-trash-can fa-lg"></i></a>';
                }
                return $btns;

            })
    
            ->rawColumns(['user_name','courses_badges','installments_summary','commissions_summary','status_name','created_at','action'])
            ->setRowId('id');

            /* ---- HEADER TOTAL (sum of PAID installments in the same window) ---- */
            $paid     = request('paid');       // today|week|month|year
            $paidFrom = request('paid_from');  // YYYY-MM-DD
            $paidTo   = request('paid_to');    // YYYY-MM-DD

            $from = $paidFrom ? \Carbon\Carbon::parse($paidFrom)->startOfDay() : null;
            $to   = $paidTo   ? \Carbon\Carbon::parse($paidTo)->endOfDay()     : null;

            if ($paid && !$from && !$to) {
                $now = \Carbon\Carbon::now(config('app.timezone'));
                switch ($paid) {
                    case 'today': $from = $now->copy()->startOfDay();   $to = $now->copy()->endOfDay();   break;
                    case 'week':  $from = $now->copy()->startOfWeek();  $to = $now->copy()->endOfWeek();  break;
                    case 'month': $from = $now->copy()->startOfMonth(); $to = $now->copy()->endOfMonth(); break;
                    case 'year':  $from = $now->copy()->startOfYear();  $to = $now->copy()->endOfYear();  break;
                }
            }

            $sum = Installment::query()
                ->where('status', 'paid')
                ->when($from, fn($q) => $q->where('paid_at', '>=', $from))
                ->when($to,   fn($q) => $q->where('paid_at', '<=', $to))
                ->whereHas('payment', function ($pq) {
                    // same Payment filters used in query()
                    $pq->whereHas('user.roles', fn($r) => $r->where('name', 'student'));
                    if ($statusId = request('status_id')) $pq->where('payments.status_id', $statusId);
                    if ($userId   = request('user_id'))   $pq->where('payments.user_id', $userId);
                })
                ->sum('amount');

            return $dt->with([
                'total_amount'      => $sum,
                'total_amount_text' => currency_format($sum),
            ]);

            
    }

    public function query(Payment $model): QueryBuilder
    {
        $q = $model->newQuery()
            // sums are still handy if you show a total anywhere
            ->withSum('commissions as commissions_total', 'amount')
            // eager-load what you'll render (no N+1):
            ->with([
                'installments:id,payment_id,amount,due_date,status,paid_at',
                'commissions:id,payment_id,user_id,payee_name,amount,status,paid_at',
                'commissions.user:id,name',
                'commissions.user.mainRoleRelation:id,name',
                'commissions.user.roles:id,name',
            ])
            ->select([
                'payments.id','payments.user_id','payments.total','payments.amount','payments.discount','payments.status_id','payments.notes',
                'users.name  as user_name','users.email as user_email','users.contact_email as user_contact_email','users.phone as user_phone',
                'payment_statuses.name as status_name','payment_statuses.color as status_color',
                'courses.title as course_title','course_levels.name as level_name',   
                DB::raw("DATE_FORMAT(users.created_at, '%Y-%m-%d') AS registered_text"),
            ])
            ->leftJoin('users', 'users.id', '=', 'payments.user_id')
            ->leftJoin('payment_statuses', 'payment_statuses.id', '=', 'payments.status_id')
            ->leftJoin('courses', 'courses.id', '=', 'payments.course_id')
            ->leftJoin('course_levels',  'course_levels.id',  '=', 'courses.level_id');

            // keep payments that belong to (main) students; exclude admin/manager users
            $q->whereHas('user.roles', function ($r) {
                $r->where('name','student');
            });

            // existing optional filters
            if ($status = request('status_id')) $q->where('payments.status_id', $status);
            if ($userId = request('user_id'))   $q->where('payments.user_id', $userId);

            // ---- NEW: paid installments time filter ----
            $paid = request('paid'); // today|week|month|year
            $paidFrom = request('paid_from');
            $paidTo   = request('paid_to');

            if ($paid || $paidFrom || $paidTo) {
                $now = Carbon::now(config('app.timezone'));
                $from = $paidFrom ? Carbon::parse($paidFrom)->startOfDay() : null;
                $to   = $paidTo   ? Carbon::parse($paidTo)->endOfDay()     : null;

                if ($paid && !$from && !$to) {
                    switch ($paid) {
                        case 'today': $from = $now->copy()->startOfDay();  $to = $now->copy()->endOfDay(); break;
                        case 'week':  $from = $now->copy()->startOfWeek();  $to = $now->copy()->endOfWeek(); break;
                        case 'month': $from = $now->copy()->startOfMonth(); $to = $now->copy()->endOfMonth(); break;
                        case 'year':  $from = $now->copy()->startOfYear();  $to = $now->copy()->endOfYear(); break;
                    }
                }

                // Only include payments having at least one PAID installment in the window
                $q->whereHas('installments', function ($iq) use ($from, $to) {
                    $iq->where('status','paid');
                    if ($from && $to) {
                        $iq->whereBetween('paid_at', [$from, $to]);
                    } elseif ($from) {
                        $iq->where('paid_at', '>=', $from);
                    } elseif ($to) {
                        $iq->where('paid_at', '<=', $to);
                    }
                });
            }

            return $q;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('payment-table')
                    ->columns($this->getColumns())
                    ->ajax([
                        'url'  => url()->current(),   // index route
                        'type' => 'GET',
                        'data' => 'function(d){
                            const qs = new URLSearchParams(window.location.search);
                            d.paid     = qs.get("paid")     || "";   // today|week|month|year
                            d.paid_from= qs.get("paid_from")|| "";   // optional explicit range
                            d.paid_to  = qs.get("paid_to")  || "";
                            d.status_id= qs.get("status_id")|| "";
                            d.user_id  = qs.get("user_id")  || "";
                        }',
                    ])
                    ->pageLength(50)
                    ->parameters([

                        'dom' => '<"top d-flex flex-column mb-2"
                                <"d-flex align-items-center justify-content-between flex-column flex-sm-row"
                                    <"left-section d-flex flex-column align-items-center align-items-sm-start"l
                                        <"d-flex justify-content-start mt-0 mb-1"i> 
                                    >
                                    <"right-section d-flex flex-column align-items-center align-items-sm-end mt-3 mt-sm-0"
                                        <"buttons-section"B>
                                        <"pagination-section mt-2 mb-1"p> 
                                    >
                                > 
                            >
                            rt
                            <"bottom d-flex align-items-center justify-content-between gap-3 flex-column flex-sm-row mt-3"ip>',

                        // Show « ‹ 1 2 3 › » style
                        'pagingType' => 'full_numbers',
                        'lengthMenu' => [[50, 100, 500, -1], [50, 100, 500, 'All']], // Custom entries per page options
                        'pageLength' => 50, // Default number of records per page
                        'responsive' => false,
                        'autoWidth' => false,
                        'processing' => true,
                        'serverSide' => true,
                        // Icon labels (works with Bootstrap 5 integration)
                        'language' => [
                            // keep your existing language keys...
                            'info'         => 'Showing <b>_TOTAL_</b> records',
                            'infoEmpty'    => 'No records',
                            'infoFiltered' => '',
                            'lengthMenu'   => '_MENU_ records per page',

                            // ✨ ICONS for pagination (use FA7 'fa-solid'; if you're on FA4 use 'fa')
                            'paginate' => [
                                'first'    => '<i class="fa-solid fa-angles-left"></i>',
                                'previous' => '<i class="fa-solid fa-chevron-left"></i>',
                                'next'     => '<i class="fa-solid fa-chevron-right"></i>',
                                'last'     => '<i class="fa-solid fa-angles-right"></i>',
                            ],
                        ],
                        // Make the pagers compact and tidy (top & bottom)
                        'drawCallback' => "function () {
                            var api = this.api();
                            var wrapper = $(api.table().container());
                            wrapper.find('.dataTables_paginate').addClass('mb-0');
                            wrapper.find('.dataTables_paginate .pagination').addClass('pagination-sm');
                            wrapper.find('.top .dataTables_paginate').addClass('ms-2');
                        }",

                        'paymentStatusOptions' => PaymentStatus::orderBy('name')->pluck('name')->values()->toArray(),

                        'initComplete' => <<<'JS'
                            function () {
                            var api = this.api();

                            // options injected from PHP (oInit)
                            var init = api.settings()[0].oInit || {};
                            var statusOptions = init.paymentStatusOptions || [];

                            api.columns().every(function () {
                                var column = this;
                                var dataSrc = column.dataSrc();
                                var $head  = $(column.header());
                                var title  = $head.text().trim();

                                // DO NOT add a filter for these columns
                                var noFilter = ['action','id']; // add more keys if needed
                                    if (noFilter.indexOf(dataSrc) !== -1) {
                                    return; // skip creating input/select
                                }

                                $head.find('.dt-filter').remove(); // clean

                                if (title === 'Status') {
                                // build a select with exact-match regex values (^Name$)
                                var $sel = $('<select/>', {
                                    'class': 'form-select mt-2 dt-filter text-capitalize'
                                }).append($('<option/>', { value: '', text: 'All' }));

                                if (statusOptions.length) {
                                    statusOptions.forEach(function (name) {
                                    $sel.append($('<option/>', { value: '^' + name + '$', text: name }));
                                    });
                                } else {
                                    // fallback: unique values from current page (not ideal with serverSide)
                                    column.data().unique().sort().each(function (d) {
                                    // strip possible badge HTML to get plain text
                                    var txt = $('<div>').html(d).text().trim();
                                    if (txt) $sel.append($('<option/>', { value: '^' + txt + '$', text: txt }));
                                    });
                                }

                                $head.append($sel);

                                $sel.on('change', function () {
                                    column.search(this.value, true, false).draw(); // regex = true, smart = false
                                });
                                } else {
                                // default: text input
                                var $inp = $('<input/>', {
                                    type: 'text',
                                    'class': 'form-control form-control-sm mt-2 dt-filter',
                                });
                                $head.append($inp);
                                $inp.on('keyup change', function () {
                                    column.search(this.value).draw();
                                });
                                }
                            });
                            }
                            JS,
                    ])
                    //->dom('Bfrtip')
                    ->orderBy(0)
                    ->selectStyleSingle()
                    ->buttons([
                Button::make('colvis')->className('btn btn-primary py-1 px-2'),

                Button::make('excel')
                    ->className('btn btn-primary py-1 px-2')
                    ->exportOptions([
                        'columns'   => ':visible:not(.no-print)',
                        'stripHtml' => true,
                        'format'    => [
                            // keep only the plain TH title
                            'header' => 'function (data, idx) {
                                var $h = $("<div>").html(data);
                                $h.find(".dt-filter").remove(); // drop selects/inputs in TH
                                return $.trim($h.text());
                            }',
                        ],
                    ]),

                    Button::make('print')
                        ->className('btn btn-primary py-1 px-2')
                        ->exportOptions([
                            'columns'   => ':visible:not(.no-print)',
                            'stripHtml' => true,
                            'format'    => [
                                'header' => 'function (data, idx) {
                                    var $h = $("<div>").html(data);
                                    $h.find(".dt-filter").remove();
                                    return $.trim($h.text());
                                }',
                            ],
                        ])
                        // hide any leftover header filters in the print window just in case
                        ->customize('function (win) {
                            $(win.document.head).append("<style>.dt-filter{display:none !important}</style>");
                        }'),
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('id')->title('#')->orderable(true)->searchable(true)->width(40),
            Column::make('user_name')->title('Name')->name('users.name')->orderable(false)->searchable(true),
            Column::make('user_email')->title('Email')->name('users.email')->orderable(false)->searchable(true)->visible(false),
            Column::make('user_contact_email')->title('Contact Email')->name('users.contact_email')->orderable(false)->searchable(true)->visible(false),
            Column::make('user_phone')->title('Phone')->name('users.phone')->orderable(false)->searchable(true)->visible(false),
            Column::make('registered_text')->title('Registered')->name('users.created_at')->orderable(true)->searchable(true)->visible(true)->width(100),
            Column::computed('courses_badges')->title('Courses')->orderable(false)->searchable(true),
            Column::make('amount')->title('Amount')->orderable(false)->searchable(true)->visible(false),
            Column::make('discount')->title('Discount')->orderable(false)->searchable(true)->visible(false),
            Column::make('total')->title('Total')->orderable(false)->searchable(true)->visible(true)->width(80),
            Column::computed('installments_summary')->title('Installments')->orderable(false)->searchable(false)->addClass('text-nowrap')->width(350),
            Column::computed('commissions_summary')->title('Commissions')->orderable(false)->searchable(true)->addClass('text-nowrap'),
            Column::make('status_name')->title('Status')->name('payment_statuses.name')->orderable(false)->searchable(true)->width(150),
            Column::make('notes')->title('Notes')->orderable(false)->searchable(true)->visible(false),
            Column::computed('action')->title('Action')->exportable(false)->printable(false)->addClass('text-nowrap')->width(50)->addClass('no-print'),
        ];
    }

    protected function filename(): string
    {
        return 'Payment_' . date('YmdHis');
    }

}
