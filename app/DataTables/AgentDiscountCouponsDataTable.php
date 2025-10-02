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

class AgentDiscountCouponsDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('code_cell', function ($r) {
                // Code with a small copy button
                $code = e($r->code);
                return <<<HTML
                    <div class="d-inline-flex align-items-center gap-2">
                        <span class="fw-semibold">$code</span>
                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1 copy-code" data-code="$code" title="Copy">
                            <i class="fa-regular fa-copy"></i>
                        </button>
                    </div>
                HTML;
            })
            ->editColumn('discount_value', function ($r) {
                $val = (float)$r->discount_value;
                $display = $r->discount_type === 'percent'
                    ? rtrim(rtrim(number_format($val, 2, '.', ''), '0'), '.') . '%'
                    : number_format($val, 2);
                return '<span data-order="'.e($val).'">'.$display.'</span>';
            })
            ->editColumn('min_amount', fn($r) => $r->min_amount !== null ? number_format((float)$r->min_amount, 2) : '—')
            ->editColumn('max_uses', fn($r) => $r->max_uses ?? '∞')
            ->addColumn('used',      fn($r) => (int)($r->usages_count ?? 0))
            ->addColumn('status_badge', fn($r) => $r->status
                ? '<span class="badge bg-success text-success-fg">Active</span>'
                : '<span class="badge bg-danger text-danger-fg">Inactive</span>')
            ->addColumn('created_text', fn($r) => $r->created_at?->format('Y-m-d') ?? '')

            // Filters
            ->filterColumn('status_badge', function ($q, $kw) {
                $v = strtolower(trim($kw));
                if ($v === 'active')   $q->where('discount_coupons.active', 1);
                if ($v === 'inactive') $q->where('discount_coupons.active', 0);
            })
            ->filterColumn('discount_type', function ($q, $kw) {
                $v = strtolower(trim($kw));
                if (in_array($v, ['percent','fixed'], true)) {
                    $q->where('discount_coupons.discount_type', $v);
                }
            })

            ->rawColumns(['code_cell','discount_value','status_badge'])
            ->setRowId('id');
    }

    public function query(DiscountCoupon $model): QueryBuilder
    {
        // Only this agent's coupons
        $agentId = Auth::user()->id;

        return $model->newQuery()
            ->where('agent_id', $agentId)
            ->withCount('usages')
            ->select('discount_coupons.*');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('agent-coupon-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0, 'desc') // created_at col index
            ->pageLength(25)
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
                'lengthMenu' => [[25,50,100,-1],[25,50,100,'All']],
                'responsive' => false,
                'autoWidth'  => false,
                'processing' => true,
                'serverSide' => true,
                'language' => [
                    'info'       => 'Showing <b>_TOTAL_</b> coupons',
                    'infoEmpty'  => 'No coupons',
                    'lengthMenu' => '_MENU_ per page',
                    'paginate'   => [
                        'first'    => '<i class="fa-solid fa-angles-left"></i>',
                        'previous' => '<i class="fa-solid fa-chevron-left"></i>',
                        'next'     => '<i class="fa-solid fa-chevron-right"></i>',
                        'last'     => '<i class="fa-solid fa-angles-right"></i>',
                    ],
                ],
                'initComplete' => <<<'JS'
                    function () {
                      var api = this.api();
                      var $wrap = $(api.table().container());
                      $wrap.off('.dtHdrStop')
                           .on('click.dtHdrStop mousedown.dtHdrStop keydown.dtHdrStop', 'thead .dt-filter, thead .dt-filter *', function(e){ e.stopPropagation(); });

                      function addInput($head, column){
                        var $inp = $('<input/>', {'class':'form-control form-control-sm mt-2 dt-filter', type:'text'});
                        $head.append($inp);
                        $inp.on('keyup change', function(){ column.search(this.value).draw(); });
                      }

                      api.columns().every(function(){
                        var column = this, dataSrc = column.dataSrc(), $head = $(column.header());
                        if (['DT_RowIndex'].indexOf(dataSrc) !== -1) return;

                        $head.find('.dt-filter').remove();

                        if (dataSrc === 'discount_type') {
                          var $sel = $('<select/>', {'class':'form-select form-select-sm mt-2 dt-filter'})
                                     .append('<option value="">All</option>')
                                     .append('<option value="percent">Percent</option>')
                                     .append('<option value="fixed">Fixed</option>');
                          $head.append($sel).on('change', 'select', function(){ column.search(this.value, false, true).draw(); });
                          return;
                        }

                        if (dataSrc === 'status_badge') {
                          var $sel = $('<select/>', {'class':'form-select form-select-sm mt-2 dt-filter'})
                                     .append('<option value="">All</option>')
                                     .append('<option value="Active">Active</option>')
                                     .append('<option value="Inactive">Inactive</option>');
                          $head.append($sel).on('change', 'select', function(){ column.search(this.value, false, true).draw(); });
                          return;
                        }

                        addInput($head, column);
                      });

                      // copy button
                      $wrap.on('click', '.copy-code', function(){
                        var code = $(this).data('code');
                        navigator.clipboard.writeText(code).then(function(){
                          // optional toast could go here
                        });
                      });
                    }
                JS,
            ])
            ->buttons([
                Button::make('colvis')->className('btn btn-primary py-1 px-2'),
                Button::make('excel')->className('btn btn-primary py-1 px-2')
                    ->exportOptions([
                        'columns'   => ':visible',
                        'stripHtml' => true,
                        'format'    => [
                            'header' => 'function (data) { var $h = $("<div>").html(data); $h.find(".dt-filter").remove(); return $.trim($h.text()); }',
                        ],
                    ]),
                Button::make('print')->className('btn btn-primary py-1 px-2')
                    ->exportOptions([
                        'columns'   => ':visible',
                        'stripHtml' => true,
                        'format'    => [
                            'header' => 'function (data) { var $h = $("<div>").html(data); $h.find(".dt-filter").remove(); return $.trim($h.text()); }',
                        ],
                    ]),
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')->title('#')->width(25)->orderable(false)->searchable(false),
            Column::computed('code_cell')->title('Code')->orderable(false)->searchable(true),
            Column::make('discount_type')->title('Type')->name('discount_coupons.discount_type'),
            Column::make('discount_value')->title('Value')->name('discount_coupons.discount_value'),
            Column::make('min_amount')->title('Min Amount')->name('discount_coupons.min_amount'),
            Column::make('max_uses')->title('Max Uses')->name('discount_coupons.max_uses'),
            Column::computed('used')->title('Used')->name('usages_count')->searchable(false),
            Column::computed('status_badge')->title('Status')->orderable(false)->searchable(true),
            Column::computed('created_text')->title('Created')->orderable(true)->searchable(false),
        ];
    }

    protected function filename(): string
    {
        return 'Agent_Discount_Coupons_' . date('YmdHis');
    }
}
