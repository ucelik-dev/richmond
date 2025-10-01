<?php

namespace App\DataTables;

use App\Models\User;
use App\Models\UserStatus;
use DB;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class InstructorStudentsDataTable extends DataTable
{

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))

            // COURSES (title + level)
            ->addColumn('courses', function ($u) {
                if (!$u->relationLoaded('enrollments')) return '';
                return $u->enrollments->map(function ($en) {
                    $title = $en->course?->title ?? null;
                    $level = $en->course?->level?->name ?? null;
                    if (!$title) return '';
                    return '<span class="badge bg-secondary text-secondary-fg mb-1">'
                         . e($title . ($level ? " ({$level})" : '')) . '</span>';
                })->implode('<br>');
            })

            // GROUPS
            ->addColumn('groups', function ($u) {
                if (!$u->relationLoaded('enrollments')) return '';
                return $u->enrollments->filter(fn($en) => $en->group)->map(function ($en) {
                    $g = $en->group;
                    $name = str_replace('_', ' ', $g->name ?? '');
                    $color = $g->color ?: 'secondary';
                    return '<span class="badge bg-'.e($color).' text-'.e($color).'-fg mb-1">'.e($name).'</span>';
                })->implode('<br>');
            })

            // BATCHES
            ->addColumn('batches', function ($u) {
                if (!$u->relationLoaded('enrollments')) return '';
                return $u->enrollments->filter(fn($en) => $en->batch)->map(function ($en) {
                    $b = $en->batch;
                    $name  = str_replace('_', ' ', $b->name ?? '');
                    $color = $b->color ?: 'secondary';
                    return '<span class="badge bg-'.e($color).' text-'.e($color).'-fg mb-1">'.e($name).'</span>';
                })->implode('<br>');
            })

            ->addColumn('country', fn($u) => e($u->country?->name ?? '—'))

            ->filterColumn('country', function ($q, $kw) {
                $kw = trim($kw);
                if ($kw !== '') {
                    $q->where('countries.name', 'like', "%{$kw}%");
                }
            })

            ->editColumn('registered_text', fn($r) => e($r->registered_text ?? ''))

            ->filterColumn('registered_text', function ($q, $kw) {
                $q->whereRaw("DATE_FORMAT(users.created_at, '%Y-%m-%d') LIKE ?", ["%".trim($kw)."%"]);
            })

            // AWARDING BODY block (or payment gate)
            ->addColumn('awarding_body_col', function ($u) {
                if ($u->relationLoaded('awardingBodyRegistrations') &&
                    $u->awardingBodyRegistrations->isNotEmpty()) {
                    return $u->awardingBodyRegistrations->map(function ($reg) {
                        $name   = e($reg->awardingBody?->name ?? '—');
                        $level  = e($reg->registrationLevel?->name ?? '—');
                        $number = e($reg->awarding_body_registration_number ?? '—');
                        $date   = e($reg->awarding_body_registration_date ?? '—');

                        return '<div class="text-secondary">
                                  <span class="d-inline-block fw-bold me-2" style="width:50px;">Name</span><span>: '.$name.'</span><br>
                                  <span class="d-inline-block fw-bold me-2" style="width:50px;">Level</span><span>: '.$level.'</span><br>
                                  <span class="d-inline-block fw-bold me-2" style="width:50px;">Number</span><span>: '.$number.'</span><br>
                                  <span class="d-inline-block fw-bold me-2" style="width:50px;">Date</span><span>: '.$date.'</span>
                                  <hr class="m-1">
                                </div>';
                    })->implode('');
                }

                // fallback based on payments
                $paid = $u->payments?->flatMap->installments
                        ->where('status','paid')->sum('amount') ?? 0;

                return $paid >= 1000
                    ? '<span class="badge bg-yellow text-yellow-fg">To be registered</span>'
                    : '<span class="badge bg-danger text-danger-fg">Insufficient payment</span>';
            })

             // ACCOUNT STATUS badge
            ->addColumn('account_status', function ($row) {
                return (int) $row->account_status === 1
                    ? '<span class="badge bg-green text-green-fg">Active</span>'
                    : '<span class="badge bg-red text-red-fg">Inactive</span>';
            })

            // ACCOUNT STATUS
            ->editColumn('account_status', function ($row) {
                if ((int)$row->account_status === 1) {
                    return '<span class="badge bg-green text-green-fg">Active</span>';
                }
                return '<span class="badge bg-red text-red-fg">Inactive</span>';
            })

            // USER STATUS badge
            ->addColumn('user_status', function ($row) {
                $name  = $row->user_status_name ?? '—';
                $color = $row->user_status_color ?? 'secondary';
                return '<span class="badge bg-'.$color.' text-'.$color.'-fg">'.ucwords($name).'</span>';
            })

            ->filterColumn('account_status', function ($q, $keyword) {
                $raw = trim((string)$keyword, '^$ ');
                $kw  = strtolower($raw);

                if ($kw === 'active' || $raw === '1') {
                    $q->where('users.account_status', 1);
                } elseif ($kw === 'inactive' || $raw === '0') {
                    $q->where('users.account_status', 0);
                }
            })
            ->filterColumn('user_status', function ($q, $keyword) {
                $kw = trim($keyword, '^$ ');
                if ($kw !== '') {
                    $q->whereRaw('LOWER(user_statuses.name) = ?', [strtolower($kw)]);
                    // (or partial match) ->where('user_statuses.name','like',"%{$kw}%")
                }
            })

            ->rawColumns(['name_block','courses','groups','batches','awarding_body_col','account_status','user_status'])
            ->setRowId('id');
    }

    public function query(User $model): QueryBuilder
    {
        $instructorId = Auth::user()->id;

        return $model->newQuery()
            ->whereHas('roles', function ($q) {
                $q->where('roles.name','student')->where('user_roles.is_main',1);
            })
            ->whereHas('enrollments.group', function ($q) use ($instructorId) {
                $q->where('instructor_id', $instructorId);
            })
            ->leftJoin('user_statuses','user_statuses.id','=','users.user_status_id')
            ->leftJoin('countries','countries.id','=','users.country_id')
            ->select([
                'users.id','users.name','users.email','users.phone',
                'users.country_id','users.account_status','users.user_status_id',
                'user_statuses.name  as user_status_name',   
                'user_statuses.color as user_status_color',  
                'countries.name as country_name', 
                DB::raw("DATE_FORMAT(users.created_at, '%Y-%m-%d') AS registered_text"),
            ])
            ->with([
                'country:id,name',
                'userStatus:id,name,color',
                'enrollments.course.level',
                'enrollments.group:id,name,color,instructor_id',
                'enrollments.batch:id,name,color',
                'awardingBodyRegistrations.awardingBody:id,name',
                'awardingBodyRegistrations.registrationLevel:id,name',
                'payments.installments:id,payment_id,amount,status',
            ]);
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('instructor-students-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
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
                'pagingType'   => 'full_numbers',
                'lengthMenu'   => [[25, 50, 100, -1], [25, 50, 100, 'All']],
                'responsive'   => false,
                'autoWidth'    => false,
                'processing'   => true,
                'serverSide'   => true,
                'language'     => [
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

                 // Make the pagers compact and tidy (top & bottom)
                'drawCallback' => "function () {
                    var api = this.api();
                    var wrapper = $(api.table().container());
                    wrapper.find('.dataTables_paginate').addClass('mb-0');
                    wrapper.find('.dataTables_paginate .pagination').addClass('pagination-sm');
                    wrapper.find('.top .dataTables_paginate').addClass('ms-2');
                }",

                // pass user-status options for the header select
                'accountStatusOptions' => ['Active', 'Inactive'],
                'userStatusOptions' => UserStatus::orderBy('name')->pluck('name')->values()->toArray(),

                'initComplete' => <<<'JS'
                    function () {
                        var api  = this.api();
                        var init = api.settings()[0].oInit || {};

                        var accountStatusOptions = init.accountStatusOptions || ['Active','Inactive'];
                        var userStatusOptions    = init.userStatusOptions || [];

                        // helper to build <option> list
                        function buildOptions(arr){
                            var html = '<option value="">All</option>';
                            (arr || []).forEach(function(txt){
                            html += '<option value="'+ txt +'">'+ txt +'</option>';
                            });
                            return html;
                        }

                      api.columns().every(function(){
                        var column  = this;
                        var dataSrc = column.dataSrc();
                        var $head   = $(column.header());

                        // no filter for some columns
                        if (['awarding_body_col','id'].indexOf(dataSrc) !== -1) return;

                        $head.find('.dt-filter').remove(); // clean

                        // --- Account Status (exact match) ---
                        if (dataSrc === 'account_status') {
                            var $selAS = $('<select/>', {
                                'class':'form-select form-select-sm mt-2 dt-filter text-capitalize'
                            })
                            .append('<option value="">All</option>')
                            .append('<option value="1">Active</option>')
                            .append('<option value="0">Inactive</option>');

                            $head.append($selAS);
                            $selAS.on('change', function(){
                                // no regex; send plain 1/0 or empty
                                column.search(this.value, false, true).draw();
                            });
                            return;
                        }

                        // --- User Status (exact match) ---
                        if (dataSrc === 'user_status') {
                            var $selUS = $('<select/>', {
                                'class':'form-select form-select-sm mt-2 dt-filter text-capitalize'
                            }).html(buildOptions(userStatusOptions));
                            $head.append($selUS);
                            $selUS.on('change', function(){
                                var v  = this.value;
                                var rx = v ? '^'+ $.fn.dataTable.util.escapeRegex(v) + '$' : '';
                                column.search(rx, true, false).draw();
                            });
                            return;
                        }

                        // default text input
                        var $inp = $('<input type="text" class="form-control form-control-sm mt-2 dt-filter">');
                        $head.append($inp);
                        $inp.on('keyup change', function(){
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
            //Column::make('id')->title('#')->width(60)->searchable(false),
            Column::computed('name')->title('Name')->orderable(false)->searchable(true),
            Column::computed('email')->title('Email')->orderable(false)->searchable(true)->visible(false),
            Column::computed('phone')->title('Phone')->orderable(false)->searchable(true)->visible(false),
            Column::computed('country')->title('Country')->orderable(false)->searchable(true),
            Column::make('registered_text')->title('Registration')->name('users.created_at')->orderable(false)->searchable(true)->visible(false),
            Column::computed('courses')->title('Course')->orderable(false)->searchable(true),
            Column::computed('groups')->title('Group')->orderable(false)->searchable(true),
            Column::computed('batches')->title('Batch')->orderable(false)->searchable(true),
            Column::computed('awarding_body_col')->title('Awarding Body')->orderable(false)->searchable(false)->addClass('text-nowrap')->addClass('no-print')->printable(false),
            Column::computed('account_status')->title('Account Status')->orderable(false)->searchable(true),
            Column::computed('user_status')->title('User Status')->orderable(false)->searchable(true),
        ];
    }

    protected function filename(): string
    {
        return 'InstructorStudents_' . date('YmdHis');
    }

}
