<?php

namespace App\DataTables;

use App\Models\Manager;
use App\Models\User;
use App\Models\UserStatus;
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

class ManagerDataTable extends DataTable
{

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            // avatar
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
                return e($e1).'<br>'.e($e2);
            })

            ->filterColumn('email', function ($q, $kw) {
                $kw = trim((string)$kw);
                if ($kw === '') return;
                $q->where(function ($qq) use ($kw) {
                    $qq->where('users.email', 'like', "%{$kw}%")
                    ->orWhere('users.contact_email', 'like', "%{$kw}%");
                });
            })

            // pretty badges (two separate columns)
            ->addColumn('account_status', function ($row) {
                return (int)$row->account_status === 1
                    ? '<span class="badge bg-green text-green-fg">Active</span>'
                    : '<span class="badge bg-red text-red-fg">Inactive</span>';
            })
            ->addColumn('user_status', function ($row) {
                $name  = $row->user_status_name ?? '—';
                $color = $row->user_status_color ?? 'secondary';
                return '<span class="badge bg-'.$color.' text-'.$color.'-fg">'.ucwords($name).'</span>';
            })

            ->editColumn('registered_text', fn($r) => e($r->registered_text ?? ''))

            ->addColumn('action', function ($row) {
                $btns = '';

                if(Auth::user()?->canResource('admin_managers','edit')){
                    $btns .= '<a href="'.route('admin.manager.edit', $row->id).'"
                               class="btn-sm btn-primary me-2 text-decoration-none" title="Edit">
                               <i class="fa-solid fa-pen-to-square fa-lg"></i></a>';
                }
                if(Auth::user()?->canResource('admin_managers','delete')){
                    $btns .= '<a href="'.route('admin.manager.destroy', $row->id).'"
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

            // column-specific server filters
            ->filterColumn('account_status', function ($q, $keyword) {
                // header select sends "1" or "0" (we also accept 'active'/'inactive')
                $raw = strtolower(trim($keyword, '^$ '));
                if ($raw === '') return;

                if (in_array($raw, ['1','active','true','yes'], true)) {
                    $q->where('users.account_status', 1);
                } elseif (in_array($raw, ['0','inactive','false','no'], true)) {
                    $q->where('users.account_status', 0);
                }
            })
            ->filterColumn('user_status', function ($q, $keyword) {
                $kw = trim($keyword, '^$ ');
                if ($kw !== '') {
                    $q->whereRaw('LOWER(user_statuses.name) = ?', [strtolower($kw)]);
                }
            })
            ->rawColumns([
                'image','account_status','user_status','email','action'
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
                'user_statuses.name  as user_status_name',
                'user_statuses.color as user_status_color',
                'colleges.name as college_name', 
            ])
            // only MANAGERS with MAIN role
            ->whereHas('roles', function ($r) {
                $r->where('roles.name', 'manager')
                  ->where('user_roles.is_main', 1); // change pivot/table/column name if yours differs
            })
            ->leftJoin('user_statuses','user_statuses.id','=','users.user_status_id')
            ->leftJoin('colleges', 'colleges.id', '=', 'users.college_id');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('manager-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->pageLength(50)
            ->orderBy(0, 'desc')
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

                // used in header select for user status
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

                      var $wrap = $(api.table().container());
                      // stop header filter clicks from triggering sort
                      $wrap
                        .off('.dtHdrStop')
                        .on('click.dtHdrStop mousedown.dtHdrStop keydown.dtHdrStop', 'thead .dt-filter, thead .dt-filter *', function(e){ e.stopPropagation(); });

                      api.columns().every(function(){
                        var column = this;
                        var dataSrc = column.dataSrc();
                        var $head  = $(column.header());

                        // no filters for these
                        if (['image','action','id'].indexOf(dataSrc) !== -1) return;

                        $head.find('.dt-filter').remove();

                        // account status select (1/0)
                        if (dataSrc === 'account_status') {
                          var $sel = $('<select/>', {'class':'form-select form-select-sm mt-2 dt-filter'})
                            .append('<option value="">All</option>')
                            .append('<option value="1">Active</option>')
                            .append('<option value="0">Inactive</option>');
                          $head.append($sel);
                          $sel.on('change', function(){
                            column.search(this.value, false, true).draw();
                          });
                          return;
                        }

                        // user status select (exact match)
                        if (dataSrc === 'user_status') {
                          var $us = $('<select/>', {'class':'form-select form-select-sm mt-2 dt-filter text-capitalize'})
                            .html(buildOptions(userStatusOptions));
                          $head.append($us);
                          $us.on('change', function(){
                            var v = this.value;
                            var rx = v ? '^' + $.fn.dataTable.util.escapeRegex(v) + '$' : '';
                            column.search(rx, true, false).draw();
                          });
                          return;
                        }

                        // default text input
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
            Column::computed('image')->title('Image')->exportable(false)->printable(false)->orderable(false)->searchable(false)->width(60),
            Column::computed('college')->title('College')->name('colleges.name')->orderable(false)->searchable(true),
            Column::make('name')->title('Name')->name('users.name')->orderable(true)->searchable(true),
            Column::make('email')->title('Email')->name('users.email')->orderable(false)->searchable(true),
            Column::make('phone')->title('Phone')->name('users.phone')->orderable(false)->searchable(true),

            // separate status columns
            Column::computed('account_status')->title('Account Status')->name('users.account_status')->orderable(false)->searchable(true),
            Column::computed('user_status')->title('User Status')->name('user_statuses.name')->orderable(false)->searchable(true),

            Column::make('registered_text')->title('Registration')->name('users.created_at')->orderable(true)->searchable(true),

            Column::computed('action')->title('Action')->exportable(false)->printable(false)->orderable(false)->searchable(false)->addClass('text-nowrap'),
        ];
    }

    protected function filename(): string
    {
        return 'Manager_' . date('YmdHis');
    }

}
