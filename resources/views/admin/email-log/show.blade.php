@extends('admin.layouts.master')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">EMAIL LOG PREVIEW</h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.email-log.index') }}" class="btn btn-default">
                            <i class="fa-solid fa-arrow-left me-2"></i>
                            Back
                        </a>
                    </div>
                </div>
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table ">
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>User Name</th>
                                    <th>User Email</th>
                                    <th>Sent at</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-nowrap">{{ $emailLog->subject ?? '-' }}</td>

                                    <td class="text-nowrap">{{ $emailLog->user->name ?? '-' }}</td>

                                    <td class="text-nowrap">{{ $emailLog->to ?? '-' }}</td>

                                    <td class="text-nowrap">{{ \Carbon\Carbon::parse($emailLog->sent_at)->format('d-m-Y H:m:i') }}</td>

                                    <td class="text-nowrap">
                                        @if($emailLog->status === 'sent')
                                            <span class="badge bg-success text-success-fg">{{ $emailLog->status }}</span>
                                        @else
                                            <span class="badge bg-danger text-danger-fg">{{ $emailLog->status }}</span>
                                        @endif
                                    
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="p-0">
                                        <iframe
                                            id="emailPreview"
                                            class="w-100 border-0"
                                            style="min-height: 200px;"
                                            src="{{ route('admin.email-log.inline', $emailLog) }}">
                                        </iframe>
                                    </td>
                                </tr>
                                <tr>
                                    
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h4 class="mt-3 mb-2"><i class="fa-solid fa-paperclip"></i> Attachments</h4>
                    {{-- under the preview card --}}
                    @if($attachments->isNotEmpty())
                    <div class="mt-3">
                        <h5 class="mb-2">Attachments</h5>
                        @foreach($attachments as $i => $att)
                        <div>
                            <a href="{{ $att['url'] }}" target="_blank" rel="noopener">
                            {{ $att['name'] }}
                            </a>
                            {{-- or use secure download route: --}}
                            {{-- <a href="{{ route('admin.email-log.attachment.download', [$emailLog->id, $i]) }}">{{ $att['name'] }}</a> --}}
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="mt-3 text-muted">No attachments</div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    const frame = document.getElementById('emailPreview');
    if (!frame) return;

    function resize() {
        try {
            const doc = frame.contentDocument || frame.contentWindow.document;
            if (doc && doc.body) {
                frame.style.height = (doc.body.scrollHeight || 900) + 'px';
            }
        } catch (e) {
            // ignore if sandbox/cross-origin blocks access
        }
    }

    frame.addEventListener('load', resize);
    // fallback resize in case images load later
    setTimeout(resize, 1000);
})();
</script>
@endpush