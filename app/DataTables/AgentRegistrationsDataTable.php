<?php

namespace App\DataTables;

use App\Models\AgentRegistration;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Str;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class AgentRegistrationsDataTable extends DataTable
{

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $agentId = Auth::user()->id;

        return (new EloquentDataTable($query))
            ->addIndexColumn() // -> DT_RowIndex for "#"
            // Courses (badges)
            ->addColumn('courses', function ($u) {
                if (!$u->relationLoaded('enrollments')) return '';
                return $u->enrollments->map(function ($en) {
                    $title = $en->course?->title;
                    $level = $en->course?->level?->name;
                    if (!$title) return '';
                    return '<span class="badge bg-secondary text-secondary-fg mb-1" style="font-size:14px">' .
                           e($title . ($level ? " ({$level})" : '')) . '</span>';
                })->implode('<br>');
            })
            // Commission cell
            ->addColumn('commission_cell', function ($u) {
                $rows = $u->agentCommissions ?? collect();

                if ($rows->isEmpty()) {
                    return '<span class="text-warning">No commissions</span>';
                }

                return $rows->map(function ($c) {
                    $amt   = currency_format($c->amount);
                    //$date  = $c->paid_at ? Carbon::parse($c->paid_at)->format('d-m-Y') : '';
                    $badge = $c->status === 'paid'
                            ? '<span class="badge bg-success" style="font-size:14px">'.$amt.'</span>'
                            : '<span class="badge bg-danger" style="font-size:14px">'.$amt.'</span>';

                    return '<div class="text-nowrap">'
                        . $badge
                        //.     '<div class="badge bg-light text-light-fg" style="font-size:14px">' . e($date) . '</div>'
                        . '</div>';
                })->implode('<hr class="my-1 border-0">'); // thin divider between lines
            })

            ->editColumn('registered_text', fn ($u) => $u->registered_text ?: '—')

            ->filterColumn('registered_text', function ($q, $kw) {
                $kw = trim($kw);
                if ($kw === '') return;
                $q->whereRaw("DATE_FORMAT(users.created_at, '%Y-%m-%d') LIKE ?", ["%{$kw}%"]);
            })

            ->addColumn('note', function ($u) {
                $note = optional($u->agentCommissions->first())->note;
                return $note ? e(Str::limit($note, 200)) : '';
            })

            // Column filters
            ->filterColumn('country_name', fn($q,$kw) => $kw !== '' ? $q->where('countries.name','like','%'.trim($kw).'%') : null)
            ->filterColumn('name',         fn($q,$kw) => $kw !== '' ? $q->where('users.name','like','%'.trim($kw).'%') : null)
            ->filterColumn('email',        fn($q,$kw) => $kw !== '' ? $q->where('users.email','like','%'.trim($kw).'%') : null)
            ->filterColumn('phone',        fn($q,$kw) => $kw !== '' ? $q->where('users.phone','like','%'.trim($kw).'%') : null)
            ->filterColumn('note', function ($q, $kw) use ($agentId) {
                $kw = trim($kw);
                if ($kw === '') return;

                $q->whereExists(function ($sub) use ($kw, $agentId) {
                    $sub->selectRaw('1')
                        ->from('commissions')
                        ->join('payments','payments.id','=','commissions.payment_id')
                        ->whereColumn('payments.user_id','users.id')
                        ->where('commissions.user_id', $agentId)
                        ->where('commissions.note', 'like', "%{$kw}%");
                });
            })
            ->filterColumn('courses', function ($q, $kw) {
                $kw = trim($kw);
                if ($kw === '') return;

                $q->whereHas('enrollments', function ($en) use ($kw) {
                    $en->whereHas('course', function ($c) use ($kw) {
                        $c->where('courses.title', 'like', "%{$kw}%")
                        ->orWhereHas('level', function ($l) use ($kw) {
                            $l->where('name', 'like', "%{$kw}%");
                        });
                    });
                });
            })

            ->filterColumn('commission_cell', function ($q, $kw) use ($agentId) {
                // keep only digits and dot (so "£370.00" → "370.00")
                $digits = preg_replace('/\D+/', '', (string) $kw);
                if ($digits === '') return;

                // match exact amount for any commission of this agent for that student
                $q->whereExists(function ($sub) use ($agentId, $digits) {
                    $sub->selectRaw('1')
                        ->from('commissions as c')
                        ->join('payments as p', 'p.id', '=', 'c.payment_id')
                        ->whereColumn('p.user_id', 'users.id')
                        ->where('c.user_id', $agentId)
                        ->whereRaw("REPLACE(CAST(c.amount AS CHAR), '.', '') LIKE ?", ['%' . $digits . '%']);
                });
            })

            ->rawColumns(['courses','commission_cell'])
            ->setRowId('id');
    }

    public function query(User $model): QueryBuilder
    {
        $agentId = Auth::id();

        $q = $model->newQuery()
            ->where('users.agent_id', $agentId)
            ->whereHas('roles', fn($w) => $w->where('name','student')->where('user_roles.is_main',1))
            ->leftJoin('countries', 'countries.id', '=', 'users.country_id')
            ->select([
                'users.id','users.name','users.email','users.phone','users.created_at',
                'countries.name as country_name',
                DB::raw("DATE_FORMAT(users.created_at, '%Y-%m-%d') as registered_text"),
            ])
            // … your selectSub pieces for commissions …
            ->selectSub(function ($q) use ($agentId) {
                $q->from('commissions as c')
                ->join('payments as p','p.id','=','c.payment_id')
                ->selectRaw('MAX(c.id)')
                ->where('c.user_id',$agentId)
                ->whereColumn('p.user_id','users.id');
            }, 'last_commission_id')
            ->selectSub(function ($q) use ($agentId) {
                $q->from('commissions as c')
                ->join('payments as p','p.id','=','c.payment_id')
                ->select('c.status')
                ->where('c.user_id',$agentId)
                ->whereColumn('p.user_id','users.id')
                ->orderByDesc('c.id')->limit(1);
            }, 'last_commission_status');

        // ✅ Only apply default ordering when there is no client-side order request
        $orderReq = request()->input('order', []);
        if (empty($orderReq)) {
            $q->orderByRaw("(last_commission_status = 'unpaid') DESC")
            ->orderByDesc('last_commission_id')
            ->orderByDesc('users.id');
        }

        return $q->with([
            'enrollments.course.level',
            'agentCommissions' => fn($c) => $c->select(
                'commissions.id','commissions.payment_id','commissions.user_id',
                'commissions.amount','commissions.status',
                'commissions.created_at','commissions.paid_at','commissions.note'
            )->where('commissions.user_id',$agentId)->latest(),
        ]);
    }


    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('agent-registrations-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->pageLength(25)
            ->parameters([
                'order' => [],
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
                'lengthMenu' => [[25, 50, 100, -1], [25, 50, 100, 'All']],
                'responsive' => false,
                'autoWidth'  => false,
                'processing' => true,
                'serverSide' => true,
                'language'   => [
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
                'initComplete' => <<<'JS'
                    function () {
                      var api = this.api();
                      api.columns().every(function () {
                        var column  = this;
                        var dataSrc = column.dataSrc();
                        var $head   = $(column.header());
                        // filter for text columns
                        if (['id'].indexOf(dataSrc) !== -1) return;
                        var $input = $('<input type="text" class="form-control form-control-sm mt-2 dt-filter" placeholder="">');
                        $head.append($input);
                        $input.on('keyup change', function(){
                          column.search(this.value).draw();
                        });
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
            //Column::computed('DT_RowIndex')->title('#')->width(30)->orderable(false)->searchable(false),
            Column::make('name')->title('Name')->orderable(true)->searchable(true),
            Column::make('email')->title('Email')->orderable(false)->searchable(true),
            Column::make('phone')->title('Phone')->orderable(false)->searchable(true),
            Column::make('country_name')->title('Country')->name('countries.name')->orderable(false)->searchable(true),
            Column::make('registered_text')->title('Registered')->name('users.created_at')->orderable(true)->searchable(true)->addClass('text-nowrap'),
            Column::computed('courses')->title('Course')->orderable(false)->searchable(true),
            Column::computed('commission_cell')->title('Commission')->orderable(false)->searchable(true)->addClass('text-nowrap'),
            
            Column::make('note')->title('Note')->orderable(false)->searchable(true)->visible(false),
        ];
    }

    protected function filename(): string
    {
        return 'AgentRegistrations_' . date('YmdHis');
    }

}
