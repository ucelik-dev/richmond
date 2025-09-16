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
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class PaymentDataTable extends DataTable
{

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
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
                    $user = $c->user;

                    // MAIN role name from pivot (collection; take the first)
                    $main = $user?->mainRoleRelation?->first();
                    $roleName = $main?->name;

                    // Fallback to the first role if no main role flagged
                    if (!$roleName && $user && $user->relationLoaded('roles')) {
                        $roleName = optional($user->roles->first())->name;
                    }

                    $userName = e($user?->name ?? 'User');

                    // Paid/unpaid -> choose color
                    $status = strtolower((string) $c->status);   // e.g. "paid" / "unpaid"
                    $class  = $status === 'paid' ? 'success' : 'danger';

                    return '<span class="mb-1 badge bg-'.$class.' text-'.$class.'-fg">'
                        . e(ucwords($roleName)) . ' : ' . $userName . ' : ' . currency_format($c->amount)
                        . '</span><br>';
                })->implode(' ');

                return $badges ?: 'No commissions';
            })

            ->filterColumn('commissions_summary', function ($q, $keyword) {
                $keyword = trim($keyword);

                $q->where(function ($w) use ($keyword) {
                    // match commission user's name
                    $w->whereHas('commissions.user', function ($uq) use ($keyword) {
                        $uq->where('name', 'like', "%{$keyword}%");
                    })
                    // or match the commission user's MAIN role name
                    ->orWhereHas('commissions.user.mainRoleRelation', function ($rq) use ($keyword) {
                        $rq->where('name', 'like', "%{$keyword}%");
                    });
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

                if (Auth::user()->can('edit_admin_payments')) {
                    $btns .= '<a href="'.route('admin.payment.edit', $row->id).'"
                               class="btn-sm btn-primary me-2 text-decoration-none">
                               <i class="fa-solid fa-pen-to-square fa-lg"></i></a>';
                }
                if (Auth::user()->can('delete_admin_payments')) {
                    $btns .= '<a href="'.route('admin.payment.destroy', $row->id).'"
                               class="text-red delete-item text-decoration-none">
                               <i class="fa-solid fa-trash-can fa-lg"></i></a>';
                }
                return $btns;

            })
    
            ->rawColumns(['user_name','courses_badges','installments_summary','commissions_summary','status_name','created_at','action'])
            ->setRowId('id');
    }

    public function query(Payment $model): QueryBuilder
    {
        $q = $model->newQuery()
            // sums are still handy if you show a total anywhere
            ->withSum('commissions as commissions_total', 'amount')

            // eager-load what you'll render (no N+1):
            ->with([
                'installments:id,payment_id,amount,due_date,status,paid_at',

                // all commissions for the payment
                'commissions:id,payment_id,user_id,amount,status,paid_at',

                // the commission owner (user) + their MAIN role
                'commissions.user:id,name',
                'commissions.user.mainRoleRelation:id,name',   // <-- pulls the main role name only
                // (optional fallback) first role if main not set:
                'commissions.user.roles:id,name',

            ])

            // base select + joins for columns/aliases you display
            ->select([
                'payments.id',
                'payments.user_id',
                'payments.total',
                'payments.amount',
                'payments.discount',
                'payments.status_id',
                'payments.notes',

                'users.name  as user_name',
                'users.email as user_email',
                'users.contact_email as user_contact_email',
                'users.phone as user_phone',

                'payment_statuses.name as status_name',
                'payment_statuses.color as status_color',

                'courses.title as course_title',
                'course_levels.name as level_name',   

                DB::raw("DATE_FORMAT(users.created_at, '%Y-%m-%d') AS registered_text"),

            ])
            ->leftJoin('users', 'users.id', '=', 'payments.user_id')
            ->leftJoin('payment_statuses', 'payment_statuses.id', '=', 'payments.status_id')
            ->leftJoin('courses', 'courses.id', '=', 'payments.course_id')
            ->leftJoin('course_levels',  'course_levels.id',  '=', 'courses.level_id');

        // optional filters you already had
        if ($status = request('status_id')) {
            $q->where('payments.status_id', $status);
        }
        if ($userId = request('user_id')) {
            $q->where('payments.user_id', $userId);
        }

        return $q; // IMPORTANT: no ->get(), let Yajra page with start/length
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('payment-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
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
                        Button::make('excel')->className('btn btn-primary py-1 px-2'),
                        Button::make('print')->className('btn btn-primary py-1 px-2'),
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
            Column::computed('action')->title('Action')->exportable(false)->printable(false)->addClass('text-nowrap')->width(50),
        ];
    }

    protected function filename(): string
    {
        return 'Payment_' . date('YmdHis');
    }

}
