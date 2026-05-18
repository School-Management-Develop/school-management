@extends('backend.layout.master')

@section('title', 'Item Action Logs')
@section('item_active', 'active')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0">
                <i class="bi bi-journal-text me-2"></i>Item Action Logs
            </h4>
            <p class="text-muted small mb-0">Full history of item create / update / delete / restore actions.</p>
        </div>
        <a href="{{ route('items.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Back to Items
        </a>
    </div>

    {{-- Search --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('items.action_logs') }}" class="d-flex gap-2" style="max-width:420px;">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="q" value="{{ request('q') }}"
                           class="form-control border-start-0" placeholder="Search item or user...">
                </div>
                <button class="btn btn-primary px-3" type="submit">Search</button>
                @if(request('q'))
                    <a href="{{ route('items.action_logs') }}" class="btn btn-outline-secondary">Reset</a>
                @endif
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="px-4 pt-3 pb-2 d-flex justify-content-between align-items-center">
                <span class="text-muted small">{{ $logs->total() }} log(s) found</span>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-secondary small">
                            <th style="width:50px;">#</th>
                            <th>Item</th>
                            <th style="width:110px;">Action</th>
                            <th>Details</th>
                            <th style="width:130px;">By</th>
                            <th class="text-center" style="width:160px;">Date &amp; Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @include('backend.page.items.action_logs_rows', ['logs' => $logs])
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-4 py-3">
                {{ $logs->withQueryString()->links() }}
            </div>
        </div>
    </div>

</div>
@endsection