<?php

namespace App\DataTables;

use App\Models\Agent;
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
use Yajra\DataTables\Services\DataTable;

class AgentDataTable extends DataTable
{

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            // LOGO / avatar
            ->editColumn('image', function ($row) {
                $src = $row->image ? asset($row->image) : asset('images/avatar-placeholder.png');
                return '<div class="table-avatar"><img src="'.$src.'" alt="logo" loading="lazy"></div>';
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

            // NAME block (company, name, country, email, phone)
            ->addColumn('name_cell', function ($row) {
                $company = $row->company ?: '';
                $country = $row->country->name ?? '';
                $email1 = $row->email ?: '';
                $email2 = $row->contact_email ?: '';

                $emailsHtml = '';
                if ($email1 !== '') {
                    $emailsHtml .= '<span class="text-secondary">'.e($email1).'</span>';
                }
                if ($email2 !== '' && strcasecmp($email1, $email2) !== 0) {
                    $emailsHtml .= ($emailsHtml ? '<br>' : '') . '<span class="text-secondary">'.e($email2).'</span>';
                }
                if ($emailsHtml !== '') $emailsHtml .= '<br>';

                return '<div class="text-nowrap">'
                    . '<span class="fw-bold text-uppercase text-primary">'.e($company).'</span><br>'
                    . '<span>'.e($row->name).'</span><br>'
                    . '<span>'.e($country).'</span><br>'
                    . $emailsHtml
                    . '<span class="text-secondary">'.e($row->phone).'</span>'
                    . '</div>';
            })

            // Let the "Name" header input search company, contact name, email, phone and country
            ->filterColumn('name_cell', function ($q, $keyword) {
                $kw = trim($keyword);
                if ($kw === '') return;

                $q->where(function ($w) use ($kw) {
                    $w->where('users.company', 'like', "%{$kw}%")
                    ->orWhere('users.name',   'like', "%{$kw}%")
                    ->orWhere('users.email',  'like', "%{$kw}%")
                    ->orWhere('users.contact_email',  'like', "%{$kw}%")
                    ->orWhere('users.phone',  'like', "%{$kw}%")
                    ->orWhereHas('country', function ($cq) use ($kw) {
                        $cq->where('name', 'like', "%{$kw}%");
                    });
                });
            })

            ->addColumn('notes_cell', function ($row) {
                if (!$row->relationLoaded('userNotes') || $row->userNotes->isEmpty()) {
                    return '<small class="text-muted">No notes</small>';
                }

                return $row->userNotes->map(function ($n) {
                    $by = $n->addedBy->name ?? null;
                    $at = $n->created_at ? $n->created_at->format('d-m-Y') : null;

                    $meta = [];
                    if ($by) $meta[] = e($by);
                    if ($at) $meta[] = e($at);

                    return '<div class="mb-2" style="max-width:480px;white-space:normal;">'
                        . '<div>'.nl2br(e($n->note)).'</div>'
                        . ($meta ? '<div class="small text-muted">'.implode(' · ', $meta).'</div>' : '')
                        . '<hr class="m-1">'
                        . '</div>';
                })->implode('');
            })
            ->filterColumn('notes_cell', function ($q, $kw) {
                $kw = trim($kw);
                if ($kw === '') return;
                $q->whereHas('userNotes', function ($nq) use ($kw) {
                    $nq->where('note', 'like', "%{$kw}%");
                });
            })


            // Students & commissions (accordion)
            ->addColumn('students_cell', function ($row) {
                if ($row->assignedStudents_count < 1) {
                    return '<small class="text-muted">No students</small>';
                }

                $id = (int)$row->id;
                $html = '<div class="accordion bg-light" id="accordion-'.$id.'">';
                $html .= '<div class="accordion-item">';
                $html .= '<h2 class="accordion-header" id="heading-'.$id.'">';
                $html .= '<button class="accordion-button collapsed text-primary fw-bold px-3 py-1" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-'.$id.'" aria-expanded="false" aria-controls="collapse-'.$id.'">';
                $html .= 'Registrations ('.$row->assignedStudents_count.')';
                $html .= '</button></h2>';
                $html .= '<div id="collapse-'.$id.'" class="accordion-collapse collapse" data-bs-parent="#accordion-'.$id.'"><div class="accordion-body pt-0">';

                // We loaded the relations in query() to avoid N+1
                foreach ($row->assignedStudents as $student) {
                    $courseTitle = $student->enrollments->first()->course->title ?? null;
                    $courseLevel = $student->enrollments->first()->course->level->name ?? null;

                    // Commissions for this agent per this student (already eager loaded)
                    $paid   = 0;
                    $unpaid = 0;

                    foreach ($row->commissions as $com) {
                        if (optional($com->payment)->user_id === $student->id) {
                            if ($com->status === 'paid') $paid += $com->amount;
                            else                        $unpaid += $com->amount;
                        }
                    }

                    $html .= '<div class="mb-1">';
                    $html .= '<span class="badge bg-info text-info-fg mb-1">'.e($student->name).'</span><br>';
                    if ($courseTitle) {
                        $html .= '<span class="badge bg-light mb-1 px-0">'.e($courseTitle).($courseLevel ? ' ('.e($courseLevel).')' : '').'</span>';
                    }
                    if ($paid > 0 || $unpaid > 0) {
                        $html .= '<br><small>';

                        $pieces = [];
                        if ($paid > 0) {
                            $pieces[] = '<span class="badge bg-success text-success-fg">'.currency_format($paid).'</span>';
                        }
                        if ($unpaid > 0) {
                            $pieces[] = '<span class="badge bg-danger text-danger-fg">'.currency_format($unpaid).'</span>';
                        }

                        $html .= implode(' | ', $pieces);
                        $html .= '</small>';
                    } else {
                        $html .= '<br><small class="text-muted">No commissions</small>';
                    }
                    $html .= '</div><hr class="my-3">';
                }

                $html .= '</div></div></div></div>';

                return $html;
            })

            // Totals (server-side selectSub already computed as com_total/com_paid/com_unpaid)
            ->addColumn('totals_cell', function ($row) {
                $total  = currency_format($row->com_total ?? 0);
                $paid   = currency_format($row->com_paid ?? 0);
                $unpaid = currency_format($row->com_unpaid ?? 0);

                return '<div class="text-secondary text-nowrap">'
                    . '<span style="display:inline-block;width:60px;">Total </span> : '.$total.'<br>'
                    . '<span style="display:inline-block;width:60px;">Paid </span> : '.$paid.'<br>'
                    . '<span style="display:inline-block;width:60px;">Unpaid </span> : '.$unpaid
                    . '</div>';
            })

            // Account status badge (Active / Inactive)
            ->addColumn('account_status', function ($row) {
                return ((int)$row->account_status === 1)
                    ? '<span class="badge bg-green text-green-fg">Active</span>'
                    : '<span class="badge bg-red text-red-fg">Inactive</span>';
            })

            // User status badge (uses joined color + name)
            ->addColumn('user_status', function ($row) {
                $name  = $row->user_status_name  ?? '—';
                $color = $row->user_status_color ?? 'secondary';
                return '<span class="badge bg-'.$color.' text-'.$color.'-fg">'.ucwords($name).'</span>';
            })

            // Account status select (accepts 1/0 or active/inactive)
            ->filterColumn('account_status', function($q, $keyword) {
                $raw = strtolower(trim($keyword, '^$ '));
                if ($raw === '') return;

                if (in_array($raw, ['1','active','true','yes'], true)) {
                    $q->where('users.account_status', 1);
                    return;
                }
                if (in_array($raw, ['0','inactive','false','no'], true)) {
                    $q->where('users.account_status', 0);
                    return;
                }
            })

            // User status exact match (e.g. ^Ongoing$)
            ->filterColumn('user_status', function($q, $keyword) {
                $kw = trim($keyword, '^$ ');
                if ($kw !== '') {
                    $q->whereRaw('LOWER(user_statuses.name) = ?', [strtolower($kw)]);
                }
            })

            // Registration date (preformatted)
            ->editColumn('registered_text', fn($r) => e($r->registered_text ?? ''))

            // Actions
            ->addColumn('action', function ($row) {
                $btns = '';

                if(Auth::user()?->canResource('admin_agents','edit')){
                    $btns .= '<a href="'.route('admin.agent.edit', $row->id).'"
                               class="btn-sm btn-primary me-2 text-decoration-none">
                               <i class="fa-solid fa-pen-to-square fa-lg"></i></a>';
                }
                if(Auth::user()?->canResource('admin_agents','delete')){
                    $btns .= '<a href="'.route('admin.agent.destroy', $row->id).'"
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

            ->filterColumn('user_status_name', function($q, $keyword) {
                $kw = trim($keyword, '^$ ');
                if ($kw !== '') {
                    $q->whereRaw('LOWER(user_statuses.name) = ?', [strtolower($kw)]);
                }
            })

            ->addColumn('documents_cell', function ($row) {
                if (!$row->documents || $row->documents->isEmpty()) {
                    return '<small class="text-muted">No documents</small>';
                }

                $out = '';
                foreach ($row->documents as $doc) {
                    $category = $doc->category->name;
                    $url   = $doc->path ? asset($doc->path) : '#';
                    $ext = $doc->extension ?: strtolower(pathinfo(parse_url($doc->path, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION)) ?: 'file';
                    $icon  = getFileIcon($ext);

                    $out .= '<a href="'.$url.'" target="_blank" rel="noopener" '.
                            'class=" text-decoration-none me-1 mb-1 d-inline-block"><i class="fa-solid '.$icon.' me-1"></i>'.basename($doc->path).'</a><br>';
                }
                return $out;
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
                'image','name_cell','students_cell','totals_cell','account_status','user_status','action','documents_cell','notes_cell'
            ])
            ->setRowId('id');
    }

    public function query(User $model): QueryBuilder
    {
        return $model->newQuery()
            ->select([
                'users.id','users.name','users.email','users.contact_email','users.phone','users.image',
                'users.company','users.country_id','users.login_count','users.last_login_at','users.last_login_ip',
                'users.account_status','users.created_at',
                DB::raw("DATE_FORMAT(users.created_at, '%Y-%m-%d') AS registered_text"),
                'user_statuses.name  as user_status_name',
                'user_statuses.color as user_status_color',
                'colleges.name as college_name', 
            ])
            // totals with selectSub (fast + filterable)
            ->selectSub(function ($q) {
                $q->from('commissions')
                  ->selectRaw('COALESCE(SUM(amount),0)')
                  ->whereColumn('commissions.user_id', 'users.id');
            }, 'com_total')
            ->selectSub(function ($q) {
                $q->from('commissions')
                  ->selectRaw('COALESCE(SUM(amount),0)')
                  ->whereColumn('commissions.user_id', 'users.id')
                  ->where('status','paid');
            }, 'com_paid')
            ->selectSub(function ($q) {
                $q->from('commissions')
                  ->selectRaw('COALESCE(SUM(amount),0)')
                  ->whereColumn('commissions.user_id', 'users.id')
                  ->where('status','!=','paid');
            }, 'com_unpaid')

            ->selectSub(function ($q) {
                $q->from('user_notes as n')
                ->selectRaw('MAX(n.created_at)')
                ->whereColumn('n.user_id', 'users.id');
            }, 'latest_note_at')

            // only AGENTS with MAIN role
            ->whereHas('roles', function ($r) {
                $r->where('roles.name', 'agent')          // scope to agent role
                ->where('user_roles.is_main', 1);       // use your actual pivot table + column
                // If your pivot table is named differently, e.g. role_user, use that name here.
            })

            ->leftJoin('user_statuses','user_statuses.id','=','users.user_status_id')
            ->leftJoin('colleges', 'colleges.id', '=', 'users.college_id')

            // basic eager-loading to render accordions efficiently
            ->withCount([
                'assignedStudents as assignedStudents_count' => function ($q) {
                    $q->whereHas('roles', function ($r) {
                        $r->where('roles.name', 'student')
                        ->where('user_roles.is_main', 1);   // <- your pivot/flag
                    });
                }
            ])
            ->with([
            'country:id,name',

            'assignedStudents' => function ($q) {
                $q->select('id','name','agent_id')
                ->whereHas('roles', function ($r) {
                    $r->where('roles.name', 'student')
                        ->where('user_roles.is_main', 1);
                })
                ->with([
                    'enrollments' => function ($e) {
                        $e->select('id','user_id','course_id')
                            ->with([
                                'course:id,title,level_id',
                                'course.level:id,name'
                            ]);
                    }
                ]);
            },

            'commissions:id,user_id,amount,status,payment_id',
            'commissions.payment:id,user_id',
            'documents:id,user_id,category_id,path',

            'userNotes' => function ($q) {
                $q->select('id','user_id','added_by','note','created_at')
                ->orderByDesc('created_at');
            },
            'userNotes.addedBy:id,name',
        ]);
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('agent-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->pageLength(50)
            ->orderBy(0, 'DESC')
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

                // add header selects for account/user status
                'userStatusOptions' => UserStatus::orderBy('name')->pluck('name')->values()->toArray(),

                'initComplete' => <<<'JS'
                    function () {
                      var api = this.api();
                      var init = api.settings()[0].oInit || {};
                      var userStatusOptions = init.userStatusOptions || [];

                      function buildOptions(arr){
                        var html = '<option value="">All</option>';
                        (arr || []).forEach(function(v){ html += '<option value="'+v+'">'+v+'</option>'; });
                        return html;
                      }

                      api.columns().every(function(){
                        var column = this;
                        var dataSrc = column.dataSrc();
                        var $head = $(column.header());

                        // don't add filter to these
                        if (['image','students_cell','totals_cell','documents_cell','status_cell','action','id'].indexOf(dataSrc) !== -1) return;

                        $head.find('.dt-filter').remove();

                        // Account Status select
                        if (dataSrc === 'account_status') {
                        var $sel = $('<select/>', { 'class': 'form-select form-select-sm mt-2 dt-filter' })
                            .append('<option value="">All</option>')
                            .append('<option value="1">Active</option>')
                            .append('<option value="0">Inactive</option>');
                        $head.append($sel);
                        $sel.on('change', function(){
                            column.search(this.value, false, true).draw();
                        });
                        return;
                        }

                        // User Status select
                        if (dataSrc === 'user_status') {
                        var $us = $('<select/>', { 'class':'form-select form-select-sm mt-2 dt-filter text-capitalize' })
                            .html(buildOptions(userStatusOptions));
                        $head.append($us);
                        $us.on('change', function(){
                            var v = this.value;
                            var rx = v ? '^'+ $.fn.dataTable.util.escapeRegex(v) + '$' : '';
                            column.search(rx, true, false).draw();
                        });
                        return;
                        }

                        if (dataSrc === 'user_status_name') {
                          var $us = $('<select/>', {
                            'class': 'form-select form-select-sm mt-2 dt-filter text-capitalize'
                          }).html(buildOptions(userStatusOptions));
                          $head.append($us);
                          $us.on('change', function(){
                            var v = this.value;
                            var rx = v ? '^'+ $.fn.dataTable.util.escapeRegex(v) + '$' : '';
                            column.search(rx, true, false).draw();
                          });
                          return;
                        }

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
            Column::make('id')->title('ID')->name('users.id')->width(50),
            Column::computed('image')->title('Logo')->exportable(false)->printable(false)->orderable(false)->searchable(false)->width(60),
            Column::computed('college')->title('College')->name('colleges.name')->orderable(false)->searchable(true),
            Column::computed('name_cell')->title('Name')->orderable(false)->searchable(true),
            Column::computed('students_cell')->title('Students & Commissions')->orderable(false)->searchable(false)->addClass('text-nowrap'),
            Column::computed('notes_cell')->title('Notes')->name('latest_note_at')->orderable(true)->searchable(true)->visible(true),
            Column::computed('totals_cell')->title('Total Commission')->orderable(false)->searchable(false),
            Column::computed('documents_cell')->title('Documents')->orderable(false)->searchable(true)->addClass('text-nowrap')->visible(false),
            Column::computed('account_status')->title('Account Status')->orderable(false)->searchable(true)->visible(false),
            Column::computed('user_status')->title('User Status')->orderable(false)->searchable(true)->visible(false),
            Column::make('registered_text')->title('Registration')->name('users.created_at')->orderable(true)->searchable(true),
            Column::make('login_count')->title('Login Count')->name('users.login_count')->orderable(true)->searchable(true)->width(80)->visible(false),
            Column::make('last_login_at')->title('Last Login')->name('users.last_login_at')->orderable(true)->searchable(true)->addClass('text-nowrap')->visible(false),
            Column::make('last_login_ip')->title('Last IP')->name('users.last_login_ip')->orderable(false)->searchable(true)->addClass('text-nowrap')->visible(false),
            Column::computed('action')->title('Action')->exportable(false)->printable(false)->orderable(false)->searchable(false)->addClass('text-nowrap'),
        ];
    }

    protected function filename(): string
    {
        return 'Agent_' . date('YmdHis');
    }

}
