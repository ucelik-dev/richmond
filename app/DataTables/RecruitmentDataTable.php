<?php

namespace App\DataTables;

use App\Models\Recruitment;
use App\Models\RecruitmentSource;
use App\Models\RecruitmentStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Str;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class RecruitmentDataTable extends DataTable
{

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            
            // Name (plain)
            ->editColumn('name', fn ($r) => e($r->name ?: '—'))

            // Phone (plain; show em-dash when empty)
            ->editColumn('phone', fn ($r) => e($r->phone ?: '—'))

            // Country (already selected as country_name)
            ->addColumn('country_name', fn($r) => e($r->country_name ?? '—'))

            // Call logs (count) – if you don't have the relation, leave the selectSub in query() out
            ->addColumn('call_logs', function ($r) {
                if (!$r->relationLoaded('callLogs') || $r->callLogs->isEmpty()) {
                    return '<span class="text-secondary">—</span>';
                }

            return $r->callLogs->map(function ($c) {
                $callDate = $c->created_at ? Carbon::parse($c->created_at)->format('d-m-Y') : '—';
                    
                return '
                    <div class="text-secondary " style="min-width:400px">
                        <div style="display:grid;grid-template-columns:60px 12px 1fr;align-items:start">
                            <div class="fw-bold">Date</div>   <div>:</div> <div>'.e($callDate).'</div>
                            <div class="fw-bold">Method</div> <div>:</div> <div>'.e(ucfirst($c->communication_method)).'</div>
                            <div class="fw-bold">Status</div> <div>:</div> <div><span class="badge bg-'.e($c->status->color).' text-'.e($c->status->color).'-fg">'.e($c->status->name).'</span></div>
                            <div class="fw-bold">Note</div>   <div>:</div> <div style="word-break:break-word;white-space:normal">'.e($c->note).'</div>
                        </div>
                        <hr class="m-1">
                    </div>';

            })->implode('');
        })

            // Source
            ->addColumn('source_name', fn($r) => e($r->source_name ?? '—'))

            // Status badge (color comes from recruitment_statuses.color)
            ->addColumn('status_badge', function ($r) {
                $name  = $r->status_name  ?? '—';     // e.g., "Called"
                $color = $r->status_color ?? 'secondary'; // e.g., "primary", "success", ...
                return '<span class="badge bg-'.e($color).' text-'.e($color).'-fg">'.e($name).'</span>';
            })

            // Actions
            ->addColumn('action', function ($r) {
                $edit = route('admin.recruitment.edit', $r->id);
                $del  = route('admin.recruitment.destroy', $r->id);

                $h = '';
                if(Auth::user()?->canResource('admin_recruitments','edit')){
                    $h .= '<a href="'.$edit.'" class="btn-sm btn-primary me-2 text-decoration-none">
                            <i class="fa-solid fa-pen-to-square fa-lg"></i></a>';
                }
                if(Auth::user()?->canResource('admin_recruitments','delete')){
                    $h .= '<a href="'.$del.'" class="text-red delete-item text-decoration-none">
                            <i class="fa-solid fa-trash-can fa-lg"></i></a>';
                }
                return $h ?: '-';
            })

            /* ------------ FILTER HOOKS ------------ */
            ->filterColumn('name_cell', function ($q, $kw) {
                $kw = trim($kw);
                if ($kw === '') return;
                $q->where(function ($w) use ($kw) {
                    $w->where('recruitments.name',  'like', "%{$kw}%")
                      ->orWhere('recruitments.phone', 'like', "%{$kw}%")
                      ->orWhere('recruitments.email', 'like', "%{$kw}%");
                });
            })
            ->filterColumn('country_name', function ($q, $kw) {
                $kw = trim($kw);
                if ($kw !== '') $q->where('countries.name', 'like', "%{$kw}%");
            })
            ->filterColumn('source_name', function ($q, $kw) {
                $kw = trim($kw, '^$ ');
                if ($kw !== '') $q->where('recruitment_sources.name', $kw);
            })
            ->filterColumn('status_badge', function ($q, $kw) {
                $kw = trim($kw, '^$ ');
                if ($kw !== '') $q->where('recruitment_statuses.name', $kw);
            })
            // Name only
            ->filterColumn('name', function ($q, $kw) {
                $kw = trim($kw);
                if ($kw !== '') $q->where('recruitments.name', 'like', "%{$kw}%");
            })

            // Phone only
            ->filterColumn('phone', function ($q, $kw) {
                $kw = trim($kw);
                if ($kw !== '') $q->where('recruitments.phone', 'like', "%{$kw}%");
            })

            ->rawColumns(['name_cell','status_badge','action','call_logs'])
            ->setRowId('id');
    }

    public function query(Recruitment $model): QueryBuilder
    {
        return $model->newQuery()
            ->select([
                'recruitments.id','recruitments.name','recruitments.phone','recruitments.email',
                'recruitments.country_id','recruitments.source_id','recruitments.status_id',

                // joined fields for easy rendering/filtering
                'countries.name as country_name',
                'recruitment_sources.name as source_name',
                'recruitment_statuses.name as status_name',
                'recruitment_statuses.color as status_color',
            ])

            ->leftJoin('countries',            'countries.id',            '=', 'recruitments.country_id')
            ->leftJoin('recruitment_sources',  'recruitment_sources.id',  '=', 'recruitments.source_id')
            ->leftJoin('recruitment_statuses', 'recruitment_statuses.id', '=', 'recruitments.status_id')

            ->with(['callLogs' => function ($q) {
                $q->latest('called_at')
                ->take(5)                   // show up to 5 latest; adjust as you like
                ->with('status');           // only if your RecruitmentCall has status() relation
            }]);
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('recruitment-table')
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

                // pass options for header selects
                'statusOptions' => RecruitmentStatus::orderBy('name')->pluck('name')->values()->toArray(),
                'sourceOptions' => RecruitmentSource::orderBy('name')->pluck('name')->values()->toArray(),

                'initComplete' => <<<'JS'
                    function () {
                      var api = this.api();
                      var init = api.settings()[0].oInit || {};

                      var statusOptions = init.statusOptions || [];
                      var sourceOptions = init.sourceOptions || [];

                      function buildOptions(arr){
                        var html = '<option value="">All</option>';
                        (arr || []).forEach(function(v){ html += '<option value="'+ v +'">'+ v +'</option>'; });
                        return html;
                      }

                      api.columns().every(function () {
                        var column  = this;
                        var dataSrc = column.dataSrc();
                        var $head   = $(column.header());
                        $head.find('.dt-filter').remove();

                        // no filters for these
                        if (['id','action','call_logs'].indexOf(dataSrc) !== -1) return;

                        // Status select (exact match)
                        if (dataSrc === 'status_badge') {
                          var $sel = $('<select/>', {'class':'form-select form-select-sm mt-2 dt-filter'})
                                      .html(buildOptions(statusOptions));
                          $head.append($sel);
                          $sel.on('change', function(){
                            var v = this.value;
                            var rx = v ? '^' + $.fn.dataTable.util.escapeRegex(v) + '$' : '';
                            column.search(rx, true, false).draw();
                          });
                          return;
                        }

                        // Source select (exact match)
                        if (dataSrc === 'source_name') {
                          var $selS = $('<select/>', {'class':'form-select form-select-sm mt-2 dt-filter'})
                                      .html(buildOptions(sourceOptions));
                          $head.append($selS);
                          $selS.on('change', function(){
                            var v = this.value;
                            var rx = v ? '^' + $.fn.dataTable.util.escapeRegex(v) + '$' : '';
                            column.search(rx, true, false).draw();
                          });
                          return;
                        }

                        // default text input (name, country)
                        var $inp = $('<input type="text" class="form-control form-control-sm mt-2 dt-filter">');
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
            Column::make('id')->title('#')->width(50)->orderable(true)->searchable(false),
            Column::make('name')->title('Name')->name('recruitments.name')->orderable(false)->searchable(true),
            Column::make('phone')->title('Phone')->name('recruitments.phone')->orderable(false)->searchable(true),
            Column::make('country_name')->title('Country')->name('countries.name')->orderable(false)->searchable(true),
            Column::make('source_name')->title('Source')->name('recruitment_sources.name')->orderable(false)->searchable(true),
            Column::computed('call_logs')->title('Call Logs')->orderable(false)->searchable(false)->width(500),
            Column::computed('status_badge')->title('Status')->orderable(false)->searchable(true)->width(100),
            Column::computed('action')->title('Action')->exportable(false)->printable(false)->orderable(false)->searchable(false)->addClass('text-nowrap'),
        ];
    }

    protected function filename(): string
    {
        return 'Recruitment_' . date('YmdHis');
    }

}
