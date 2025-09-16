<?php

namespace App\DataTables;

use App\Models\EmailLog;
use DB;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class EmailLogDataTable extends DataTable
{

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('subject_cell', fn($r) => e($r->subject ?? '-'))
            ->addColumn('name_cell',    fn($r) => e($r->user_name ?? '-'))
            ->addColumn('email_to',     fn($r) => e($r->to ?? '-'))
            ->editColumn('sent_at_text', fn($r) => e($r->sent_at_text ?? ''))
            ->addColumn('status_badge', function ($r) {
                $s = strtolower((string)$r->status);
                $cls = $s === 'sent' ? 'bg-success text-success-fg' : 'bg-danger text-danger-fg';
                return '<span class="badge '.$cls.'">'.e($r->status ?? '-').'</span>';
            })
            ->addColumn('action', function ($r) {
                $show = route('admin.email-log.show', $r->id);
                return '<a href="'.$show.'" class="btn-sm btn-primary text-decoration-none">'
                     . '<i class="fa-solid fa-eye me-2"></i>Preview</a>';
            })

            /* -------- filters ---------- */
            ->filterColumn('subject_cell', fn($q, $kw) => $q->where('email_logs.subject', 'like', '%'.trim($kw).'%'))
            ->filterColumn('name_cell',    fn($q, $kw) => $q->where('users.name', 'like', '%'.trim($kw).'%'))
            ->filterColumn('email_to',     fn($q, $kw) => $q->where('email_logs.to', 'like', '%'.trim($kw).'%'))
            ->filterColumn('status_badge', function ($q, $kw) {
                $v = strtolower(trim($kw, '^$ '));
                if ($v !== '') $q->where('email_logs.status', $v);
            })
            ->filterColumn('sent_at_text', function ($q, $kw) {
                $kw = trim($kw);
                if ($kw !== '') {
                    // matches YYYY-MM-DD or part of it
                    $q->whereRaw("DATE_FORMAT(email_logs.sent_at, '%Y-%m-%d %H:%i:%s') LIKE ?", ["%{$kw}%"]);
                }
            })

            ->rawColumns(['status_badge','action'])
            ->setRowId('id');
    }

    public function query(EmailLog $model): QueryBuilder
    {
        return $model->newQuery()
            ->select([
                'email_logs.*',
                'users.name as user_name',
                DB::raw("DATE_FORMAT(email_logs.sent_at, '%d-%m-%Y %H:%i:%s') as sent_at_text"),
            ])
            ->leftJoin('users', 'users.id', '=', 'email_logs.user_id');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('email-log-table')
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
                'initComplete' => <<<'JS'
                    function () {
                      var api = this.api();
                      var $wrap = $(api.table().container());

                      // stop header sort when using inputs
                      $wrap.off('.dtHdrStop')
                           .on('click.dtHdrStop mousedown.dtHdrStop keydown.dtHdrStop',
                               'thead .dt-filter, thead .dt-filter *', function(e){ e.stopPropagation(); });

                      function textFilter(col){
                        var $inp = $('<input/>', {class:'form-control form-control-sm mt-2 dt-filter', type:'text'});
                        $(col.header()).append($inp);
                        $inp.on('keyup change', function(){ col.search(this.value).draw(); });
                      }

                      api.columns().every(function(){
                        var column = this;
                        var dataSrc = column.dataSrc();
                        var $head = $(column.header());
                        $head.find('.dt-filter').remove();

                        if (['action','id'].indexOf(dataSrc) !== -1) return;

                        if (dataSrc === 'status_badge') {
                          var $sel = $('<select/>', {class:'form-select form-select-sm mt-2 dt-filter'})
                            .append('<option value="">All</option>')
                            .append('<option value="sent">sent</option>')
                            .append('<option value="failed">failed</option>')
                            .append('<option value="bounced">bounced</option>');
                          $head.append($sel);
                          $sel.on('change', function(){
                            column.search(this.value || '', false, true).draw();
                          });
                          return;
                        }

                        textFilter(column);
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
            Column::make('id')->title('#')->width(60)->orderable(true)->searchable(false),
            Column::computed('subject_cell')->title('Subject')->orderable(false)->searchable(true),
            Column::computed('name_cell')->title('Name')->orderable(false)->searchable(true),
            Column::computed('email_to')->title('Email')->orderable(false)->searchable(true),
            Column::make('sent_at_text')->title('Sent at')->name('email_logs.sent_at')->orderable(true)->searchable(true),
            Column::computed('status_badge')->title('Status')->orderable(false)->searchable(true)->width(90),
            Column::computed('action')->title('Action')->exportable(false)->printable(false)->orderable(false)->searchable(false)->addClass('text-nowrap'),
        ];
    }

    protected function filename(): string
    {
        return 'EmailLog_' . date('YmdHis');
    }
    
}
