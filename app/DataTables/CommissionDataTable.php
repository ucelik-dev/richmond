<?php

namespace App\DataTables;

use App\Models\Commission;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class CommissionDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $norm = function ($s) { return preg_replace('/[^\d.]/', '', (string) $s); };

        $dt = (new EloquentDataTable($query))
            ->addColumn('payee_cell', function ($r) {
                $txt = $r->payee_company ?: ($r->payee_user_name ?: ($r->fallback_payee_name ?: '-'));
                return e($txt);
            })

            // Role badge (agent / sales / external)
            ->addColumn('role_badge', function ($r) {
                $name = $r->role_name ?: 'external';
                return '<span class="badge bg-secondary text-secondary-fg text-capitalize">'.e($name).'</span>';
            })

            // Student name
            ->addColumn('student_name', fn($r) => e(optional($r->payment->user)->name ?: '—'))

            // Course + level badge
            ->addColumn('student_course', function ($r) {
                $c = $r->payment?->course;
                $title = $c?->title ?? 'Unknown Course';
                $level = $c?->level?->name;
                $label = $level ? $title.' ('.$level.')' : $title;

                return '<span class="badge bg-primary text-primary-fg align-self-start mb-1">'.e($label).'</span>';
            })

            // Course Price / Discount / Total
            ->addColumn('course_price',    fn($r) => $r->payment ? currency_format($r->payment->amount)   : '')
            ->addColumn('course_discount', fn($r) => $r->payment ? currency_format($r->payment->discount) : '')
            ->addColumn('course_total',    fn($r) => $r->payment ? currency_format($r->payment->total)    : '')

            // Commission amount
            ->editColumn('amount', fn($r) => e(currency_format($r->amount)))

            // Status badge
            ->addColumn('status_badge', function ($r) {
                $s = strtolower($r->status ?? '');
                if ($s === 'paid')   return '<span class="badge bg-green text-green-fg">Paid</span>';
                if ($s === 'unpaid') return '<span class="badge bg-red text-red-fg">Unpaid</span>';
                return e(ucfirst($r->status ?? ''));
            })

            // Date (preformatted)
            ->editColumn('paid_at_text', fn($r) => e($r->paid_at_text ?? ''))

            // Note
            ->editColumn('note', fn($r) => e($r->note ?? ''))

            // Actions
            ->addColumn('action', function ($r) {
                $edit = route('admin.commission.edit', $r->id);
                $del  = route('admin.commission.destroy', $r->id);

                $h = '';
                if(Auth::user()?->canResource('admin_commissions','edit')){
                    $h .= '<a href="'.$edit.'" class="btn-sm btn-primary me-2 text-decoration-none"><i class="fa-solid fa-pen-to-square fa-lg"></i></a>';
                }
                if(Auth::user()?->canResource('admin_commissions','delete')){
                    $h .= '<a href="'.$del.'" class="text-red delete-item text-decoration-none"><i class="fa-solid fa-trash-can fa-lg"></i></a>';
                }
                return $h ?: '-';
            })

            /* ---------- SEARCH HOOKS ---------- */

            // price / discount / total
            ->filterColumn('course_price', function ($q, $kw) use ($norm) {
                $n = $norm($kw); if ($n === '') return;
                $q->whereHas('payment', fn($p) => $p->where('amount', 'like', "%{$n}%"));
            })
            ->filterColumn('course_discount', function ($q, $kw) use ($norm) {
                $n = $norm($kw); if ($n === '') return;
                $q->whereHas('payment', fn($p) => $p->where('discount', 'like', "%{$n}%"));
            })
            ->filterColumn('course_total', function ($q, $kw) use ($norm) {
                $n = $norm($kw); if ($n === '') return;
                $q->whereHas('payment', fn($p) => $p->where('total', 'like', "%{$n}%"));
            })

            ->filterColumn('payee_cell', function ($q, $kw) {
                $kw = trim($kw);
                $q->where(function ($w) use ($kw) {
                    $w->where('users.company', 'like', "%{$kw}%")
                      ->orWhere('users.name',    'like', "%{$kw}%")
                      ->orWhere('commissions.payee_name', 'like', "%{$kw}%");
                });
            })

            // Role filter – agent/sales come from roles; "external" means commissions.user_id is NULL
            ->filterColumn('role_badge', function ($q, $keyword) {
                $v = strtolower(trim($keyword, '^$ '));
                if ($v === '') return;

                if ($v === 'external') {
                    $q->whereNull('commissions.user_id');
                    return;
                }

                $q->whereHas('user.roles', function ($r) use ($v) {
                    $r->where('roles.name', $v)
                      ->where('user_roles.is_main', 1); // adjust pivot name if different
                });
            })

            ->filterColumn('student_name', function ($q, $kw) {
                $kw = trim($kw);
                $q->whereHas('payment.user', fn($w) => $w->where('name', 'like', "%{$kw}%"));
            })
            ->filterColumn('student_course', function ($q, $kw) {
                $kw = trim($kw);
                $q->where(function ($w) use ($kw) {
                    $w->whereHas('payment.course', fn($c) => $c->where('title','like',"%{$kw}%"))
                      ->orWhereHas('payment.course.level', fn($l) => $l->where('name','like',"%{$kw}%"));
                });
            })
            ->filterColumn('status_badge', function ($q, $kw) {
                $raw = strtolower(trim($kw, '^$ '));
                if ($raw === 'paid' || $raw === 'unpaid') {
                    $q->where('commissions.status', $raw);
                }
            })
            ->filterColumn('paid_at_text', function ($q, $kw) {
                $kw = trim($kw);
                if ($kw !== '') {
                    $q->whereRaw("DATE_FORMAT(commissions.paid_at, '%Y-%m-%d') LIKE ?", ["%{$kw}%"]);
                }
            })

            ->rawColumns(['role_badge','student_course','status_badge','action'])
            ->setRowId('id');

            // Compute total amount for the CURRENT server-side filters (URL params)
            $sum = (clone $query)->sum('commissions.amount');

            // Add both raw and formatted values to the response payload
            return $dt->with([
                'total_amount'       => $sum,
                'total_amount_text'  => currency_format($sum),
            ]);
    }

    public function query(Commission $model): QueryBuilder
    {
        $q = $model->newQuery()
            ->select([
                'commissions.*',
                'users.company as payee_company',
                'users.name as payee_user_name',
                'commissions.payee_name as fallback_payee_name',
                \DB::raw("DATE_FORMAT(commissions.paid_at, '%Y-%m-%d') as paid_at_text"),
            ])
            ->selectSub(function ($q) {
                $q->from('roles')
                ->join('user_roles', 'user_roles.role_id', '=', 'roles.id')
                ->whereColumn('user_roles.user_id', 'commissions.user_id')
                ->where('user_roles.is_main', 1)
                ->select('roles.name')
                ->limit(1);
            }, 'role_name')
            ->leftJoin('users', 'users.id', '=', 'commissions.user_id')
            ->with([
                'payment.user:id,name',
                'payment.course.level',
                'user.roles' => function ($q) { $q->select('roles.id','roles.name'); },
            ]);

        /* ---------- Optional filters from query string ---------- */

        // 1) Period from dashboard: ?commission=today|week|month|year
        $commission = request('commission');
        $commissionFrom = request('commission_from');
        $commissionTo   = request('commission_to');

        if ($commission || $commissionFrom || $commissionTo) {
            $now  = Carbon::now(config('app.timezone'));
            $from = $commissionFrom ? Carbon::parse($commissionFrom)->startOfDay() : null;
            $to   = $commissionTo   ? Carbon::parse($commissionTo)->endOfDay()     : null;

            if ($commission && !$from && !$to) {
                switch ($commission) {
                    case 'today': $from = $now->copy()->startOfDay();  $to = $now->copy()->endOfDay();   break;
                    case 'week':  $from = $now->copy()->startOfWeek(); $to = $now->copy()->endOfWeek();  break;
                    case 'month': $from = $now->copy()->startOfMonth();$to = $now->copy()->endOfMonth(); break;
                    case 'year':  $from = $now->copy()->startOfYear(); $to = $now->copy()->endOfYear();  break;
                }
            }

            if ($from) $q->where('commissions.paid_at', '>=', $from);
            if ($to)   $q->where('commissions.paid_at', '<=', $to);

            // Dashboard intent: paid commissions in the window
            $q->where('commissions.status', 'paid');
        }

        // 2) Optional quick filters via URL
        if ($st = strtolower((string) request('status'))) {
            if (in_array($st, ['paid','unpaid'], true)) {
                $q->where('commissions.status', $st);
            }
        }
        if ($role = strtolower((string) request('role'))) {
            if ($role === 'external') {
                $q->whereNull('commissions.user_id');
            } else {
                $q->whereHas('user.roles', function ($r) use ($role) {
                    $r->where('roles.name', $role)->where('user_roles.is_main', 1);
                });
            }
        }

        return $q;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('commission-table')
            ->columns($this->getColumns())
            ->ajax([
                'url'  => url()->current(),
                'type' => 'GET',
                'data' => 'function(d){
                    const qs = new URLSearchParams(window.location.search);
                    d.commission       = qs.get("commission")       || "";   // today|week|month|year
                    d.commission_from  = qs.get("commission_from")  || "";   // optional YYYY-MM-DD
                    d.commission_to    = qs.get("commission_to")    || "";   // optional YYYY-MM-DD
                    d.role      = qs.get("role")                    || "";   // agent|sales|external (optional)
                    const s = qs.get("status");
                    if (s !== null) d.status = s;  // only send if present in URL
                }',
            ])
            ->pageLength(50)
            ->orderBy(0)
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

                'pagingType' => 'full_numbers',
                'lengthMenu' => [[50,100,500,-1],[50,100,500,'All']],
                'responsive' => false,
                'autoWidth'  => false,
                'processing' => true,
                'serverSide' => true,

                'language' => [
                    'info'         => 'Showing <b>_TOTAL_</b> records',
                    'infoEmpty'    => 'No records',
                    'infoFiltered' => '',
                    'lengthMenu'   => '_MENU_ records per page',
                    'paginate'     => [
                        'first'    => '<i class="fa-solid fa-angles-left"></i>',
                        'previous' => '<i class="fa-solid fa-chevron-left"></i>',
                        'next'     => '<i class="fa-solid fa-chevron-right"></i>',
                        'last'     => '<i class="fa-solid fa-angles-right"></i>',
                    ],
                ],

                'drawCallback' => "function () {
                    var api = this.api();
                    var wrapper = $(api.table().container());
                    wrapper.find('.dataTables_paginate').addClass('mb-0');
                    wrapper.find('.dataTables_paginate .pagination').addClass('pagination-sm');
                    wrapper.find('.top .dataTables_paginate').addClass('ms-2');
                }",

                // send role names to JS
                'roleOptions' => Role::orderBy('name')->pluck('name')->values()->toArray(),

                'initComplete' => <<<'JS'
                    function () {
                      var api = this.api();
                      var init = api.settings()[0].oInit || {};
                      var $wrap = $(api.table().container());

                      // freeze sorting when interacting with header filters
                      $wrap.off('.dtHdrStop')
                           .on('click.dtHdrStop mousedown.dtHdrStop keydown.dtHdrStop',
                               'thead .dt-filter, thead .dt-filter *',
                               function(e){ e.stopPropagation(); });

                      function addText(col){
                        var $inp = $('<input/>', {'class':'form-control form-control-sm mt-2 dt-filter', type:'text'});
                        $(col.header()).append($inp);
                        $inp.on('keyup change', function(){ col.search(this.value).draw(); });
                      }

                      function buildOptions(arr){
                        var html = '<option value="">All</option>';
                        (arr || []).forEach(function(v){ html += '<option value="'+ v +'">'+ v +'</option>'; });
                        return html;
                      }

                      // roles from PHP + extra "external"
                      var roleOptions = (init.roleOptions || []).slice();
                      if (roleOptions.indexOf('external') === -1) roleOptions.push('external');

                      api.columns().every(function(){
                        var column = this;
                        var dataSrc = column.dataSrc();
                        var $head = $(column.header());
                        $head.find('.dt-filter').remove();

                        if (['id','action'].indexOf(dataSrc) !== -1) return;

                        if (dataSrc === 'status_badge') {
                          var $sel = $('<select/>', {'class':'form-select form-select-sm mt-2 dt-filter'})
                            .append('<option value="">All</option>')
                            .append('<option value="^Paid$">Paid</option>')
                            .append('<option value="^Unpaid$">Unpaid</option>');
                          $head.append($sel);
                          $sel.on('change', function(){
                            column.search(this.value, true, false).draw();
                          });
                          return;
                        }

                        if (dataSrc === 'role_badge') {
                          var $selR = $('<select/>', {'class':'form-select form-select-sm mt-2 dt-filter text-capitalize'})
                            .html(buildOptions(roleOptions));
                          $head.append($selR);
                          $selR.on('change', function(){
                            column.search(this.value || '', false, true).draw();
                          });
                          return;
                        }

                        addText(column);
                      });
                    }
                JS,
            ])
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
            Column::make('id')->title('#')->orderable(true)->searchable(false)->width(50),
            Column::computed('payee_cell')->title('Commission To')->orderable(false)->searchable(true),
            Column::computed('role_badge')->title('Role')->orderable(false)->searchable(true)->width(80),
            Column::computed('student_name')->title('Student Name')->orderable(false)->searchable(true),
            Column::computed('student_course')->title('Student Course')->orderable(false)->searchable(true),
            Column::computed('course_price')->title('Course Price')->orderable(false)->searchable(true),
            Column::computed('course_discount')->title('Course Discount')->orderable(false)->searchable(true),
            Column::computed('course_total')->title('Course Total')->orderable(false)->searchable(true),
            Column::make('amount')->title('Commission Amount')->orderable(false)->searchable(true),
            Column::computed('status_badge')->title('Status')->orderable(false)->searchable(true)->width(90),
            Column::make('paid_at_text')->title('Payment Date')->name('commissions.paid_at')->orderable(true)->searchable(true)->width(120),
            Column::make('note')->title('Note')->orderable(false)->searchable(true)->visible(false),
            Column::computed('action')->title('Action')->exportable(false)->printable(false)->orderable(false)->searchable(false)->addClass('text-nowrap')->addClass('no-print'),
        ];
    }

    protected function filename(): string
    {
        return 'Commission_' . date('YmdHis');
    }
}
