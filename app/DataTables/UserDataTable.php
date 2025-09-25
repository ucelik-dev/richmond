<?php

namespace App\DataTables;

use App\Models\Role;
use App\Models\User;
use App\Models\UserStatus;
use DB;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Str;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class UserDataTable extends DataTable
{

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            // IMAGE
            ->editColumn('image', function ($row) {
                $src = $row->image ? asset($row->image) : asset('images/avatar-placeholder.png');

                return '<div class="table-avatar">
                        <img src="'.$src.'" alt="logo" loading="lazy">
                        </div>';
            })

            ->addColumn('college', fn($row) => e($row->college_name ?? ''))

            ->editColumn('education_status', function ($row) {
                return $row->education_status
                    ? Str::of($row->education_status)->replace('_', ' ')->title()
                    : '';
            })

            ->filterColumn('college', function ($q, $kw) {
                $kw = trim($kw);
                if ($kw === '') return;

                // allow searching by name or code
                $q->where(function ($w) use ($kw) {
                    $w->where('colleges.name', 'like', "%{$kw}%");
                });
            })

            // COURSES (title + level)
            ->addColumn('courses', function ($row) {
                if (!$row->relationLoaded('enrollments')) return '';
                return $row->enrollments->map(function ($en) {
                    $title = $en->course?->title ?? '—';
                    $level = $en->course?->level?->name ? ' ('.$en->course->level->name.')' : '';
                    return '<span class="badge bg-secondary text-secondary-fg align-self-start mb-1">'.$title.$level.'</span>';
                })->implode('<br>');
            })

            // BATCH badges
            ->addColumn('batches', function ($row) {
                if (!$row->relationLoaded('enrollments')) return '';
                return $row->enrollments->filter(fn($en) => $en->batch)->map(function ($en) {
                    $b = $en->batch;
                    $label = str_replace(['_', '-'], ' ', $b->name);
                    return '<span class="badge bg-'.$b->color.' text-'.$b->color.'-fg align-self-start mb-1">'.$label.'</span>';
                })->implode('<br>');
            })

            // GROUP badges
            ->addColumn('groups', function ($row) {
                if (!$row->relationLoaded('enrollments')) return '';
                return $row->enrollments->filter(fn($en) => $en->group)->map(function ($en) {
                    $g = $en->group;
                    $label = str_replace(['_', '-'], ' ', $g->name);
                    return '<span class="badge bg-'.$g->color.' text-'.$g->color.'-fg align-self-start mb-1">'.$label.'</span>';
                })->implode('<br>');
            })

            // ACCOUNT STATUS
            ->editColumn('account_status', function ($row) {
                if ((int)$row->account_status === 1) {
                    return '<span class="badge bg-green text-green-fg">Active</span>';
                }
                return '<span class="badge bg-red text-red-fg">Inactive</span>';
            })

            // USER STATUS (from joined user_statuses)
            ->addColumn('user_status', function ($row) {
                $name  = $row->user_status_name ?? '—';
                $color = $row->user_status_color ?? 'secondary';
                return '<span class="badge bg-'.$color.' text-'.$color.'-fg">'.ucwords($name).'</span>';
            })

            // ROLES (sorted by main first)
            ->addColumn('roles', function ($row) {
                if (!$row->relationLoaded('roles')) return '';
                return $row->roles
                    ->sortByDesc(fn($r) => (int)($r->pivot->is_main ?? 0))
                    ->map(function ($r) {
                        $cls = ($r->pivot->is_main ?? 0) ? 'bg-primary text-primary-fg' : 'bg-secondary text-primary-fg';
                        return '<span class="badge '.$cls.' align-self-start mb-1">'.ucfirst($r->name).'</span>';
                    })->implode(' ');
            })

            // REGISTERED date (d-m-Y)
            ->editColumn('registered_text', fn($r) => e($r->registered_text ?? ''))

            // ACTIONS
            ->addColumn('action', function ($row) {
                $btns = '';
                
                if(Auth::user()?->canResource('admin_user_permissions','edit')){
                    $btns .= '<a href="'.route('admin.user.permission.edit', $row->id).'"
                               class="btn-sm text-yellow me-2 text-decoration-none">
                               <i class="fa-solid fa-user-shield fa-lg"></i></a>';
                }
                if(Auth::user()?->canResource('admin_users','edit')){
                    $btns .= '<a href="'.route('admin.user.edit', $row->id).'"
                               class="btn-sm btn-primary me-2 text-decoration-none">
                               <i class="fa-solid fa-pen-to-square fa-lg"></i></a>';
                }
                if(Auth::user()?->canResource('admin_users','delete')){
                    $btns .= '<a href="'.route('admin.user.destroy', $row->id).'"
                               class="text-red delete-item text-decoration-none me-2">
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

            // SEARCH hooks
            
            ->filterColumn('courses', function ($q, $kw) {
                $kw = trim($kw);
                $q->whereHas('enrollments.course', function ($cq) use ($kw) {
                    $cq->where('courses.title', 'like', "%{$kw}%")
                       ->orWhereHas('level', fn($lq) => $lq->where('name','like',"%{$kw}%"));
                });
            })
            ->filterColumn('batches', function ($q, $kw) {
                $kw = trim($kw);
                $needle = str_replace(' ', '_', $kw); // treat spaces as underscores
                $q->whereHas('enrollments.batch', fn($bq) => $bq->where('name','like',"%{$needle}%"));
            })
            ->filterColumn('groups', function ($q, $kw) {
                $kw = trim($kw);
                $needle = str_replace(' ', '_', $kw);
                $q->whereHas('enrollments.group', fn($gq) => $gq->where('name','like',"%{$needle}%"));
            })
            ->filterColumn('roles', function ($q, $kw) {
                $q->whereHas('roles', fn($rq) => $rq->where('name','like',"%{$kw}%"));
            })
            ->filterColumn('user_status', function ($q, $keyword) {
                // value comes like ^Ongoing$ from the header's exact-match select
                $kw = trim($keyword, '^$ ');               // "Ongoing"
                if ($kw === '') return;

                $q->whereRaw('LOWER(user_statuses.name) = ?', [strtolower($kw)]);
                // If you prefer partial match, use:
                // $q->where('user_statuses.name', 'like', "%{$kw}%");
            })
            ->filterColumn('registered_text', function ($q, $kw) {
                // search against formatted d-m-Y
                $q->whereRaw("DATE_FORMAT(users.created_at, '%Y-%m-%d') LIKE ?", ["%".trim($kw)."%"]);
            })
            ->filterColumn('account_status', function ($q, $keyword) {
                // Value comes from header select like "^Active$" / "^Inactive$"
                $raw = trim((string) $keyword, '^$ ');
                if ($raw === '') return;

                $kw = strtolower($raw);
                $val = null; // bool|null

                // map to true/false
                if (in_array($kw, ['active','1','true','yes'], true)) {
                    $val = true;
                } elseif (in_array($kw, ['inactive','0','false','no'], true)) {
                    $val = false;
                }

                if ($val !== null) {
                    // IMPORTANT: pass a PHP boolean so the driver binds TRUE/FALSE correctly
                    $q->where('users.account_status', $val);
                }
            })

            // Last login: format as d-m-Y H:i (or show — if null)
            ->editColumn('last_login_at', function ($row) {
                $v = $row->last_login_at;

                // Handle casted Carbon, nulls, and MySQL zero-dates safely
                if ($v instanceof \Illuminate\Support\Carbon) {
                    return $v->format('d-m-Y H:i');
                }

                if (empty($v) || $v === '0000-00-00 00:00:00' || $v === '0000-00-00') {
                    return '';
                }

                try {
                    return \Illuminate\Support\Carbon::parse($v)->format('d-m-Y H:i');
                } catch (\Throwable $e) {
                    return '';
                }
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
                'image','name_block','courses','batches',
                'groups','account_status','user_status',
                'roles','action'
            ])
            ->setRowId('id');
    }

    public function query(User $model): QueryBuilder
    {
        return $model->newQuery()
            ->select([
                'users.id','users.name','users.email','users.contact_email','users.phone','users.image',
                'users.account_status','users.created_at', 'users.login_count','users.last_login_at','users.last_login_ip',
                'users.education_status',
                DB::raw("DATE_FORMAT(users.created_at, '%Y-%m-%d') AS registered_text"),
                'user_statuses.name  as user_status_name',
                'user_statuses.color as user_status_color',
                'colleges.name as college_name',
            ])
            ->leftJoin('user_statuses','user_statuses.id','=','users.user_status_id')
            ->leftJoin('colleges', 'colleges.id', '=', 'users.college_id')
            ->with([
                'enrollments.course.level',
                'enrollments.batch',
                'enrollments.group',
                'roles:id,name',                 // pivot has is_main
            ]);
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('user-table')
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

                        'roleOptions' => Role::orderBy('name')->pluck('name')->values()->toArray(),
                        'accountStatusOptions' => ['Active', 'Inactive'],
                        'userStatusOptions' => UserStatus::orderBy('name')->pluck('name')->values()->toArray(),

                        'initComplete' => <<<'JS'
                            function () {
                            var api = this.api();
                            var init = api.settings()[0].oInit || {};

                            var roleOptions = init.roleOptions || [];
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

                            api.columns().every(function () {
                                var column = this;
                                var dataSrc = column.dataSrc();
                                var $head  = $(column.header());

                                // DO NOT add a filter for these columns
                                var noFilter = ['action','image','id']; // add more keys if needed
                                    if (noFilter.indexOf(dataSrc) !== -1) {
                                    return; // skip creating input/select
                                }

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

                                // --- Roles (contains match, since cells may have multiple badges) ---
                                if (dataSrc === 'roles') {
                                    var $selR = $('<select/>', {
                                        'class':'form-select form-select-sm mt-2 dt-filter text-capitalize'
                                    }).html(buildOptions(roleOptions));
                                    $head.append($selR);
                                    $selR.on('change', function(){
                                        column.search(this.value || '', false, true).draw();   // plain contains
                                    });
                                    return;
                                }

                                // --- default text input for other columns ---
                                var $inp = $('<input/>', {
                                type:'text', 'class':'form-control form-control-sm mt-2 dt-filter'
                                });
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
            Column::make('id')->title('id')->width(40)->searchable(true),
            Column::computed('image')->title('Image')->exportable(false)->printable(false)->orderable(false)->searchable(false)->width(60)->visible(false),
            Column::computed('college')->title('College')->name('colleges.name')->orderable(false)->searchable(true),
            Column::make('name')->title('Name')->name('users.name')->orderable(false)->searchable(true)->addClass('text-nowrap'),
            Column::computed('education_status')->title('Education Status')->orderable(false)->searchable(true)->visible(false),
            Column::make('email')->title('Email')->name('users.email')->orderable(false)->searchable(true)->visible(false),
            Column::computed('contact_email')->title('Contact Email')->orderable(false)->searchable(true)->visible(false),
            Column::make('phone')->title('Phone')->name('users.phone')->orderable(false)->searchable(true),
            Column::computed('courses')->title('Courses')->orderable(false)->searchable(true),
            Column::computed('batches')->title('Batch')->orderable(false)->searchable(true)->visible(false),
            Column::computed('groups')->title('Group')->orderable(false)->searchable(true)->visible(false),
            Column::make('account_status')->title('Account Status')->name('users.account_status')->orderable(false)->searchable(true),
            Column::computed('user_status')->title('User Status')->name('user_statuses.name')->orderable(false)->searchable(true),
            Column::computed('roles')->title('Roles')->orderable(false)->searchable(true),
            Column::make('registered_text')->title('Registration')->name('users.created_at')->orderable(true)->searchable(true),
            Column::make('login_count')->title('Login Count')->name('users.login_count')->orderable(true)->searchable(true)->width(80)->visible(false),
            Column::make('last_login_at')->title('Last Login')->name('users.last_login_at')->orderable(true)->searchable(true)->addClass('text-nowrap')->visible(false),
            Column::make('last_login_ip')->title('Last IP')->name('users.last_login_ip')->orderable(false)->searchable(true)->addClass('text-nowrap')->visible(false),
            Column::computed('action')->title('Action')->exportable(false)->printable(false)->addClass('text-nowrap')->addClass('no-print')->orderable(false)->searchable(false),
        ];
    }

    protected function filename(): string
    {
        return 'User_' . date('YmdHis');
    }
}
