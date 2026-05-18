@extends('backend.layout.master')

@section('title', 'Returned Late')
@section('late_return_active', 'active')
@section('contents')

    <style>
        .content-wrapper,
        .content,
        .container,
        .container-lg,
        .container-md,
        .container-sm {
            max-width: 100% !important;
            width: 100% !important;
        }

        /* ── Call-history button (matches overdue page style) ── */
        .btn-call-history {
            background: linear-gradient(135deg, #e0e7ff 0%, #dbeafe 100%);
            color: #3730a3;
            border: none;
            font-size: 0.75rem;
            padding: 3px 10px;
            border-radius: 20px;
            font-weight: 500;
        }

        .btn-call-history:hover {
            background: linear-gradient(135deg, #c7d2fe 0%, #bfdbfe 100%);
            color: #3730a3;
        }
    </style>

    <div class="container-fluid" style="padding:3%;">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div>
                <h2 class="fw-bold mb-1">{{ __('app.returned Late') }}</h2>
                <div class="text-secondary">{{ __('app.Students who returned items late (more than 2 days).') }}</div>
            </div>

            <form method="GET" action="{{ url()->current() }}" class="d-flex flex-wrap gap-2 align-items-center">
                <div class="input-group" style="min-width: 320px;">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control border-start-0"
                        placeholder="{{ __('app.Search student or item...') }}">
                </div>
                <button class="btn btn-primary">{{ __('app.search') }}</button>
                <a href="{{ url()->current() }}" class="btn btn-danger">{{ __('app.reset') }}</a>
            </form>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-4 w-100">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-secondary small">
                        {{ __('app.Total Late Returns') }}: {{ $lateReturns->total() }}
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0 w-100 table-hover" >
                        <thead>
                            <tr class="text-secondary small">
                                <th style="width:60px;">#</th>
                                <th>{{ __('app.students') }}</th>
                                <th class="text-center" style="width:140px;">
                                    <div class="dropdown d-inline-block">
                                        <a class="text-dark text-decoration-none fw-semibold dropdown-toggle" href="#"
                                            data-bs-toggle="dropdown">
                                            Late Times
                                        </a>
                                        <ul class="dropdown-menu shadow border-0 rounded-3" style="min-width:160px;">
                                            <li>
                                                <a class="dropdown-item {{ !request('late_sort') && !request('late_times') ? 'fw-bold' : '' }}"
                                                    href="{{ request()->fullUrlWithQuery(['late_sort' => null, 'late_times' => null]) }}">
                                                    Default
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item {{ request('late_sort') === 'asc' ? 'fw-bold' : '' }}"
                                                    href="{{ request()->fullUrlWithQuery(['late_sort' => 'asc']) }}">
                                                    <i class="bi bi-sort-numeric-up me-1"></i> Ascending
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item {{ request('late_sort') === 'desc' ? 'fw-bold' : '' }}"
                                                    href="{{ request()->fullUrlWithQuery(['late_sort' => 'desc']) }}">
                                                    <i class="bi bi-sort-numeric-down me-1"></i> Descending
                                                </a>
                                            </li>
                                           
                                        </ul>
                                    </div>
                                </th>
                                <th>{{ __('app.item') }}</th>
                                <th class="text-center" style="width:160px;">{{ __('app.borrow_date') }}</th>
                                <th class="text-center" style="width:160px;">{{ __('app.return_date') }}</th>
                                <th class="text-center" style="width:130px;">{{ __('app.Total Days late') }}</th>
                                <th class="text-center" style="width:190px;">{{ __('app.Call history') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($lateReturns as $k => $b)
                                @php
                                    $borrow = \Carbon\Carbon::parse($b->borrow_date);
                                    $return = \Carbon\Carbon::parse($b->return_date);
                                    $totalDays = ceil($borrow->diffInHours($return) / 24);
                                    $callStatus = $b->call_status ?? 'not_yet_called';

                                    $callHistories = \App\Models\ItemHistory::where('borrow_id', $b->id)
                                        ->where('action', 'Called')
                                        ->with('user')
                                        ->latest('action_at')
                                        ->get();
                                    $lateCount = \App\Models\Borrow::where('student_id', $b->student_id)
                                        ->whereNotNull('return_date')
                                        ->whereRaw('TIMESTAMPDIFF(HOUR, borrow_date, return_date) >= 48')
                                        ->count();
                                @endphp

                                <tr>
                                    <td class="text-secondary">{{ $lateReturns->firstItem() + $k }}</td>

                                    <td class="fw-semibold">{{ $b->student->student_name ?? '—' }}</td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill"
                                            style="font-size:12px; padding: 6px 14px;
                                            background-color: {{ $lateCount >= 3 ? '#dc3545' : ($lateCount == 2 ? '#fd7e14' : '#0d6efd') }}; 
                                            color:white;">
                                            {{ $lateCount }} {{ $lateCount == 1 ? 'time' : 'times' }}
                                        </span>
                                    </td>
                                    <td>{{ $b->item->display_name ?? '—' }}</td>

                                    <td class="text-center">{{ $borrow->format('d M Y H:i') }}</td>

                                    <td class="text-center">{{ $return->format('d M Y H:i') }}</td>

                                    <td class="text-center">
                                        <span class="badge bg-warning-subtle text-warning-emphasis fw-semibold">
                                            {{ $totalDays }} {{ __('app.days') }}
                                        </span>
                                    </td>

                                    {{-- ── Call History cell ── --}}
                                    <td class="text-center">
                                        @if ($callHistories->count() > 0)
                                            <button type="button" class="btn btn-sm btn-call-history mt-2"
                                                data-bs-toggle="modal"
                                                data-bs-target="#callHistoryModalLate{{ $b->id }}">
                                                <i class="bi bi-clock-history me-1"></i>
                                                {{ __('app.Call history') }} ({{ $callHistories->count() }})
                                            </button>
                                        @else
                                            <span class="text-muted small">
                                                <i class="bi bi-telephone-x me-1"></i>No call record
                                            </span>
                                        @endif
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-danger py-3">
                                        No late returns found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 d-flex justify-content-end">
                    {{ $lateReturns->onEachSide(1)->links('vendor.pagination.adminlte-simple') }}
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         Call History Modals — rendered after the table, one per row
    ══════════════════════════════════════════════════════════════ --}}
    @foreach ($lateReturns as $b)
        @php
            $callHistories = \App\Models\ItemHistory::where('borrow_id', $b->id)
                ->where('action', 'Called')
                ->with('user')
                ->latest('action_at')
                ->get();

            $lateDays = ceil(
                \Carbon\Carbon::parse($b->borrow_date)->diffInHours(\Carbon\Carbon::parse($b->return_date)) / 24,
            );
        @endphp

        @if ($callHistories->count() > 0)
            <div class="modal fade" id="callHistoryModalLate{{ $b->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow rounded-4">

                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold">
                                <i class="bi bi-clock-history me-2"></i>{{ __('app.Call history') }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        {{-- Student / item summary card --}}
                        <div class="mx-3 mt-2 p-3 rounded-4 d-flex align-items-center gap-3" style="background:#f8f9fa;">
                            <div class="d-flex align-items-center justify-content-center rounded-circle bg-warning bg-opacity-10 text-warning fw-bold"
                                style="width:44px; height:44px; font-size:18px;">
                                {{ mb_substr($b->student->student_name ?? '?', 0, 1) }}
                            </div>
                            <div>
                                <div class="fw-semibold">{{ $b->student->student_name ?? '-' }}</div>
                                <div class="text-muted small">
                                    {{ $b->student->phone_number ?? '-' }} ·
                                    {{ $b->item->name ?? '-' }} ·
                                    <span class="text-warning fw-semibold">
                                        {{ $lateDays }} {{ __('app.days') }} late
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Timeline --}}
                        <div class="modal-body pt-3">
                            <div class="position-relative" style="padding-left: 24px;">
                                <div class="position-absolute"
                                    style="left:7px; top:8px; bottom:8px; width:2px; background:#e5e7eb;"></div>

                                @foreach ($callHistories as $ch)
                                    @php
                                        $dotColor = '#6B7280';
                                        $statusText = 'Called';

                                        if (str_contains($ch->details, 'Called Done')) {
                                            $dotColor = '#10B981';
                                            $statusText = 'Called Done';
                                        } elseif (str_contains($ch->details, 'No Answer')) {
                                            $dotColor = '#F59E0B';
                                            $statusText = 'No Answer';
                                        } elseif (str_contains($ch->details, 'Wrong Number')) {
                                            $dotColor = '#EF4444';
                                            $statusText = 'Wrong Number';
                                        }

                                        $note = null;
                                        if (str_contains($ch->details, '| Note: ')) {
                                            $note = substr($ch->details, strpos($ch->details, '| Note: ') + 8);
                                        }
                                    @endphp

                                    <div class="position-relative" style="padding-bottom: 20px;">
                                        <div class="position-absolute rounded-circle border border-2 border-white"
                                            style="left:-20px; top:4px; width:12px; height:12px; background:{{ $dotColor }};">
                                        </div>

                                        <div class="fw-semibold small">{{ $statusText }}</div>
                                        <div class="text-muted" style="font-size:12px;">
                                            by {{ $ch->user->name ?? '-' }}
                                        </div>
                                        <div class="text-muted" style="font-size:11px;">
                                            {{ $ch->action_at?->timezone('Asia/Phnom_Penh')->format('d M Y, H:i') }}
                                        </div>

                                        @if ($note)
                                            <div class="mt-1 small text-muted px-2 py-1 rounded"
                                                style="background:#f1f3f5; border-left:3px solid {{ $dotColor }};">
                                                {{ $note }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-light"
                                data-bs-dismiss="modal">{{ __('app.close') }}</button>
                        </div>

                    </div>
                </div>
            </div>
        @endif
    @endforeach

@endsection
