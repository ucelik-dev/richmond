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

class InstructorDataTable extends DataTable
{

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))

            // Avatar
            ->editColumn('image', function ($row) {
                $src = $row->image ? asset($row->image) : asset('images/avatar-placeholder.png');
                return '<div class="table-avatar"><img src="'.$src.'" alt="" loading="lazy"></div>';
            })

            ->addColumn('college', fn($row) => e($row->college_name ?? ''))

            ->filterColumn('college', function ($q, $kw) {
                $kw = trim($kw);
                if ($kw === '') return;

                // allow searching by name or code
                $q->where(function ($w) use ($kw) {
                    $w->where('colleges.name', 'like', "%{$kw}%");
                });
            })

            ->editColumn('email', function ($row) {
                $e1 = trim((string) $row->email);
                $e2 = trim((string) $row->contact_email);

                // only contact_email present
                if ($e1 === '') {
                    return e($e2);
                }

                // only email present OR both equal (case-insensitive)
                if ($e2 === '' || strcasecmp($e1, $e2) === 0) {
                    return e($e1);
                }

                // both different → show both
                return e($e1).'<br>'
                    .e($e2);
            })

            ->filterColumn('email', function ($q, $kw) {
                $kw = trim((string)$kw);
                if ($kw === '') return;
                $q->where(function ($qq) use ($kw) {
                    $qq->where('users.email', 'like', "%{$kw}%")
                    ->orWhere('users.contact_email', 'like', "%{$kw}%");
                });
            })

            // Groups + per-group student counts + total
            ->addColumn('groups', function ($row) {
                if (!$row->relationLoaded('groups')) return '';

                $html = '';
                $total = 0;

                foreach ($row->groups as $g) {
                    $name  = str_replace(['_', '-'], ' ', $g->name);
                    $count = $g->students?->count() ?? 0;
                    $total += $count;

                    $html .= '<span class="badge bg-info text-info-fg align-self-start mb-1">'.$name.'</span>
                              <span class="badge bg-light text-muted align-self-start mb-1">'.$count.'</span><br>';
                }

                if ($row->groups->isEmpty()) {
                    $html .= '<span class="text-secondary">No groups</span>';
                } else {
                    $html .= '<hr class="mt-1 mb-2">
                        <span class="badge bg-primary text-primary-fg align-self-start mb-1">Total</span>
                        <span class="badge bg-primary text-primary-fg align-self-start mb-1">'.$total.'</span>';
                }

                return $html;
            })

            // Students by account statuses (Active/Inactive)
            ->addColumn('students_account_counts', function ($row) {
                // flatten all students across instructor's groups (unique by id)
                $students = $row->groups->flatMap->students->unique('id');

                $active   = $students->where('account_status', 1)->count();
                $inactive = $students->where('account_status', 0)->count();

                if ($students->isEmpty()) {
                    return '<span class="text-secondary">No students</span>';
                }

                return
                    '<span class="badge bg-success text-white align-self-start mb-1">Active: '.$active.'</span><br>'.
                    '<span class="badge bg-danger text-white align-self-start mb-1">Inactive: '.$inactive.'</span>';
            })

            // Students by user statuses (Ongoing/Graduated/…)
            ->addColumn('students_user_counts', function ($row) {
                // require userStatus eager-loaded on the students
                $students = $row->groups->flatMap->students->unique('id');

                if ($students->isEmpty()) {
                    return '<span class="text-muted">No students</span>';
                }

                $by = $students->groupBy('user_status_id');
                $badges = $by->map(function ($set, $statusId) use ($students) {
                    $first  = $set->first();
                    $name   = optional($first->userStatus)->name ?? '—';
                    $color  = optional($first->userStatus)->color ?? 'secondary';
                    return '<span class="badge bg-'.$color.' text-'.$color.'-fg mb-1">'.ucfirst($name).': '.$set->count().'</span>';
                })->implode('<br>');

                return $badges;
            })

            // ACCOUNT STATUS → green/red badge
            ->editColumn('account_status', function ($row) {
                return (int) $row->account_status === 1
                    ? '<span class="badge bg-green text-green-fg">Active</span>'
                    : '<span class="badge bg-red text-red-fg">Inactive</span>';
            })

            // USER STATUS → colored badge from joined table
            ->addColumn('user_status', function ($row) {
                $name  = $row->user_status ?? '—';
                $color = $row->user_status_color ?? 'secondary';
                return '<span class="badge bg-'.$color.' text-'.$color.'-fg">'.ucwords($name).'</span>';
            })

            // filters
            ->filterColumn('account_status', function ($q, $keyword) {
                // header select sends "", "1", "0"
                $v = trim((string) $keyword, '^$ ');
                if ($v === '1')  { $q->where('users.account_status', 1); }
                if ($v === '0')  { $q->where('users.account_status', 0); }
            })
            ->filterColumn('user_status', function ($q, $keyword) {
                $kw = trim($keyword, '^$ ');
                if ($kw !== '') {
                    $q->whereRaw('LOWER(user_statuses.name) = ?', [strtolower($kw)]);
                    // (or partial match) ->where('user_statuses.name','like',"%{$kw}%")
                }
            })

            // Registration (d-m-Y)
            ->editColumn('registered_text', fn($r) => e($r->registered_text ?? ''))


            // Actions
            ->addColumn('action', function ($row) {
                $btns = '';

                if(Auth::user()?->canResource('admin_impersonate_instructors','edit')){
                    $btns .= '<a href="'.route('admin.instructor.edit', $row->id).'"
                               class="btn-sm btn-primary me-2 text-decoration-none">
                               <i class="fa-solid fa-pen-to-square fa-lg"></i></a>';
                }
                if(Auth::user()?->canResource('admin_impersonate_instructors','delete')){
                    $btns .= '<a href="'.route('admin.instructor.destroy', $row->id).'"
                               class="text-red delete-item me-2 text-decoration-none">
                               <i class="fa-solid fa-trash-can fa-lg"></i></a>';
                }
                if(Auth::user()?->canResource('admin_impersonate_users','edit')){
                    $btns .= '<a href="'.route('admin.impersonate.quick', $row->id).'"
                            class="text-yellow text-decoration-none" title="Impersonate">
                                <i class="fa-solid fa-user-secret fa-lg"></i>
                            </a>';
                }
                return $btns;
            })

            // FILTER HOOKS (server-side)
            ->filterColumn('groups', function ($q, $kw) {
                $kw = trim($kw);
                $q->whereHas('groups', function ($gq) use ($kw) {
                    $gq->where('groups.name', 'like', "%{$kw}%");
                });
            })
            ->filterColumn('students_account_counts', function ($q, $kw) {
                $kw = strtolower(trim($kw));
                if ($kw === 'active') {
                    $q->whereHas('groups.students', fn($sq) => $sq->where('users.account_status', 1));
                } elseif ($kw === 'inactive') {
                    $q->whereHas('groups.students', fn($sq) => $sq->where('users.account_status', 0));
                }
            })
            ->filterColumn('students_user_counts', function ($q, $kw) {
                $kw = trim($kw, '^$ ');
                if ($kw !== '') {
                    $q->whereHas('groups.students.userStatus', fn($sq) => $sq->where('name', 'like', "%{$kw}%"));
                }
            })

            ->rawColumns([
                'image','groups','students_account_counts','students_user_counts', 'account_status','user_status',
                'email', 'action'
            ])
            ->setRowId('id');
    }

    public function query(User $model): QueryBuilder
    {
        return $model->newQuery()
        ->select([
            'users.id','users.name','users.email','users.contact_email','users.phone','users.image',
            'users.account_status','users.created_at',
            DB::raw("DATE_FORMAT(users.created_at, '%Y-%m-%d') AS registered_text"),
            'user_statuses.name  as user_status',
            'user_statuses.color as user_status_color',
            'colleges.name as college_name', 
        ])
        ->leftJoin('user_statuses','user_statuses.id','=','users.user_status_id')
        ->leftJoin('colleges', 'colleges.id', '=', 'users.college_id')

        // ⬇️ main role MUST be instructor (this automatically excludes admins)
        ->whereHas('mainRoleRelation', function ($q) {
            $q->where('name', 'instructor');
        })

        // load what we need for the three columns
        ->with([
            'groups',                            // keep full selects to avoid ambiguity issues
            'groups.students',                   // students in each group
            'groups.students.userStatus',        // for the colored labels
        ]);
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('instructor-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
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
                'lengthMenu' => [[50, 100, 500, -1], [50, 100, 500, 'All']],
                'responsive' => false,
                'autoWidth'  => false,
                'processing' => true,
                'serverSide' => true,

                'language' => [
                    'info'       => 'Showing <b>_TOTAL_</b> records',
                    'infoEmpty'  => 'No records',
                    'infoFiltered' => '',
                    'lengthMenu' => '_MENU_ records per page',
                    'paginate' => [
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

                // options for header selects
                'userStatusOptions' => UserStatus::orderBy('name')->pluck('name')->values()->toArray(),

                // header filters
                'initComplete' => <<<'JS'
                    function () {
                        var api = this.api();
                        var init = api.settings()[0].oInit || {};
                        var userStatusOptions = init.userStatusOptions || [];

                        function buildOptions(arr){
                            var html = '<option value="">All</option>';
                            (arr || []).forEach(function (txt) {
                                html += '<option value="'+ txt +'">'+ txt +'</option>';
                            });
                            return html;
                        }

                        api.columns().every(function () {
                            var column = this;
                            var dataSrc = column.dataSrc();
                            var $head  = $(column.header());

                            // no filter for these
                            var noFilter = ['image','action','groups','students_account_counts','students_user_counts','id'];
                            if (noFilter.indexOf(dataSrc) !== -1) return;

                            $head.find('.dt-filter').remove();

                            // simple text input default
                            var $inp = $('<input/>', {
                                type:'text', 'class':'form-control form-control-sm mt-2 dt-filter'
                            });
                            $head.append($inp);
                            $inp.on('keyup change', function () {
                                column.search(this.value).draw();
                            });

                            // for user status (instructor's own) provide select
                            if (dataSrc === 'user_status') {
                                var $sel = $('<select/>', {
                                    'class':'form-select form-select-sm mt-2 dt-filter text-capitalize'
                                }).html(buildOptions(userStatusOptions));
                                $head.find('input.dt-filter').remove();
                                $head.append($sel);
                                $sel.on('change', function(){
                                    var v  = this.value;
                                    var rx = v ? '^'+ $.fn.dataTable.util.escapeRegex(v) + '$' : '';
                                    column.search(rx, true, false).draw();
                                });
                            }

                            // for account_status (instructor's own) provide select (1/0)
                            if (dataSrc === 'account_status') {
                                var $sel2 = $('<select/>', {
                                    'class':'form-select form-select-sm mt-2 dt-filter'
                                })
                                  .append('<option value="">All</option>')
                                  .append('<option value="1">Active</option>')
                                  .append('<option value="0">Inactive</option>');
                                $head.find('input.dt-filter').remove();
                                $head.append($sel2);
                                $sel2.on('change', function(){
                                    column.search(this.value, false, true).draw();
                                });
                            }
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
            Column::make('id')->title('ID')->width(40),
            Column::computed('image')->title('Image')->exportable(false)->printable(false)->orderable(false)->searchable(false)->width(60),
            Column::computed('college')->title('College')->name('colleges.name')->orderable(false)->searchable(true),
            Column::make('name')->title('Name')->name('users.name')->orderable(false),
            Column::make('email')->title('Email')->name('users.email')->orderable(false),
            Column::make('phone')->title('Phone')->name('users.phone')->orderable(false),
            Column::computed('groups')->title('Group')->orderable(false)->searchable(true)->addClass('text-nowrap'),
            Column::computed('students_account_counts')->title('Students by<br>Account Statuses')->orderable(false)->searchable(true)->addClass('text-nowrap'),
            Column::computed('students_user_counts')->title('Students by<br>User Statuses')->orderable(false)->searchable(true)->addClass('text-nowrap'),
            Column::make('account_status')->title('Account Status')->name('users.account_status')->visible(true)->orderable(false)->searchable(true),
            Column::computed('user_status')->title('User Status')->name('user_statuses.name')->orderable(false)->searchable(true),
            Column::make('registered_text')->title('Registration')->name('users.created_at')->orderable(true)->searchable(true),
            Column::computed('action')->title('Action')->exportable(false)->printable(false)->addClass('text-nowrap')->orderable(false)->searchable(false),
        ];
    }

    protected function filename(): string
    {
        return 'Instructor_' . date('YmdHis');
    }

}
