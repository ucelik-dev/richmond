@extends('admin.layouts.master')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        EXPENSES ( <span id="expense-total" class="text-center" style="font-size:.95rem;font-weight:bold">—</span> )
                    </h3>
                    <div class="card-actions">
                        @if (auth()->user()?->canResource('admin_expenses', 'create'))
                            <a href="{{ route('admin.expense.create') }}" class="btn btn-dark px-2 py-1 px-md-3 py-md-2">
                                <i class="fa-solid fa-plus me-2"></i> Add new
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body table-responsive">
                    {{ $dataTable->table() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}

    <script type="module">
        // When the DataTable loads/reloads, update the total
        const updateExpenseTotal = () => {
            const table = $('#expense-table').DataTable();
            const json = table.ajax.json() || {};
            const text = (json.total_amount_text !== undefined) ?
                json.total_amount_text :
                (json.total_amount !== undefined ? json.total_amount : '—');
            $('#expense-total').text(text);
        };

        // Hook into DataTables events
        $(document).on('xhr.dt draw.dt', '#expense-table', updateExpenseTotal);
    </script>
@endpush
