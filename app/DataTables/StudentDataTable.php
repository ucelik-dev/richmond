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

class StudentDataTable extends DataTable
{

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            /* ───── IMAGE ───── */
            ->editColumn('image', function ($row) {
                $src = $row->image ? asset($row->image) : asset('images/avatar-placeholder.png');
                return '<div class="table-avatar"><img src="'.$src.'" alt="img" loading="lazy"></div>';
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

            ->addColumn('registration_block', function ($row) {
                $html  = '';

                if ($row->salesPerson?->name) {
                    $html .= '<div class="text-secondary mb-0">Sales : '.($row->salesPerson->name).'</div>';
                }
                if ($row->agent?->company) {
                    $html .= '<div class="text-secondary mb-0">Agent : '.($row->agent->company).'</div>';
                }
                if ($row->manager?->name) {
                    $html .= '<div class="text-secondary mb-0">Manager : '.($row->manager->name).'</div>';
                }
                if (($row->reference)) {
                    $html .= '<div class="text-secondary mb-0">Reference : '.($row->reference).'</div>';
                }
                return $html;
            })

            ->addColumn('country', fn($row) => e($row->country_name ?? ''))

            ->filterColumn('country', function ($q, $kw) {
                $kw = trim($kw);
                if ($kw !== '') {
                    $q->where('countries.name', 'like', "%{$kw}%");
                }
            })

            // COURSES (title + level)
            ->addColumn('courses', function ($row) {
                if (!$row->relationLoaded('enrollments')) return '';
                return $row->enrollments->map(function ($en) {
                    $title = $en->course?->title ?? '—';
                    $level = $en->course?->level?->name ? ' ('.$en->course->level->name.')' : '';
                    return '<span class="badge bg-primary text-primary-fg align-self-start mb-1">'
                        . e($title.$level)
                        . '</span>';
                })->implode('<br>');
            })

            // GROUPS (one badge per enrollment; remove underscores)
            ->addColumn('groups', function ($row) {
                if (!$row->relationLoaded('enrollments')) return '';
                return $row->enrollments->filter(fn($en) => $en->group)->map(function ($en) {
                    $g = $en->group;
                    $name = preg_replace('/_+/', ' ', $g->name ?? '—');
                    return '<span class="badge bg-'.e($g->color ?: 'secondary').' text-'.e($g->color ?: 'secondary').'-fg align-self-start mb-1">'
                        . e($name)
                        . '</span>';
                })->implode('<br>');
            })

            ->addColumn('group_instructors', function ($row) {
                if (!$row->relationLoaded('enrollments')) return '';
                return $row->enrollments
                    ->filter(fn($en) => $en->group && $en->group->instructor)
                    ->map(fn($en) => '<span class="align-self-start mb-1">'.e($en->group->instructor->name ?? '—').'</span>')
                    ->implode('<br>');
            })

            // BATCHES (one badge per enrollment; remove underscores)
            ->addColumn('batches', function ($row) {
                if (!$row->relationLoaded('enrollments')) return '';
                return $row->enrollments->filter(fn($en) => $en->batch)->map(function ($en) {
                    $b = $en->batch;
                    $name = preg_replace('/_+/', ' ', $b->name ?? '—');
                    return '<span class="badge bg-'.e($b->color ?: 'secondary').' text-'.e($b->color ?: 'secondary').'-fg align-self-start mb-1">'
                        . e($name)
                        . '</span>';
                })->implode('<br>');
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

            /* ───── AWARDING BODY (list) or “To be registered” ───── */
            ->addColumn('awarding_body_col', function ($row) {
                if ($row->relationLoaded('awardingBodyRegistrations') &&
                    $row->awardingBodyRegistrations->isNotEmpty()) {

                    return $row->awardingBodyRegistrations->map(function ($reg) {
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

                // else: if not pending/withdrawn/suspended and paid installments >= 1000
                $blocked = in_array(strtolower((string)$row->user_status_name), [], true);
                if (!$blocked && $row->relationLoaded('payments')) {
                    $totalPaid = $row->payments
                        ->flatMap->installments
                        ->where('status', 'paid')
                        ->sum('amount');

                    if ($totalPaid >= 1000) {
                        return '<span class="badge bg-yellow text-yellow-fg">To be registered</span>';
                    } else {
                        return '<span class="badge bg-danger text-danger-fg">Insufficient payment</span>';
                    }
                }
                return '';
            })

            /* ───── AWARDING BODY REGISTRATION DATA (user profile details) ───── */
            ->addColumn('awarding_data_col', function ($row) {
                return '
                  <div class="text-secondary">
                     <span class="d-inline-block fw-bold me-2 text-nowrap" style="width:60px;">Name</span><span>: '.e($row->name).'</span><br>
                     <span class="d-inline-block fw-bold me-2 text-nowrap" style="width:60px;">Email</span><span>: '.e($row->email).'</span><br>
                     <span class="d-inline-block fw-bold me-2 text-nowrap" style="width:60px;">DOB</span><span>: '.e($row->dob ?? '').'</span><br>
                     <span class="d-inline-block fw-bold me-2 text-nowrap" style="width:60px;">Gender</span><span>: '.e($row->gender ?? '').'</span><br>
                     <span class="d-inline-block fw-bold me-2 text-nowrap" style="width:60px;">Phone</span><span>: '.e($row->phone ?? '').'</span><br>
                     <span class="d-inline-block fw-bold me-2 text-nowrap" style="width:60px;">Country</span><span>: '.e($row->country?->name ?? '').'</span><br>
                     <span class="d-inline-block fw-bold me-2 text-nowrap" style="width:60px;">City</span><span>: '.e($row->city ?? '').'</span><br>
                     <span class="d-inline-block fw-bold me-2 text-nowrap" style="width:60px;">Postcode</span><span>: '.e($row->post_code ?? '').'</span><br>
                     <span class="d-inline-block fw-bold me-2" style="width:60px;">Address</span><span>: '.e($row->address ?? '').'</span>
                  </div>';
            })

            /* ───── REGISTRATION DATE ───── */
            ->editColumn('registered_text', fn($r) => e($r->registered_text ?? ''))

            /* ───── ACTIONS ───── */
            ->addColumn('action', function ($row) {
                $btns = '';

                if(Auth::user()?->canResource('admin_students','edit')){
                    $btns .= '<a href="'.route('admin.student.edit', $row->id).'"
                               class="btn-sm btn-primary me-2 text-decoration-none" title="Edit">
                               <i class="fa-solid fa-pen-to-square fa-lg"></i></a>';
                }
                if(Auth::user()?->canResource('admin_students','delete')){
                    $btns .= '<a href="'.route('admin.student.destroy', $row->id).'"
                               class="text-red delete-item me-2 text-decoration-none" title="Delete">
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

            /* ───── FILTER HOOKS ───── */
            ->filterColumn('name', function ($q, $kw) {
                $kw = trim($kw);
                $q->where(function ($w) use ($kw) {
                    $w->where('users.name',  'like', "%{$kw}%");
                });
            })
            ->filterColumn('courses', function ($q, $kw) {
                $kw = trim($kw);
                $q->whereHas('enrollments.course', function ($cq) use ($kw) {
                    $cq->where('courses.title', 'like', "%{$kw}%")
                    ->orWhereHas('level', fn($lq) => $lq->where('name','like',"%{$kw}%"));
                });
            })

            ->filterColumn('groups', function ($q, $kw) {
                $kw = trim($kw);
                $q->whereHas('enrollments.group', function ($gq) use ($kw) {
                    // match with underscores removed
                    $gq->whereRaw("REPLACE(groups.name, '_',' ') LIKE ?", ["%{$kw}%"]);
                });
            })

            ->filterColumn('group_instructors', function ($q, $kw) {
                $kw = trim($kw);
                if ($kw === '') return;

                $q->where(function ($w) use ($kw) {
                    // match by instructor name
                    $w->whereHas('enrollments.group.instructor', function ($tq) use ($kw) {
                        $tq->where('users.name', 'like', "%{$kw}%");
                    });

                    // if numeric, also allow exact ID match
                    if (ctype_digit($kw)) {
                        $w->orWhereHas('enrollments.group.instructor', function ($tq) use ($kw) {
                            $tq->where('users.id', (int) $kw);
                        });
                    }
                });
            })

            ->filterColumn('batches', function ($q, $kw) {
                $kw = trim($kw);
                $q->whereHas('enrollments.batch', function ($bq) use ($kw) {
                    $bq->whereRaw("REPLACE(batches.name, '_',' ') LIKE ?", ["%{$kw}%"]);
                });
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
            ->filterColumn('registered_text', function ($q, $kw) {
                $q->whereRaw("DATE_FORMAT(users.created_at, '%Y-%m-%d') LIKE ?", ["%".trim($kw)."%"]);
            })

            // Last login: format as d-m-Y H:i (or show — if null)
            ->editColumn('last_login_at', function ($row) {
                return $row->last_login_at
                    ? \Illuminate\Support\Carbon::parse($row->last_login_at)->format('Y-m-d H:i')
                    : '';
            })

            // (Optional) allow searching by formatted last_login_at
            ->filterColumn('last_login_at', function ($q, $kw) {
                $kw = trim($kw);
                if ($kw === '') return;
                $q->whereRaw("DATE_FORMAT(users.last_login_at, '%Y-%m-%d %H:%i') LIKE ?", ["%{$kw}%"]);
            })

            // (Optional) show em dash if IP is null
            ->editColumn('last_login_ip', fn ($r) => e($r->last_login_ip ?? ''))

            ->rawColumns([
                'image', 'registration_block',
                'courses','groups','batches', 'account_status', 'awarding_body_col', 'awarding_data_col',
                'account_status','user_status','roles_badges','action','group_instructors'
            ])
            ->setRowId('id');
    }

    public function query(User $model): QueryBuilder
    {
        return $model->newQuery()
        // only users whose MAIN role is 'student'
        ->whereHas('roles', function ($q) {
            $q->where('roles.name', 'student')
              ->where('user_roles.is_main', 1);   // pivot column
        })

        ->select([
            'users.id','users.name','users.email','users.contact_email','users.phone','users.image',
            'users.gender','users.dob','users.city','users.post_code','users.address',
            'users.country_id','users.account_status','users.created_at','users.login_count','users.last_login_at','users.last_login_ip',
            'users.sales_person_id','users.agent_id','users.manager_id','users.reference',
            DB::raw("DATE_FORMAT(users.created_at, '%Y-%m-%d') AS registered_text"),
            'user_statuses.name  as user_status_name',
            'user_statuses.color as user_status_color',
            'countries.name as country_name',
            'colleges.name as college_name',
        ])
        ->leftJoin('user_statuses','user_statuses.id','=','users.user_status_id')
        ->leftJoin('countries', 'countries.id', '=', 'users.country_id')
        ->leftJoin('colleges', 'colleges.id', '=', 'users.college_id') 
        ->with([
            'country:id,name',
            'enrollments.course.level',
            'enrollments.group:id,name,color,instructor_id', 
            'enrollments.group.instructor:id,name',
            'enrollments.batch:id,name,color',
            'awardingBodyRegistrations.awardingBody:id,name',
            'awardingBodyRegistrations.registrationLevel:id,name',
            'payments.installments:id,payment_id,amount,status',
            'salesPerson:id,name',
            'agent:id,company',
            'manager:id,name',
        ]);
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('students-table')
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
                'pagingType'   => 'full_numbers',
                'lengthMenu'   => [[50, 100, 500, -1], [50, 100, 500, 'All']],
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
                        if (['image','action','awarding_body_col','awarding_data_col','id'].indexOf(dataSrc) !== -1) return;

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
                Button::make('excel')->className('btn btn-primary py-1 px-2'),
                Button::make('print')->className('btn btn-primary py-1 px-2'),
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('id')->title('ID')->width(50),
            Column::computed('image')->title('Image')->exportable(false)->printable(false)->orderable(false)->searchable(false)->width(64),
            Column::computed('college')->title('College')->name('colleges.name')->orderable(false)->searchable(true),
            Column::make('name')->title('Name')->orderable(false)->searchable(true),
            Column::computed('country')->title('Country')->name('countries.name')->orderable(false)->searchable(true)->visible(false),
            Column::computed('registration_block')->title('Registration Info')->orderable(false)->searchable(true)->visible(false)->addClass('text-nowrap'),
            Column::computed('email')->title('Email')->orderable(false)->searchable(true)->visible(false),
            Column::computed('contact_email')->title('Contact Email')->orderable(false)->searchable(true)->visible(false),
            Column::make('phone')->title('Phone')->orderable(false)->searchable(true)->visible(false),
            Column::computed('courses')->title('Courses')->orderable(false)->searchable(true),
            Column::computed('groups')->title('Group')->orderable(false)->searchable(true),
            Column::computed('batches')->title('Batch')->orderable(false)->searchable(true),
            Column::computed('group_instructors')->title('Instructor')->orderable(false)->searchable(true)->addClass('text-nowrap'),
            Column::computed('account_status')->title('Account Status')->name('users.account_status')->orderable(false)->searchable(true),
            Column::computed('user_status')->title('User Status')->name('user_statuses.name')->orderable(false)->searchable(true),
            Column::make('registered_text')->title('Registration')->name('users.created_at')->orderable(true)->searchable(true),
            Column::computed('awarding_body_col')->title('Awarding Body')->orderable(false)->searchable(true)->addClass('text-nowrap')->visible(false),
            Column::computed('awarding_data_col')->title("Awarding Body<br>Registration Data")->orderable(false)->searchable(false)->addClass('text-nowrap')->visible(false),
            Column::make('login_count')->title('Login Count')->name('users.login_count')->orderable(true)->searchable(true)->width(80)->visible(false),
            Column::make('last_login_at')->title('Last Login')->name('users.last_login_at')->orderable(true)->searchable(true)->addClass('text-nowrap')->visible(false),
            Column::make('last_login_ip')->title('Last IP')->name('users.last_login_ip')->orderable(false)->searchable(true)->addClass('text-nowrap')->visible(false),
            Column::computed('action')->title('Action')->exportable(false)->printable(false)->orderable(false)->searchable(false)->addClass('text-nowrap'),
        ];
    }

    protected function filename(): string
    {
        return 'Student_' . date('YmdHis');
    }

}
