<?php

namespace App\DataTables;

use App\Models\Expense;
use App\Models\ExpenseCategory;
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

class ExpenseDataTable extends DataTable
{

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()

            ->editColumn('amount', fn($r) => \currency_format($r->amount))
            ->editColumn('transaction_fee', fn($r) => \currency_format($r->transaction_fee))

            // date_text is already formatted by SQL
            ->editColumn('date_text', fn($r) => $r->date_text ?? '')

            ->editColumn('status', function ($r) {
                $isPaid = is_numeric($r->status)
                    ? (int)$r->status === 1
                    : strtolower((string)$r->status) === 'paid';
                return '<span class="badge bg-'.($isPaid ? 'success' : 'danger').' text-'.($isPaid ? 'success' : 'danger').'-fg">'
                    .($isPaid ? 'Paid' : 'Unpaid').'</span>';
            })
            ->filterColumn('status', function ($q, $keyword) {
                $raw   = trim($keyword);
                $kw    = strtolower($raw);

                // Accept anchors from regex searches ^paid$ etc.
                if (preg_match('/^\^?paid\$/i', $raw))   { $kw = 'paid'; }
                if (preg_match('/^\^?unpaid\$/i', $raw)) { $kw = 'unpaid'; }

                if ($kw === 'paid') {
                    // support string or numeric storage
                    $q->where(function ($w) {
                        $w->whereIn('expenses.status', ['paid', 'Paid', 1, '1', true]);
                    });
                } elseif ($kw === 'unpaid') {
                    $q->where(function ($w) {
                        $w->whereIn('expenses.status', ['unpaid', 'Unpaid', 0, '0', false]);
                    });
                } else {
                    // fallback to normal LIKE if user types something else
                    $q->where('expenses.status', 'like', "%{$raw}%");
                }
            })

            ->addColumn('action', function ($row) {
                $btns = '';

                if(Auth::user()?->canResource('admin_expenses','edit')){
                    $btns .= '<a href="'.route('admin.expense.edit', $row->id).'"
                               class="btn-sm btn-primary me-2 text-decoration-none">
                               <i class="fa-solid fa-pen-to-square fa-lg"></i></a>';
                }
                if(Auth::user()?->canResource('admin_expenses','delete')){
                    $btns .= '<a href="'.route('admin.expense.destroy', $row->id).'"
                               class="text-red delete-item text-decoration-none">
                               <i class="fa-solid fa-trash-can fa-lg"></i></a>';
                }
                return $btns;
            })

            // search the formatted date as text (dd-mm-YYYY)
            ->filterColumn('date_text', function ($q, $keyword) {
                $q->havingRaw('date_text LIKE ?', ["%{$keyword}%"]);
            })
            ->orderColumn('date_text', fn ($q, $dir) => $q->orderBy('expenses.expense_date', $dir))

            ->rawColumns(['status','action'])
            ->setRowId('id');
    }

    public function query(Expense $model): QueryBuilder
    {
        return $model->newQuery()
            ->leftJoin('expense_categories as ec', 'ec.id', '=', 'expenses.expense_category_id')
            ->leftJoin('users as u', 'u.id', '=', 'expenses.user_id')
            ->select([
                'expenses.id',
                'expenses.amount',
                'expenses.transaction_fee',
                'expenses.status',
                'expenses.note',
                'expenses.expense_date',              
                'ec.name as category_name',
                'u.name as user_name',
                DB::raw("DATE_FORMAT(expenses.expense_date, '%Y-%m-%d') AS date_text"),
            ]);
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('expense-table')
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

                        'categoryOptions' => ExpenseCategory::orderBy('name')->pluck('name')->values()->toArray(),

                        'initComplete' => <<<'JS'
                            function () {
                            var api = this.api();

                            // read options we injected from PHP (if any)
                            var init = api.settings()[0].oInit || {};
                            var categoryOptions = init.categoryOptions || [];

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
                                // Paid / Unpaid exact match via regex
                                var $sel = $('<select/>', {
                                    'class': 'form-select mt-2 dt-filter'
                                })
                                .append($('<option/>', {value:'',        text:'All'}))
                                .append($('<option/>', {value:'^paid$',  text:'Paid'}))
                                .append($('<option/>', {value:'^unpaid$',text:'Unpaid'}));

                                $head.append($sel);
                                $sel.on('change', function () {
                                    column.search(this.value, true, false).draw(); // regex=true, smart=false
                                });

                                } else if (title === 'Category') {
                                // Build a select of categories
                                var $cat = $('<select/>', {
                                    'class': 'form-select mt-2 dt-filter'
                                }).append($('<option/>', {value:'', text:'All'}));

                                // Preferred: use the list from PHP so it covers all rows
                                if (categoryOptions.length) {
                                    categoryOptions.forEach(function (name) {
                                    $cat.append($('<option/>', { value: '^'+name+'$', text: name }));
                                    });
                                } else {
                                    // Fallback: unique values from this page only (serverSide limitation)
                                    column.data().unique().sort().each(function (d) {
                                    var txt = $('<div>').html(d).text();   // strip any HTML
                                    $cat.append($('<option/>', { value: '^'+txt+'$', text: txt }));
                                    });
                                }

                                $head.append($cat);
                                $cat.on('change', function () {
                                    column.search(this.value, true, false).draw(); // exact via regex
                                });

                                } else {
                                // default text input for other columns
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
            Column::make('category_name')->title('Category')->name('ec.name')->orderable(false)->searchable(true)->width(160),
            Column::make('user_name')->title('Name')->name('u.name')->orderable(false)->searchable(true),
            Column::make('amount')->title('Amount')->orderable(true)->searchable(true),
            Column::make('transaction_fee')->title('Transaction Fee')->orderable(true)->searchable(true),
            Column::make('date_text')->title('Date')->name('expenses.expense_date')->orderable(true)->searchable(true),
            Column::make('status')->title('Status')->name('expenses.status')->orderable(false)->searchable(true)->width(100),
            Column::make('note')->title('Note')->orderable(false)->searchable(true),
            Column::computed('action')->title('Action')->exportable(false)->printable(false)->addClass('text-nowrap')->addClass('no-print')->width(50),
        ];
    }

    protected function filename(): string
    {
        return 'Expense_' . date('YmdHis');
    }

}
