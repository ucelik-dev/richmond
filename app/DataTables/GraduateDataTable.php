<?php

namespace App\DataTables;

use App\Models\Graduate;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class GraduateDataTable extends DataTable
{

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('student_cell', function ($r) {
                $h  = '<div class="text-nowrap">'.e($r->user_name);
                if ($r->user_email) $h .= '<br><small class="text-secondary">'.e($r->user_email).'</small>';
                if ($r->user_phone) $h .= '<br><small class="text-secondary">'.e($r->user_phone).'</small>';
                return $h.'</div>';
            })
            ->addColumn('course_cell', function ($r) {
                $level = optional(optional($r->course)->level)->name;  // $r->course?->level?->name
                $h = e($r->course_title ?: '—');
                if ($level) $h .= ' <small class="text-secondary">(' . e($level) . ')</small>';
                return $h;
            })
            ->addColumn('rc_grad_text',  fn($r) => $r->rc_graduation_date ? \Carbon\Carbon::parse($r->rc_graduation_date)->format('Y-m-d') : '')
            ->addColumn('top_up_text',   fn($r) => $r->top_up_date ? \Carbon\Carbon::parse($r->top_up_date)->format('Y-m-d') : '')
            ->addColumn('job_start_text',fn($r) => $r->job_start_date ? \Carbon\Carbon::parse($r->job_start_date)->format('Y-m-d') : '')
            ->addColumn('diploma_cell',  function ($r) {
                if (!$r->diploma_file) return '';
                $url = asset($r->diploma_file);
                return '<a href="'.$url.'" target="_blank"><i class="fa-solid fa-eye"></i> View</a>';
            })
            ->addColumn('study_mode',  function ($r) {
                if (!$r->study_mode) return '';
                $label = str_replace(['_', '-'], ' ', $r->study_mode);
                return '<span class="badge bg-secondary text-secondary-fg align-self-start text-capitalize">'.$label.'</a>';
            })
            ->addColumn('employed_badge', function ($r) {
                return $r->job_status
                    ? '<span class="badge bg-success text-success-fg">Yes</span>'
                    : '<span class="badge bg-danger text-danger-fg">No</span>';
            })
            ->addColumn('university_cell', function ($r) {
                $h  = e($r->university ?: '');
                if ($r->program) $h .= '<br><small class="text-secondary">'.e($r->program).'</small>';
                return $h;
            })
            ->addColumn('action', function ($r) {
                $edit = route('admin.graduate.edit', $r->id);
                $del  = route('admin.graduate.destroy', $r->id);

                $h = '';
                if(Auth::user()?->canResource('admin_graduates','edit')){
                    $h .= '<a href="'.$edit.'" class="btn-sm btn-primary me-2 text-decoration-none">
                             <i class="fa-solid fa-pen-to-square fa-lg"></i>
                           </a>';
                }
                if(Auth::user()?->canResource('admin_graduates','delete')){
                    $h .= '<a href="'.$del.'" class="text-red delete-item text-decoration-none">
                             <i class="fa-solid fa-trash-can fa-lg"></i>
                           </a>';
                }
                return $h ?: '-';
            })

            // FILTERS
            ->filterColumn('student_cell', function ($q, $kw) {
                $kw = trim($kw);
                $q->where(function($w) use ($kw) {
                    $w->where('users.name',  'like', "%{$kw}%")
                      ->orWhere('users.email','like', "%{$kw}%")
                      ->orWhere('users.phone','like', "%{$kw}%");
                });
            })
            ->filterColumn('course_cell', function ($q, $kw) {
                $kw = trim($kw);
                $q->where(function ($w) use ($kw) {
                    $w->where('courses.title', 'like', "%{$kw}%")
                    ->orWhereHas('course.level', function ($qq) use ($kw) {
                        $qq->where('name', 'like', "%{$kw}%");
                    });
                });
            })
            ->filterColumn('rc_grad_text', function ($q, $kw) {
                $kw = trim($kw);
                if ($kw !== '') $q->whereRaw("DATE_FORMAT(graduates.rc_graduation_date, '%Y-%m-%d') LIKE ?", ["%{$kw}%"]);
            })
            ->filterColumn('top_up_text', function ($q, $kw) {
                $kw = trim($kw);
                if ($kw !== '') $q->whereRaw("DATE_FORMAT(graduates.top_up_date, '%Y-%m-%d') LIKE ?", ["%{$kw}%"]);
            })
            ->filterColumn('job_start_text', function ($q, $kw) {
                $kw = trim($kw);
                if ($kw !== '') $q->whereRaw("DATE_FORMAT(graduates.job_start_date, '%Y-%m-%d') LIKE ?", ["%{$kw}%"]);
            })
            ->filterColumn('employed_badge', function ($q, $kw) {
                $raw = strtolower(trim($kw, '^$ '));
                if ($raw === '') return;
                if (in_array($raw, ['yes','1','true'], true))  $q->where('graduates.job_status', 1);
                if (in_array($raw, ['no','0','false'], true)) $q->where('graduates.job_status', 0);
            })

            ->rawColumns([
                'student_cell','study_mode','course_cell','diploma_cell','employed_badge','university_cell','action'
            ])
            ->setRowId('id');
    }

    public function query(Graduate $model): QueryBuilder
    {
        return $model->newQuery()
            ->select([
                'graduates.*',
                'users.name  as user_name',
                'users.email as user_email',
                'users.phone as user_phone',
                'courses.title as course_title',
            ])
            ->leftJoin('users',   'users.id',   '=', 'graduates.user_id')
            ->leftJoin('courses', 'courses.id', '=', 'graduates.course_id');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('graduate-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0) // by id/#
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

                'drawCallback' => "function () {
                    var api = this.api();
                    var wrapper = $(api.table().container());
                    wrapper.find('.dataTables_paginate').addClass('mb-0');
                    wrapper.find('.dataTables_paginate .pagination').addClass('pagination-sm');
                    wrapper.find('.top .dataTables_paginate').addClass('ms-2');
                }",

                'initComplete' => <<<'JS'
                    function () {
                      var api = this.api();
                      var $wrap = $(api.table().container());

                      // stop header-sort when interacting with filters
                      $wrap.off('.dtHdrStop')
                           .on('click.dtHdrStop mousedown.dtHdrStop keydown.dtHdrStop', 'thead .dt-filter, thead .dt-filter *', function(e){ e.stopPropagation(); });

                      function addInput($head, column){
                        var $inp = $('<input/>', {'class':'form-control form-control-sm mt-2 dt-filter', type:'text'});
                        $head.append($inp);
                        $inp.on('keyup change', function(){ column.search(this.value).draw(); });
                      }

                      api.columns().every(function(){
                        var column = this, dataSrc = column.dataSrc(), $head = $(column.header());
                        if (['action','diploma_cell','note','id'].indexOf(dataSrc) !== -1) return;

                        $head.find('.dt-filter').remove();

                        if (dataSrc === 'employed_badge') {
                          var $sel = $('<select/>', {'class':'form-select form-select-sm mt-2 dt-filter'})
                                     .append('<option value="">All</option>')
                                     .append('<option value="Yes">Yes</option>')
                                     .append('<option value="No">No</option>');
                          $head.append($sel);
                          $sel.on('change', function(){
                            var v = this.value;
                            column.search(v, false, true).draw();
                          });
                          return;
                        }

                        if (dataSrc === 'study_mode') {
                          var $sel = $('<select/>', {'class':'form-select form-select-sm mt-2 dt-filter'})
                                     .append('<option value="">All</option>')
                                     .append('<option value="online">Online</option>')
                                     .append('<option value="on_campus">On Campus</option>')
                                     .append('<option value="hybrid">Hybrid</option>');
                          $head.append($sel);
                          $sel.on('change', function(){
                            var v = this.value;
                            column.search(v, false, true).draw();
                          });
                          return;
                        }

                        addInput($head, column);
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
            Column::make('id')->title('#')->name('graduates.id')->width(50),
            Column::computed('student_cell')->title('Student')->orderable(false)->searchable(true),
            Column::computed('course_cell')->title('Course')->orderable(false)->searchable(true),
            Column::computed('rc_grad_text')->title('RC Graduation')->orderable(true)->searchable(true),
            Column::computed('diploma_cell')->title('RC Diploma')->orderable(false)->searchable(false),
            Column::computed('top_up_text')->title('Top-up Date')->orderable(true)->searchable(true),
            Column::computed('university_cell')->title('University / Program')->orderable(false)->searchable(true),
            Column::computed('study_mode')->title('Study Mode')->orderable(false)->searchable(true)->addClass('text-nowrap'),
            Column::computed('employed_badge')->title('Employed?')->orderable(false)->searchable(true)->visible(false),
            Column::make('job_title')->title('Job Title')->name('graduates.job_title')->orderable(false)->searchable(true)->visible(false),
            Column::computed('job_start_text')->title('Job Start')->orderable(true)->searchable(true)->visible(false),
            Column::make('note')->title('Note')->name('graduates.note')->orderable(false)->searchable(true),
            Column::computed('action')->title('Action')->exportable(false)->printable(false)->orderable(false)->searchable(false)->addClass('text-nowrap'),
        ];
    }

    protected function filename(): string
    {
        return 'Graduate_' . date('YmdHis');
    }

}
