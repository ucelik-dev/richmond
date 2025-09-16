<?php

namespace App\DataTables;

use App\Models\Income;
use App\Models\IncomeCategory;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class IncomeDataTable extends DataTable
{

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            // nice display columns
            ->addColumn('amount_text', fn($r) => currency_format($r->amount))
            ->addColumn('date_text',   fn($r) => $r->income_date ? \Carbon\Carbon::parse($r->income_date)->format('Y-m-d') : '')
            ->addColumn('status_badge', function ($r) {
                return $r->status === 'paid'
                    ? '<span class="badge bg-green text-green-fg">Paid</span>'
                    : '<span class="badge bg-red text-red-fg">Unpaid</span>';
            })

            // actions
            ->addColumn('action', function ($r) {
                $edit = route('admin.income.edit', $r->id);
                $del  = route('admin.income.destroy', $r->id);

                $html = '';
                if (Auth::user()->can('edit_admin_incomes')) {
                    $html .= '<a href="'.$edit.'" class="btn-sm btn-primary me-2 text-decoration-none">
                                <i class="fa-solid fa-pen-to-square fa-lg"></i>
                              </a>';
                }
                if (Auth::user()->can('delete_admin_incomes')) {
                    $html .= '<a href="'.$del.'" class="text-red delete-item text-decoration-none">
                                <i class="fa-solid fa-trash-can fa-lg"></i>
                              </a>';
                }
                return $html ?: '-';
            })

            // filters mapped to real DB fields
            ->filterColumn('category_name', function ($q, $kw) {
                $kw = trim($kw, '^$ ');
                if ($kw !== '') {
                    $q->whereRaw('LOWER(income_categories.name) = ?', [strtolower($kw)]);
                }
            })
            ->filterColumn('status_badge', function ($q, $kw) {
                $raw = strtolower(trim($kw, '^$ '));
                if ($raw === '') return;
                if (in_array($raw, ['paid','1','yes','true'], true))   $q->where('incomes.status', 'paid');
                if (in_array($raw, ['unpaid','0','no','false'], true)) $q->where('incomes.status', 'unpaid');
            })
            ->filterColumn('amount_text', function ($q, $kw) {
                // search by numeric amount regardless of currency formatting
                $n = preg_replace('/[^\d.]/', '', (string)$kw);
                if ($n !== '') $q->where('incomes.amount', 'like', "%{$n}%");
            })
            ->filterColumn('date_text', function ($q, $kw) {
                $kw = trim($kw);
                if ($kw !== '') {
                    $q->whereRaw("DATE_FORMAT(incomes.income_date, '%Y-%m-%d') LIKE ?", ["%{$kw}%"]);
                }
            })

            ->rawColumns(['status_badge','action'])
            ->setRowId('id');
    }

    public function query(Income $model): QueryBuilder
    {
        return $model->newQuery()
            ->select([
                'incomes.id','incomes.income_category_id','incomes.amount','incomes.income_date',
                'incomes.status','incomes.note',
                'income_categories.name as category_name',
            ])
            ->leftJoin('income_categories','income_categories.id','=','incomes.income_category_id');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('income-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(3, 'desc') // by date_text
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

                // header selects
                'categoryOptions' => IncomeCategory::orderBy('name')->pluck('name')->values()->toArray(),
                'statusOptions'   => ['Paid','Unpaid'],

                'initComplete' => <<<'JS'
                    function () {
                      var api = this.api();
                      var init = api.settings()[0].oInit || {};
                      var categoryOptions = init.categoryOptions || [];
                      var statusOptions   = init.statusOptions   || ['Paid','Unpaid'];

                      function buildOptions(arr){
                        var html = '<option value="">All</option>';
                        (arr || []).forEach(function(v){ html += '<option value="'+v+'">'+v+'</option>'; });
                        return html;
                      }

                      var $wrap = $(api.table().container());
                      $wrap
                        .off('.dtHdrStop')
                        .on('click.dtHdrStop mousedown.dtHdrStop keydown.dtHdrStop', 'thead .dt-filter, thead .dt-filter *', function(e){ e.stopPropagation(); });

                      api.columns().every(function(){
                        var column = this;
                        var dataSrc = column.dataSrc();
                        var $head  = $(column.header());
                        $head.find('.dt-filter').remove();

                        if (dataSrc === 'category_name') {
                          var $c = $('<select/>', {'class':'form-select form-select-sm mt-2 dt-filter'})
                                   .html(buildOptions(categoryOptions));
                          $head.append($c);
                          $c.on('change', function(){
                            var v = this.value;
                            var rx = v ? '^' + $.fn.dataTable.util.escapeRegex(v) + '$' : '';
                            column.search(rx, true, false).draw();
                          });
                          return;
                        }

                        if (dataSrc === 'status_badge') {
                          var $s = $('<select/>', {'class':'form-select form-select-sm mt-2 dt-filter'})
                                   .html(buildOptions(statusOptions));
                          $head.append($s);
                          $s.on('change', function(){
                            var v = this.value;
                            column.search(v, false, true).draw();
                          });
                          return;
                        }

                        // default text input
                        if (['action','id'].indexOf(dataSrc) !== -1) return;

                        var $inp = $('<input/>', {'class':'form-control form-control-sm mt-2 dt-filter', type:'text'});
                        $head.append($inp);
                        $inp.on('keyup change', function(){ column.search(this.value).draw(); });
                      });
                    }
                JS,
            ])
            ->buttons([
                Button::make('colvis')->className('btn btn-primary py-1 px-2'),
                Button::make('excel')->className('btn btn-primary py-1 px-2'),
                Button::make('print')->className('btn btn-primary py-1 px-2'),
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('id')->title('#')->name('incomes.id')->width(50),
            Column::make('category_name')->title('Category')->name('income_categories.name')->orderable(false)->searchable(true),
            Column::computed('amount_text')->title('Amount')->orderable(false)->searchable(true),
            Column::computed('date_text')->title('Date')->orderable(true)->searchable(true),
            Column::computed('status_badge')->title('Status')->orderable(false)->searchable(true),
            Column::make('note')->title('Note')->name('incomes.note')->orderable(false)->searchable(true),
            Column::computed('action')->title('Action')->exportable(false)->printable(false)->orderable(false)->searchable(false)->addClass('text-nowrap'),
        ];
    }

    protected function filename(): string
    {
        return 'Income_' . date('YmdHis');
    }

}
