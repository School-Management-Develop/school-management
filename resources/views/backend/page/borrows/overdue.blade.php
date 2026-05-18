@extends('backend.layout.master')

@section('title', 'Overdue Borrow')
@section('overdue_borrow_active', 'active')
@section('contents')

    <style>
        td.datetime {
            white-space: nowrap;
            line-height: 1.3;
            vertical-align: middle;
        }

        td.datetime .date {
            display: block;
            font-size: 14px;
        }

        td.datetime .time {
            display: block;
            font-size: 12px;
            color: #6c757d;
            margin-top: 2px;
        }

        @media (max-width: 576px) {
            td.datetime .date {
                font-size: 13px;
            }

            td.datetime .time {
                font-size: 11px;
            }
        }
        .btn-call-history {
    font-size: 12px;
    padding: 2px 8px;
    border-color: #dee2e6;
    color: #6c757d;
    transition: all 0.2s;
}

.btn-call-history:hover {
    background: #0d6efd;
    border-color: #0d6efd;
    color: #fff;
}
    </style>

    <div class="container-fluid" style="padding:3%;">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div>
                <h2 class="fw-bold mb-1">{{ __('app.overdue_borrow') }}</h2>
                <div class="text-secondary">{{ __('app.Borrowed more than 3 days and still not returned.') }}</div>
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

        @include('backend.page.alerts.alert')

        <div class="card border-0 shadow-sm rounded-4 mb-4 w-100">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 w-100 table-hover">
                        <thead>
                            <tr class="text-secondary small">
                                <th style="width:80px;">#</th>
                                <th>{{ __('app.students') }}</th>
                                <th style="width:180px;">{{ __('app.Phone Number') }}</th>
                                <th>{{ __('app.item') }}</th>
                                <th class="text-center" style="width:160px;">{{ __('app.borrow_date') }}</th>
                                <th class="text-center" style="width:130px;">{{ __('app.Total Days late') }}</th>
                                <th class="text-center" style="width:150px;">{{ __('app.Call Status') }}</th>
                                <th style="width:190px;">{{ __('app.Call Note') }}</th>
                                <th style="width:260px;" class="d-none d-md-table-cell">{{ __('app.Update') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($overdues as $k => $b)
                                @php
                                    $lateDays = (int) \Carbon\Carbon::parse($b->borrow_date)->diffInDays(now());
                                    $callStatus = $b->call_status ?? 'not_yet_called';
                                    $modalId = 'updateModal' . $b->id;
                                    $callHistories = \App\Models\ItemHistory::where('borrow_id', $b->id)
                                        ->where('action', 'Called')
                                        ->with('user')
                                        ->latest('action_at')
                                        ->get();
                                @endphp

                                <tr>
                                    <td class="text-secondary">{{ $overdues->firstItem() + $k }}</td>

                                    <td class="fw-semibold">
                                        {{ $b->student->student_name ?? '—' }}
                                    </td>

                                    <td>
                                        <div>{{ $b->student->phone_number ?? '—' }}</div>

                                        @if (!empty($b->student->phone_number))
                                            <a href="tel:{{ $b->student->phone_number }}"
                                                class="btn btn-sm btn-outline-primary mt-2">
                                                <i class="bi bi-telephone"></i> Call
                                            </a>
                                        @endif
                                    </td>

                                    <td>
                                        {{ $b->item->Itemname ?? ($b->item->item_name ?? ($b->item->name ?? '—')) }}
                                    </td>

                                    <td class="datetime text-center">
                                        <span
                                            class="date">{{ \Carbon\Carbon::parse($b->borrow_date)->format('d M Y') }}</span>
                                        <span
                                            class="time">{{ \Carbon\Carbon::parse($b->borrow_date)->format('H:i') }}</span>
                                    </td>

                                    <td class="text-center">
                                        <span class="badge bg-danger-subtle text-danger border border-danger">
                                            {{ $lateDays }} days
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        @if ($callStatus === 'not_yet_called')
                                            <span
                                                class="badge rounded-pill bg-secondary">{{ __('app.Not yet call') }}</span>
                                        @elseif($callStatus === 'called_done')
                                            <span class="badge rounded-pill bg-success">{{ __('app.Call done') }}</span>
                                        @elseif($callStatus === 'no_answer')
                                            <span
                                                class="badge rounded-pill bg-warning text-dark">{{ __('app.No answer') }}</span>
                                        @endif

                                        {{-- @if ($b->called_at)
                                            <div class="small text-muted mt-1">
                                                {{ $b->called_at->format('d M Y H:i') }}
                                            </div>
                                        @endif

                                        @if ($b->calledByUser)
                                            <div class="small text-muted">
                                                by {{ $b->calledByUser->name }}
                                            </div>
                                        @endif --}}

                                        @if ($callHistories->count() > 0)
    <button type="button" class="btn btn-sm btn-call-history mt-2"
        data-bs-toggle="modal"
        data-bs-target="#callHistoryModal{{ $b->id }}">
        <i class="bi bi-clock-history me-1"></i>{{ __('app.Call history') }} ({{ $callHistories->count() }})
    </button>
@endif
                                    </td>

                                    <td>{{ $b->call_note ?: '—' }}</td>

                                    <td class="d-none d-md-table-cell">
                                        <form method="POST" action="{{ route('borrows.overdue.call-status', $b->id) }}">
                                            @csrf
                                            @method('PATCH')

                                            <select name="call_status" class="form-select form-select-sm mb-2">
                                                <option value="not_yet_called"
                                                    {{ $callStatus === 'not_yet_called' ? 'selected' : '' }}>
                                                    {{ __('app.Not yet call') }}
                                                </option>
                                                <option value="called_done"
                                                    {{ $callStatus === 'called_done' ? 'selected' : '' }}>
                                                    {{ __('app.Call done') }}
                                                </option>
                                                <option value="no_answer"
                                                    {{ $callStatus === 'no_answer' ? 'selected' : '' }}>
                                                    {{ __('app.No answer') }}
                                                </option>
                                            </select>

                                            <textarea name="call_note" class="form-control form-control-sm mb-2" rows="2"
                                                placeholder="{{ __('app.Call Note') }}"></textarea>

                                            <button type="submit" class="btn btn-sm btn-primary w-100">
                                                <i class="bi bi-save"></i> Save
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                <tr class="bg-light d-md-none">
                                    <td colspan="9" class="py-2">
                                        <button type="button" class="btn btn-sm btn-primary w-100" data-bs-toggle="modal"
                                            data-bs-target="#{{ $modalId }}">
                                            <i class="bi bi-pencil"></i> {{ __('app.Update Call Status') }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-danger py-3">
                                        No overdue borrows
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 d-flex justify-content-end">
                    {{ $overdues->onEachSide(1)->links('vendor.pagination.adminlte-simple') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Update Call Status Modals (mobile) --}}
    @foreach ($overdues as $b)
        @php
            $lateDays = (int) \Carbon\Carbon::parse($b->borrow_date)->diffInDays(now());
            $callStatus = $b->call_status ?? 'not_yet_called';
            $modalId = 'updateModal' . $b->id;
        @endphp

        <div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="{{ $modalId }}Label">
                            {{ __('app.Update Call Status') }} - {{ $b->student->student_name ?? 'Unknown' }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form method="POST" action="{{ route('borrows.overdue.call-status', $b->id) }}">
                        @csrf
                        @method('PATCH')

                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="call_status_{{ $b->id }}"
                                    class="form-label">{{ __('app.Call Status') }}</label>
                                <select name="call_status" id="call_status_{{ $b->id }}" class="form-select">
                                    <option value="not_yet_called"
                                        {{ $callStatus === 'not_yet_called' ? 'selected' : '' }}>
                                        {{ __('app.Not yet call') }}
                                    </option>
                                    <option value="called_done" {{ $callStatus === 'called_done' ? 'selected' : '' }}>
                                        {{ __('app.Call done') }}
                                    </option>
                                    <option value="no_answer" {{ $callStatus === 'no_answer' ? 'selected' : '' }}>
                                        {{ __('app.Called - No answer') }}
                                    </option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="call_note_{{ $b->id }}"
                                    class="form-label">{{ __('app.Call Note') }}</label>
                                <textarea name="call_note" id="call_note_{{ $b->id }}" class="form-control" rows="4"
                                    placeholder="Write note...">{{ old('call_note', $b->call_note) }}</textarea>
                            </div>

                            <div class="alert alert-info alert-sm mb-0">
                                <small>
                                    <strong>{{ __('app.students') }}:</strong> {{ $b->student->student_name ?? '—' }}<br>
                                    <strong>{{ __('app.items') }}:</strong>
                                    {{ $b->item->Itemname ?? ($b->item->item_name ?? ($b->item->name ?? '—')) }}<br>
                                    <strong>{{ __('app.Total Days late') }}:</strong> {{ $lateDays }}
                                    {{ __('app.days') }}
                                </small>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{ __('app.Cancel') }}</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> {{ __('app.Save Changes') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Call History Modals --}}
    @foreach ($overdues as $b)
        @php
            $callHistories = \App\Models\ItemHistory::where('borrow_id', $b->id)
                ->where('action', 'Called')
                ->with('user')
                ->latest('action_at')
                ->get();
            $lateDays = (int) \Carbon\Carbon::parse($b->borrow_date)->diffInDays(now());
        @endphp

        @if ($callHistories->count() > 0)
            <div class="modal fade" id="callHistoryModal{{ $b->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow rounded-4">

                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold">
                                <i class="bi bi-clock-history me-2"></i>{{ __('app.Call history') }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        {{-- Student info card --}}
                        <div class="mx-3 mt-2 p-3 rounded-4 d-flex align-items-center gap-3" style="background:#f8f9fa;">
                            <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary fw-bold"
                                style="width:44px; height:44px; font-size:18px;">
                                {{ mb_substr($b->student->student_name ?? '?', 0, 1) }}
                            </div>
                            <div>
                                <div class="fw-semibold">{{ $b->student->student_name ?? '-' }}</div>
                                <div class="text-muted small">
                                    {{ $b->student->phone_number ?? '-' }} ·
                                    {{ $b->item->name ?? ($b->item->Itemname ?? '-') }} ·
                                    <span class="text-danger">{{ $lateDays }} {{ __('app.days') }}
                                        {{ __('app.overdue') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="modal-body pt-3">
                            <div class="position-relative" style="padding-left: 24px;">
                                <div class="position-absolute"
                                    style="left:7px; top:8px; bottom:8px; width:2px; background:#e5e7eb;"></div>

                                @foreach ($callHistories as $ch)
                                    @php
                                        $dotColor = '#6B7280';
                                        if (str_contains($ch->details, 'Called Done')) {
                                            $dotColor = '#10B981';
                                        } elseif (str_contains($ch->details, 'No Answer')) {
                                            $dotColor = '#F59E0B';
                                        } elseif (str_contains($ch->details, 'Wrong Number')) {
                                            $dotColor = '#EF4444';
                                        }

                                        $statusText = 'Called';
                                        if (str_contains($ch->details, 'Called Done')) {
                                            $statusText = 'Called Done';
                                        } elseif (str_contains($ch->details, 'No Answer')) {
                                            $statusText = 'No Answer';
                                        } elseif (str_contains($ch->details, 'Wrong Number')) {
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
