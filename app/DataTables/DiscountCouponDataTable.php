<?php

namespace App\DataTables;

use App\Models\DiscountCoupon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class DiscountCouponDataTable extends DataTable
{

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))

            // ===== Cells =====
            ->addColumn('agent_cell', function ($r) {
                if (!$r->agent) return '<span class="text-muted">—</span>';
                $h  = '<div class="text-nowrap">'.e($r->agent->company);
                if ($r->agent->name) $h .= '<br><small class="text-secondary">'.e($r->agent->name).'</small>';
                return $h.'</div>';
            })
            ->editColumn('discount_value', function ($r) {
                $val = (float)$r->discount_value;
                $display = $r->discount_type === 'percent'
                    ? rtrim(rtrim(number_format($val, 2, '.', ''), '0'), '.').'%'  // "12.5%"
                    : number_format($val, 2);                                     // "120.00"
                // Use data-order for correct numeric sorting
                return '<span data-order="'.e($val).'">'.$display.'</span>';
            })
            ->editColumn('min_amount', fn($r) => $r->min_amount !== null ? number_format((float)$r->min_amount, 2) : '—')
            ->editColumn('max_uses', fn($r) => $r->max_uses ?? '∞')
            ->addColumn('used',      fn($r) => (int)($r->usages_count ?? 0))
            ->addColumn('status_badge', function ($r) {
                return $r->status
                    ? '<span class="badge bg-success text-success-fg">Active</span>'
                    : '<span class="badge bg-danger text-danger-fg">Inactive</span>';
            })
            ->addColumn('created_text', fn($r) => $r->created_at ? $r->created_at->format('Y-m-d') : '')

            ->addColumn('action', function ($r) {
                $edit = route('admin.discount-coupon.edit', $r->id);
                $del  = route('admin.discount-coupon.destroy', $r->id);

                $h = '';
                if(Auth::user()?->canResource('admin_discount_coupons','edit')){
                    $h .= '<a href="'.$edit.'" class="btn-sm btn-primary me-2 text-decoration-none"><i class="fa-solid fa-pen-to-square fa-lg"></i></a>';
                }
                if(Auth::user()?->canResource('admin_discount_coupons','delete')){
                    $h .= '<a href="'.$del.'" class="text-red delete-item text-decoration-none"><i class="fa-solid fa-trash-can fa-lg"></i></a>';
                }
                return $h ?: '-';
            })

            // ===== Filters =====
            ->filterColumn('agent_cell', function ($q, $kw) {
                $kw = trim($kw);
                if ($kw === '') return;
                $q->whereHas('agent', function ($qa) use ($kw) {
                    $qa->where('name', 'like', "%{$kw}%")
                       ->orWhere('email','like', "%{$kw}%");
                });
            })
            ->filterColumn('status_badge', function ($q, $kw) {
                $raw = strtolower(trim($kw, '^$ '));
                if ($raw === '') return;
                if (in_array($raw, ['active','1','true','yes'], true))  $q->where('discount_coupons.active', 1);
                if (in_array($raw, ['inactive','0','false','no'], true)) $q->where('discount_coupons.active', 0);
            })
            ->filterColumn('discount_type', function ($q, $kw) {
                $kw = strtolower(trim($kw));
                if ($kw === '') return;
                if (in_array($kw, ['percent','fixed'], true)) $q->where('discount_coupons.discount_type', $kw);
            })

            ->rawColumns(['agent_cell','discount_value','status_badge','action'])
            ->setRowId('id');
    }

    public function query(DiscountCoupon $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['agent:id,name,company'])
            ->withCount('usages')
            ->select('discount_coupons.*');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('discount-coupon-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(8, 'desc') // created_at
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
                'initComplete' => <<<'JS'
                    function () {
                      var api = this.api();
                      var $wrap = $(api.table().container());

                      // stop header-sort when interacting with filters
                      $wrap.off('.dtHdrStop')
                           .on('click.dtHdrStop mousedown.dtHdrStop keydown.dtHdrStop', 'thead .dt-filter, thead .dt-filter *', function(e){ e.stopPropagation(); });

                      function addInput($head, column){
                        var $inp = $('<input/>', {'class':'form-control form-control-sm mt-2 dt-filter', type:'text'});
                        $head.append($inp);
                        $inp.on('keyup change', function(){ column.search(this.value).draw(); });
                      }

                      api.columns().every(function(){
                        var column = this, dataSrc = column.dataSrc(), $head = $(column.header());
                        if (['action','id'].indexOf(dataSrc) !== -1) return;

                        $head.find('.dt-filter').remove();

                        if (dataSrc === 'discount_type') {
                          var $sel = $('<select/>', {'class':'form-select form-select-sm mt-2 dt-filter'})
                                     .append('<option value="">All</option>')
                                     .append('<option value="percent">Percent</option>')
                                     .append('<option value="fixed">Fixed</option>');
                          $head.append($sel);
                          $sel.on('change', function(){ column.search(this.value, false, true).draw(); });
                          return;
                        }

                        if (dataSrc === 'status_badge') {
                          var $sel = $('<select/>', {'class':'form-select form-select-sm mt-2 dt-filter'})
                                     .append('<option value="">All</option>')
                                     .append('<option value="Active">Active</option>')
                                     .append('<option value="Inactive">Inactive</option>');
                          $head.append($sel);
                          $sel.on('change', function(){ column.search(this.value, false, true).draw(); });
                          return;
                        }

                        addInput($head, column);
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
                            'header' => 'function (data, idx) {
                                var $h = $("<div>").html(data);
                                $h.find(".dt-filter").remove();
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
                    ->customize('function (win) {
                        $(win.document.head).append("<style>.dt-filter{display:none !important}</style>");
                    }'),
            ]);
    }


    public function getColumns(): array
    {
        return [
            Column::make('id')->title('#')->name('discount_coupons.id')->width(50),
            Column::make('code')->title('Code')->name('discount_coupons.code'),
            Column::computed('agent_cell')->title('Agent')->orderable(false)->searchable(true),
            Column::make('discount_type')->title('Type')->name('discount_coupons.discount_type'),
            Column::make('discount_value')->title('Value')->name('discount_coupons.discount_value'),
            Column::make('min_amount')->title('Min Amount')->name('discount_coupons.min_amount'),
            Column::make('max_uses')->title('Max Uses')->name('discount_coupons.max_uses'),
            Column::computed('used')->title('Used')->name('usages_count')->searchable(false),
            Column::computed('status_badge')->title('Status')->orderable(false)->searchable(true),
            Column::computed('created_text')->title('Created')->orderable(true)->searchable(false),
            Column::computed('action')->title('Action')->exportable(false)->printable(false)->orderable(false)->searchable(false)->addClass('text-nowrap no-print'),
        ];
    }

    protected function filename(): string
    {
        return 'DiscountCoupon_' . date('YmdHis');
    }

}
