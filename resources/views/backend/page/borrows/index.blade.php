@php
    use Illuminate\Support\Facades\Auth;

    $user = Auth::user();
    $role = strtolower($user->role ?? '');

    $canManageUsers = in_array($role, ['admin', 'super admin', 'superadmin']);
@endphp
@extends('backend.layout.master')

@section('title', 'Borrows')
@section('borrow_active', 'active')

@section('contents')
    <style>
        /* modal header cleaner */
        .modal-header {
            border-bottom: none;
            padding-bottom: 0;
        }

        /* modal title */
        .modal-title {
            font-weight: 700;
            font-size: 20px;
        }

        /* soft card sections */
        .modal-body .card {
            border-radius: 12px;
            background: #f8f9fb;
        }

        /* section titles */
        .modal-body h6 {
            font-weight: 600;
            font-size: 14px;
            color: #555;
        }

        /* note boxes */
        .modal-body .border {
            background: #f8f9fb;
            border-radius: 8px !important;
        }

        /* spacing for labels */
        .modal-body small {
            display: block;
            margin-bottom: 4px;
        }

        /* status badge style */
        .modal-body .badge {
            font-size: 13px;
            padding: 6px 10px;
        }

        .ajax-message-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-width: 380px;
        }

        .ajax-toast {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
            color: #1f2937;
            background: #fff;
            border-left: 5px solid #22c55e;
            animation: slideInRight 0.35s ease;
            overflow: hidden;
            position: relative;
        }

        .ajax-toast.success {
            border-left-color: #22c55e;
            background: linear-gradient(135deg, #f0fdf4, #ffffff);
        }

        .ajax-toast.error {
            border-left-color: #ef4444;
            background: linear-gradient(135deg, #fef2f2, #ffffff);
        }

        .ajax-toast .icon {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: bold;
        }

        .ajax-toast.success .icon {
            background: #dcfce7;
            color: #16a34a;
        }

        .ajax-toast.error .icon {
            background: #fee2e2;
            color: #dc2626;
        }

        .ajax-toast .content {
            flex: 1;
        }

        .ajax-toast .title {
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 2px;
        }

        .ajax-toast .message {
            font-size: 13px;
            color: #4b5563;
            margin: 0;
        }

        .ajax-toast .close-btn {
            border: none;
            background: transparent;
            font-size: 18px;
            line-height: 1;
            color: #9ca3af;
            cursor: pointer;
            padding: 0;
        }

        .ajax-toast .close-btn:hover {
            color: #111827;
        }

        .ajax-toast .progress {
            position: absolute;
            left: 0;
            bottom: 0;
            height: 3px;
            width: 100%;
            background: rgba(0, 0, 0, 0.06);
        }

        .ajax-toast.success .progress-bar {
            height: 100%;
            background: #22c55e;
            animation: shrinkBar 3.5s linear forwards;
        }

        .ajax-toast.error .progress-bar {
            height: 100%;
            background: #ef4444;
            animation: shrinkBar 3.5s linear forwards;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px) scale(0.96);
            }

            to {
                opacity: 1;
                transform: translateX(0) scale(1);
            }
        }

        @keyframes fadeOutToast {
            to {
                opacity: 0;
                transform: translateX(30px) scale(0.96);
            }
        }

        @keyframes shrinkBar {
            from {
                width: 100%;
            }

            to {
                width: 0%;
            }
        }

        @media (max-width: 576px) {
            .ajax-message-container {
                top: 15px;
                right: 15px;
                left: 15px;
                max-width: unset;
            }
        }
    </style>
    <div class="container-fluid" style="padding: 3% 1%">

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div id="ajaxMessage" class="ajax-message-container"></div>
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
            <div>
                <h2 class="mb-1">{{ __('app.borrows') }}</h2>
                <p class="text-muted mb-0">{{ __('app.Borrow and return items from IT room.') }}</p>
            </div>

            <div class="d-flex gap-2">
                @if ($canManageUsers)
                    <button class="btn btn-success d-flex align-items-center gap-2" data-bs-toggle="modal"
                        data-bs-target="#restoreBorrowModal">
                        <span class="fs-5"><i class="fa-sharp fa-thin fa-trash-undo"></i></span> {{ __('app.restore_item') }}
                    </button>
                @endif

                <button class="btn btn-dark d-flex align-items-center gap-2" data-bs-toggle="modal"
                    data-bs-target="#borrowModal">
                    <span class="fs-5">+</span> {{ __('app.borrow_item') }}
                </button>

                <button class="btn btn-outline-dark d-flex align-items-center gap-2" data-bs-toggle="modal"
                    data-bs-target="#returnModal">
                    <i class="bi bi-box-arrow-in-down"></i> {{ __('app.return_item') }}
                </button>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <small class="text-muted">{{ __('app.total_borrows') }}</small>
                        <h3 class="mb-0">{{ $stats['total_records'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <small class="text-muted">{{ __('app.active_borrows') }}</small>
                        <h3 class="mb-0">{{ $stats['active_records'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <small class="text-muted">{{ __('app.return_item') }}</small>
                        <h3 class="mb-0">{{ $stats['returned_records'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">

                <div class="justify-content-end mb-3">
                    <form method="GET" action="{{ route('borrows.index') }}" class="row g-2 align-items-end mb-3">
                        <div class="col-md-3">
                            <label class="form-label small text-muted mb-1">{{ __('app.find_by') }}</label>
                            <select name="filter" class="form-select" onchange="this.form.submit()">
                                {{-- <option value="">{{ __('app.find_by') }}</option> --}}
                                <option value="group_name" {{ request('filter', 'group_name') == 'group_name' ? 'selected' : '' }}>
                                    {{ __('app.Search by group name...') }}
                                </option>
                                <option value="student_name" {{ request('filter') == 'student_name' ? 'selected' : '' }}>
                                    {{ __('app.search_by_student_name') }}
                                </option>

                                <option value="item_name" {{ request('filter') == 'item_name' ? 'selected' : '' }}>
                                    {{ __('app.Search by item name...') }}
                                </option>

                                <option value="status" {{ request('filter') == 'status' ? 'selected' : '' }}>
                                    {{ __('app.borrow_status') }}
                                </option>
                            </select>
                        </div>

                        @if (request('filter') == 'student_name')
                            <div class="col-md-4">
                                <label
                                    class="form-label small text-muted mb-1">{{ __('app.Search student name...') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        class="form-control" placeholder="{{ __('app.Search student name...') }}">
                                </div>
                            </div>
                        @endif
                        @if (request('filter') == 'item_name')
                            <div class="col-md-4">
                                <label class="form-label small text-muted mb-1">{{ __('app.items') }}</label>
                                <select name="item_id" class="form-select" onchange="this.form.submit()">
                                    <option value="">{{ __('app.all') }}</option>
                                    @foreach ($items as $it)
                                        <option value="{{ $it->Itemid }}"
                                            {{ (string) request('item_id') === (string) $it->Itemid ? 'selected' : '' }}>
                                            {{ $it->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        @if (request('filter', 'group_name') == 'group_name')
                            <div class="col-md-4">
                                <label
                                    class="form-label small text-muted mb-1">{{ __('app.Search by group name...') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        class="form-control" placeholder="{{ __('app.Search by group name...') }}">
                                </div>
                            </div>
                        @endif

                        @if (request('filter') == 'status')
                            <div class="col-md-3">
                                <label class="form-label small text-muted mb-1">{{ __('app.borrow_status') }}</label>
                                <select name="status" class="form-select" onchange="this.form.submit()">
                                    <option value="">{{ __('app.all') }}</option>
                                    <option value="BORROWED" {{ request('status') == 'BORROWED' ? 'selected' : '' }}>
                                        {{ __('app.item_borrowed') }}
                                    </option>
                                    <option value="RETURNED" {{ request('status') == 'RETURNED' ? 'selected' : '' }}>
                                        {{ __('app.returned') }}
                                    </option>
                                    <option value="OVERDUE" {{ request('status') == 'OVERDUE' ? 'selected' : '' }}>
                                        {{ __('app.overdue_borrow') }}
                                    </option>
                                </select>
                            </div>
                        @endif

                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary">{{ __('app.search') }}</button>
                            <a href="{{ route('borrows.index') }}" class="btn btn-danger">{{ __('app.reset') }}</a>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="border-bottom">
                            <tr>
                                <th style="width:60px;">#</th>
                                <th>{{ __('app.students') }}</th>
                                <th>{{ __('app.Gender') }}</th>
                                <th>{{ __('app.groups') }}</th>
                                <th>{{ __('app.item') }}</th>
                                <th>{{ __('app.qty') }}</th>
                                <th>{{ __('app.borrow_date') }}</th>
                                <th>{{ __('app.return_date') }}</th>
                                <th>{{ __('app.borrow_status') }}</th>
                                <th class="text-end" style="width:200px;">{{ __('app.Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($borrows ?? [] as $i => $borrow)
                                <tr class="border-bottom" id="borrow-row-{{ $borrow->id }}">
                                   <td>{{ ($borrows->currentPage() - 1) * $borrows->perPage() + $i + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $borrow->student->student_name ?? 'N/A' }}</div>

                                    </td>
                                    <td>{{ $borrow->student->gender ?? 'N/A' }}</td>
                                    <td>{{ $borrow->student->group->group_name ?? 'N/A' }}</td>

                                    <td>
                                        <div class="fw-semibold">{{ $borrow->item->display_name ?? 'N/A' }}</div>
                                    </td>

                                    <td>{{ $borrow->qty ?? 1 }}</td>
                                    <style>
                                        td.datetime {
                                            white-space: nowrap;
                                            line-height: 1.3;
                                        }

                                        td.datetime .date {
                                            display: block;
                                            font-size: 14px;
                                        }

                                        td.datetime .time {
                                            display: block;
                                            font-size: 12px;
                                            color: #000000;
                                            margin-top: 2px;
                                        }

                                        @media (max-width: 576px) {
                                            td.datetime .date {
                                                font-size: 13px;
                                            }

                                            td.datetime .time {
                                                font-size: 13px;
                                            }
                                        }
                                    </style>

                                    <td class="datetime">
                                        @if ($borrow->borrow_date)
                                            <span
                                                class="date">{{ \Carbon\Carbon::parse($borrow->borrow_date)->format('d M Y') }}</span>
                                            <span
                                                class="time">{{ \Carbon\Carbon::parse($borrow->borrow_date)->format('H:i') }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    {{-- <td>
                                        {!! $borrow->return_date
                                            ? \Carbon\Carbon::parse($borrow->return_date)->format('d M Y') .
                                                '<br>' .
                                                \Carbon\Carbon::parse($borrow->return_date)->format('H:i')
                                            : '-' !!}
                                    </td> --}}
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
                                    </style>
                                    {{-- Return Date --}}
                                    <td class="datetime">
                                        @if ($borrow->return_date)
                                            <span
                                                class="date">{{ \Carbon\Carbon::parse($borrow->return_date)->format('d M Y') }}</span>
                                            <span
                                                class="time">{{ \Carbon\Carbon::parse($borrow->return_date)->format('H:i') }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $st = $borrow->status ?? 'BORROWED';
                                            $cls = match ($st) {
                                                'RETURNED' => 'success',
                                                'BORROWED' => 'warning',
                                                'OVERDUE' => 'danger',
                                                default => 'secondary',
                                            };
                                        @endphp
                                        <span class="badge rounded-pill bg-{{ $cls }} px-3 py-2">
                                            {{ ucfirst(strtolower($st)) }}
                                        </span>
                                    </td>

                                    <td class="text-end">

                                        @if ($borrow->status !== 'RETURNED')
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                                data-bs-target="#editBorrowModal" data-id="{{ $borrow->id }}"
                                                data-student="{{ $borrow->student_id }}"
                                                data-item="{{ $borrow->item_id }}" data-qty="{{ $borrow->qty }}" data-notes="{{ $borrow->notes }}"
                                                style="margin-bottom: 5px">
                                                Edit
                                            </button>
                                        @endif

                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                            data-bs-toggle="modal" data-bs-target="#borrowDetailModal"
                                            data-student="{{ $borrow->student->student_name ?? '-' }}"
                                            data-item="{{ $borrow->item->display_name ?? '-' }}"
                                            data-qty="{{ $borrow->qty ?? '-' }}"
                                            data-status="{{ $borrow->status ?? '-' }}"
                                            data-condition="{{ $borrow->condition ?? '-' }}"
                                            data-borrow-date="{{ optional($borrow->borrow_date)->format('d M Y H:i') ?? '-' }}"
                                            data-due-date="{{ optional($borrow->due_date)->format('d M Y H:i') ?? '-' }}"
                                            data-return-date="{{ optional($borrow->return_date)->format('d M Y H:i') ?? '-' }}"
                                            data-approved-by="{{ $borrow->approvedByUser->name ?? '-' }}"
                                            data-returned-by="{{ $borrow->returnedByUser->name ?? '-' }}"
                                            data-notes="{{ $borrow->notes ?? '-' }}"
                                            data-return-notes="{{ $borrow->return_notes ?? '-' }}"
                                            style="margin-bottom: 5px">
                                            View
                                        </button>
                                        @php
                                            $status = $borrow->status ?? 'BORROWED';
                                        @endphp

                                        @if (in_array($status, ['BORROWED', 'OVERDUE']))
                                            <button class="btn btn-sm btn-outline-dark" data-bs-toggle="modal"
                                                data-bs-target="#returnModal" data-borrow-id="{{ $borrow->id }}"
                                                style="margin-bottom: 5px">
                                                Return
                                            </button>

                                            <form action="{{ route('borrows.destroy', $borrow->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Delete this borrow record?')">
                                                    Delete
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('borrows.undoReturn', $borrow->id) }}" method="POST"
                                                class="d-inline undo-return-form">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-warning"
                                                    style="margin-bottom: 5px">
                                                    Undo
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        No borrow records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-end mt-3">
                        {{ $borrows->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="borrowModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('app.borrow_item') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('borrows.borrow') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('app.students') }} <span
                                        class="text-danger">*</span></label>

                                <input type="text" id="student_search" class="form-control" list="students_list"
                                    placeholder="Type student name..." autocomplete="off" required>

                                <datalist id="students_list">
                                    @foreach ($students as $s)
                                        <option value="{{ $s->student_name }}" data-id="{{ $s->student_id }}"></option>
                                    @endforeach
                                </datalist>

                                <input type="hidden" name="student_id" id="student_id" required>
                                {{-- <small class="text-muted">Start typing, then select from suggestions.</small> --}}
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ __('app.item') }} <span class="text-danger">*</span></label>
                                <select name="item_id" class="form-select" required>
                                    <option value="">-- {{ __('app.select_item') }} --</option>
                                    @foreach ($items as $it)
                                        <option value="{{ $it->Itemid }}">{{ $it->display_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- <div class="col-md-6">
                            <label class="form-label">Due Date <small class="text-muted">(optional)</small></label>
                            <input type="date" name="due_date" class="form-control">
                        </div> --}}

                            <div class="col-md-6">
                                <label class="form-label">Qty <span class="text-danger">*</span></label>
                                <select name="qty" class="form-select" required>
                                    <option value="1" selected>1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <input type="text" class="form-control" value="BORROWED" disabled>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder=""></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-dark">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="returnModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('app.return_item') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="returnForm" action="{{ route('borrows.return') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">{{ __('app.select_borrow_record') }} <span
                                        class="text-danger">*</span></label>
                                <select name="borrow_id" id="return_borrow_id" class="form-select" required>
                                    <option value="">-- {{ __('app.select_active_borrow') }} --</option>
                                    @foreach ($activeBorrows as $b)
                                        <option value="{{ $b->id }}">
                                            {{ $b->student->student_name ?? 'N/A' }} -
                                            {{ $b->item->display_name ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                </select>
                                {{-- <small class="text-muted">Only active (BORROWED / OVERDUE) records shown.</small> --}}
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ __('app.return_date') }}</label>
                                <input type="datetime-local" name="return_date" class="form-control"
                                    value="{{ now('Asia/Jakarta')->format('Y-m-d\TH:i') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ __('app.item_condition') }}</label>
                                <select name="condition" class="form-select" required>
                                    <option value="Good">{{ __('app.item_condition_good') }}</option>
                                    <option value="Slightly Damaged">{{ __('app.item_condition_slightly_damaged') }}
                                    </option>
                                    <option value="Damaged">{{ __('app.item_condition_damaged') }}</option>
                                    <option value="Lost">{{ __('app.item_condition_lost') }}</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">{{ __('app.return_notes') }}</label>
                                <textarea name="return_notes" class="form-control" rows="3" placeholder=""></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light"
                            data-bs-dismiss="modal">{{ __('app.Cancel') }}</button>
                        <button type="submit" class="btn btn-dark">{{ __('app.save_return') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="borrowDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-box-seam me-2"></i> {{ __('app.borrow_details') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-4 py-4">

                    <!-- Student + Item -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <small class="text-muted">{{ __('app.students') }}</small>
                            <div class="fs-4 fw-bold text-dark js-student"></div>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted">{{ __('app.item') }}</small>
                            <div class="fs-4 fw-bold js-item"></div>
                        </div>
                    </div>

                    <!-- Borrow Info -->
                    <div class="card border-0 bg-light mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">{{ __('app.borrow_information') }}</h6>

                            <div class="row">
                                <div class="col-md-3">
                                    <small class="text-muted">{{ __('app.qty') }}</small>
                                    <div class="fw-semibold js-qty"></div>
                                </div>

                                <div class="col-md-3">
                                    <small class="text-muted">{{ __('app.borrow_status') }}</small>
                                    <div>
                                        <span class="badge bg-primary js-status"></span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <small class="text-muted">{{ __('app.item_condition') }}</small>
                                    <div class="fw-semibold js-condition"></div>
                                </div>

                                <div class="col-md-3">
                                    <small class="text-muted">{{ __('app.approved_by') }}</small>
                                    <div class="fw-semibold js-approved-by"></div>
                                </div>
                                <div class="mt-4">
                                    <small class="text-muted">{{ __('app.returned_by') }}</small>
                                    <div class="fw-semibold js-returned-by"></div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Time Info -->
                    <div class="card border-0 bg-light mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-4">Borrow Timeline</h6>

                            <div class="timeline">

                                <div class="timeline-item">
                                    <div class="timeline-icon bg-primary"></div>
                                    <div class="timeline-content">
                                        <div class="fw-semibold">Borrowed</div>
                                        <small class="text-muted js-borrow-date"></small>
                                    </div>
                                </div>

                                <div class="timeline-item d-none">
                                    <div class="timeline-icon bg-warning"></div>
                                    <div class="timeline-content">
                                        <div class="fw-semibold">Due Date</div>
                                        <small class="text-muted js-due-date"></small>
                                    </div>
                                </div>

                                <div class="timeline-item">
                                    <div class="timeline-icon bg-success"></div>
                                    <div class="timeline-content">
                                        <div class="fw-semibold">Returned</div>
                                        <small class="text-muted js-return-date"></small>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">Notes</small>
                            <div class="border rounded p-3 bg-light js-notes"></div>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted">Return Notes</small>
                            <div class="border rounded p-3 bg-light js-return-notes"></div>
                        </div>
                    </div>

                    <!-- Returned By -->


                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('app.close') }}</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editBorrowModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('app.edit_borrow') }}</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="editBorrowForm" method="POST" action="{{ route('borrows.update') }}">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="borrow_id" id="edit_borrow_id">

                    <div class="modal-body">

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">{{ __('app.students') }}</label>
                                <select name="student_id" id="edit_student_id" class="form-select" required>
                                    @foreach ($students as $s)
                                        <option value="{{ $s->student_id }}">
                                            {{ $s->student_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ __('app.items') }}</label>
                                <select name="item_id" id="edit_item_id" class="form-select" required>
                                    @foreach ($items as $it)
                                        <option value="{{ $it->Itemid }}"
                                            @if ($it->qty == 0) disabled @endif>

                                            {{ $it->display_name }}

                                            <!-- @if ($it->qty == 0)
                                                (Out of stock)
                                            @else
                                                (Stock: {{ $it->qty }})
                                            @endif -->

                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">

                                <label class="form-label">Qty</label>
                                {{-- <input type="number" name="qty" id="edit_qty" class="form-control" min="1"
                                    required> --}}
                                <select name="qty" id="edit_qty" class="form-select">
                                    <option value="">Select Quantity</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">note</label>
                                <input type="text" name="notes" id="edit_note" class="form-control">
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-dark">Update</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

        @include('backend.page.borrows.restoreBorrowModal')

    {{-- <div class="modal fade" id="restoreBorrowModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">
                        <i class="bi bi-trash-fill text-danger me-2"></i>{{ __('app.restore_item') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Student</th>
                                    <th scope="col">Item</th>
                                    <th scope="col">Qty</th>
                                    <th scope="col">Borrow Date</th>
                                    <th scope="col">Return Date</th>
                                    <th scope="col">Deleted By</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row">1</th>
                                    <td>
                                        <strong>Mark Otto</strong><br>
                                        <small class="text-muted">Group A | Male</small>
                                    </td>
                                    <td>Laptop</td>
                                    <td>1</td>
                                    <td>Oct 01, 2023</td>
                                    <td>Oct 05, 2023</td>
                                    <td>
                                        <span class="text-primary">Admin_John</span><br>
                                        <small class="text-muted" style="font-size: 0.75rem;">2023-10-06 14:30</small>
                                    </td>
                                    <td><span class="badge rounded-pill bg-danger">Deleted</span></td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            
                                            <button class="btn btn-sm btn-success">
                                                <i class="bi bi-arrow-clockwise"></i> Restore
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div> --}}
    <script>
        function showAjaxMessage(message, type = 'success') {
            const container = document.getElementById('ajaxMessage');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = `ajax-toast ${type}`;

            const icon = type === 'success' ? '✓' : '!';
            const title = type === 'success' ? 'Success' : 'Error';

            toast.innerHTML = `
            <div class="icon">${icon}</div>
            <div class="content">
                <div class="title">${title}</div>
                <p class="message">${message}</p>
            </div>
            <button type="button" class="close-btn">&times;</button>
            <div class="progress">
                <div class="progress-bar"></div>
            </div>
        `;

            container.appendChild(toast);

            const removeToast = () => {
                toast.style.animation = 'fadeOutToast 0.25s ease forwards';
                setTimeout(() => toast.remove(), 250);
            };

            toast.querySelector('.close-btn').addEventListener('click', removeToast);

            setTimeout(removeToast, 3500);
        }

        function formatStatus(status) {
            if (!status) return '-';
            return status.charAt(0) + status.slice(1).toLowerCase();
        }

        function statusBadge(status) {
            let cls = 'secondary';
            if (status === 'RETURNED') cls = 'success';
            else if (status === 'BORROWED') cls = 'warning';
            else if (status === 'OVERDUE') cls = 'danger';

            return `<span class="badge rounded-pill bg-${cls} px-3 py-2">${formatStatus(status)}</span>`;
        }

        function dateCell(datetime) {
            if (!datetime || datetime === '-') return '-';

            const parts = datetime.split(' ');
            if (parts.length >= 4) {
                return `
                <span class="date">${parts[0]} ${parts[1]} ${parts[2]}</span>
                <span class="time">${parts[3]}</span>
            `;
            }

            return datetime;
        }

        function escapeHtml(text) {
            if (text === null || text === undefined) return '';
            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function buildViewButton(borrow) {
            return `
        <button type="button" class="btn btn-sm btn-outline-secondary"
            data-bs-toggle="modal"
            data-bs-target="#borrowDetailModal"
            data-student="${escapeHtml(borrow.student_name)}"
            data-item="${escapeHtml(borrow.item_name)}"
            data-qty="${escapeHtml(borrow.qty)}"
            data-status="${escapeHtml(borrow.status)}"
            data-condition="${escapeHtml(borrow.condition ?? '-')}"
            data-borrow-date="${escapeHtml(borrow.borrow_date ?? '-')}"
            data-due-date="${escapeHtml(borrow.due_date ?? '-')}"
            data-return-date="${escapeHtml(borrow.return_date ?? '-')}"
            data-approved-by="${escapeHtml(borrow.approved_by ?? '-')}"
            data-returned-by="${escapeHtml(borrow.returned_by ?? '-')}"
            data-notes="${escapeHtml(borrow.notes ?? '-')}"
            data-return-notes="${escapeHtml(borrow.return_notes ?? '-')}"
            style="margin-bottom: 5px">
            View
        </button>
    `;
        }

        function buildActionButtons(borrow) {
            if (borrow.status === 'RETURNED') {
                return `
            ${buildViewButton(borrow)}

            <form action="/admin/borrows/${borrow.id}/undo-return" method="POST" class="d-inline undo-return-form">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <button type="submit" class="btn btn-sm btn-outline-warning" style="margin-bottom: 5px">
                    Undo
                </button>
            </form>
        `;
            }

            return `
        <button class="btn btn-sm btn-outline-primary"
            data-bs-toggle="modal"
            data-bs-target="#editBorrowModal"
            data-id="${borrow.id}"
            data-student="${borrow.student_id}"
            data-item="${borrow.item_id}"
            data-qty="${borrow.qty}"
            style="margin-bottom: 5px">
            Edit
        </button>

        ${buildViewButton(borrow)}

        <button class="btn btn-sm btn-outline-dark"
            data-bs-toggle="modal"
            data-bs-target="#returnModal"
            data-borrow-id="${borrow.id}"
            style="margin-bottom: 5px">
            Return
        </button>

        <form action="/borrows/${borrow.id}" method="POST" class="d-inline">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="DELETE">
            <button type="submit" class="btn btn-sm btn-outline-danger"
                onclick="return confirm('Delete this borrow record?')">
                Delete
            </button>
        </form>
    `;
        }

        function updateBorrowRow(borrow) {
            const row = document.getElementById(`borrow-row-${borrow.id}`);
            if (!row) return;

            const rowNumber = row.querySelector('td:first-child')?.textContent?.trim() || '#';

            row.innerHTML = `
        <td>${rowNumber}</td>
        <td><div class="fw-semibold">${borrow.student_name}</div></td>
        <td>${borrow.gender}</td>
        <td>${borrow.group_name}</td>
        <td><div class="fw-semibold">${borrow.item_name}</div></td>
        <td>${borrow.qty}</td>
        <td class="datetime">${dateCell(borrow.borrow_date)}</td>
        <td class="datetime">${dateCell(borrow.return_date)}</td>
        <td>${statusBadge(borrow.status)}</td>
        <td class="text-end">${buildActionButtons(borrow)}</td>
    `;
        }
    </script>
    <script>
        document.getElementById('returnForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();

            const form = this;
            const formData = new FormData(form);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: formData
                });

                const data = await response.json();

                if (!response.ok) {
                    showAjaxMessage(data.message || 'Return failed.', 'danger');
                    return;
                }

                updateBorrowRow(data.borrow);
                showAjaxMessage(data.message, 'success');

                const modalEl = document.getElementById('returnModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();

                form.reset();
            } catch (error) {
                showAjaxMessage('Something went wrong.', 'danger');
            }
        });
    </script>
    <script>
        document.getElementById('editBorrowForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();

            const form = this;
            const formData = new FormData(form);
            formData.append('_method', 'PUT');

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: formData
                });

                const data = await response.json();

                if (!response.ok) {
                    showAjaxMessage(data.message || 'Update failed.', 'danger');
                    return;
                }

                updateBorrowRow(data.borrow);
                showAjaxMessage(data.message, 'success');

                const modalEl = document.getElementById('editBorrowModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
            } catch (error) {
                showAjaxMessage('Something went wrong.', 'danger');
            }
        });
    </script>
    <script>
        document.addEventListener('submit', async function(e) {
            if (!e.target.classList.contains('undo-return-form')) return;

            e.preventDefault();

            const form = e.target;

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: new FormData(form)
                });

                const data = await response.json();

                if (!response.ok) {
                    showAjaxMessage(data.message || 'Undo failed.', 'danger');
                    return;
                }

                updateBorrowRow(data.borrow);
                showAjaxMessage(data.message, 'success');
            } catch (error) {
                showAjaxMessage('Something went wrong.', 'danger');
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const returnModal = document.getElementById('returnModal');
            const returnSelect = document.getElementById('return_borrow_id');

            if (returnModal && returnSelect) {
                returnModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const borrowId = button?.getAttribute('data-borrow-id');

                    returnSelect.value = borrowId || '';
                });
            }

            const detailModal = document.getElementById('borrowDetailModal');

            if (detailModal) {
                detailModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;

                    detailModal.querySelector('.js-student').textContent = button.getAttribute(
                        'data-student') || '-';
                    detailModal.querySelector('.js-item').textContent = button.getAttribute('data-item') ||
                        '-';
                    detailModal.querySelector('.js-qty').textContent = button.getAttribute('data-qty') ||
                        '-';
                    detailModal.querySelector('.js-status').textContent = button.getAttribute(
                        'data-status') || '-';
                    detailModal.querySelector('.js-condition').textContent = button.getAttribute(
                        'data-condition') || '-';
                    detailModal.querySelector('.js-borrow-date').textContent = button.getAttribute(
                        'data-borrow-date') || '-';
                    detailModal.querySelector('.js-due-date').textContent = button.getAttribute(
                        'data-due-date') || '-';
                    detailModal.querySelector('.js-return-date').textContent = button.getAttribute(
                        'data-return-date') || '-';
                    detailModal.querySelector('.js-approved-by').textContent = button.getAttribute(
                        'data-approved-by') || '-';
                    detailModal.querySelector('.js-returned-by').textContent = button.getAttribute(
                        'data-returned-by') || '-';
                    detailModal.querySelector('.js-notes').textContent = button.getAttribute(
                        'data-notes') || '-';
                    detailModal.querySelector('.js-return-notes').textContent = button.getAttribute(
                        'data-return-notes') || '-';
                });
            }

            const input = document.getElementById('student_search');
            const hidden = document.getElementById('student_id');
            const list = document.getElementById('students_list');

            if (input && hidden && list) {
                input.addEventListener('input', function() {
                    const value = input.value.trim();
                    hidden.value = '';

                    const options = list.querySelectorAll('option');
                    for (const opt of options) {
                        if (opt.value === value) {
                            hidden.value = opt.dataset.id;
                            break;
                        }
                    }
                });

                input.addEventListener('blur', function() {
                    if (!hidden.value) {
                        input.setCustomValidity('Please select a student from the list.');
                    } else {
                        input.setCustomValidity('');
                    }
                });
            }

            const url = new URL(window.location.href);
            const params = url.searchParams;

            if (params.get('openBorrow') === '1') {
                const studentName = params.get('student_name') || '';
                const studentId = params.get('student_id') || '';

                const studentInput = document.getElementById('student_search');
                const studentHidden = document.getElementById('student_id');

                if (studentInput) studentInput.value = studentName;
                if (studentHidden) studentHidden.value = studentId;

                const modalEl = document.getElementById('borrowModal');
                if (modalEl && window.bootstrap) {
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();

                    modalEl.addEventListener('shown.bs.modal', function() {
                        const itemSelect = modalEl.querySelector('select[name="item_id"]');
                        if (itemSelect) itemSelect.focus();
                    }, {
                        once: true
                    });
                }

                params.delete('openBorrow');
                params.delete('student_id');
                params.delete('student_name');

                window.history.replaceState({},
                    document.title,
                    url.pathname + (params.toString() ? '?' + params.toString() : '')
                );
            }
        });
    </script>
    <script>
        const editModal = document.getElementById('editBorrowModal');

        if (editModal) {

            editModal.addEventListener('show.bs.modal', function(event) {

                const button = event.relatedTarget;

                const borrowId = button.getAttribute('data-id');
                const studentId = button.getAttribute('data-student');
                const itemId = button.getAttribute('data-item');
                const qty = button.getAttribute('data-qty');
                const notes = button.getAttribute('data-notes');

                document.getElementById('edit_borrow_id').value = borrowId;
                document.getElementById('edit_qty').value = qty;
                document.getElementById('edit_note').value = notes || '';

                setTimeout(() => {
                    document.getElementById('edit_student_id').value = studentId;
                    document.getElementById('edit_item_id').value = itemId;
                }, 100);

            });

        }
    </script>
    <script>
    function buildActionButtons(borrow) {
        if (borrow.status === 'RETURNED') {
            return `
                ${buildViewButton(borrow)}
                <form action="/admin/borrows/${borrow.id}/undo-return" method="POST" class="d-inline undo-return-form">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button type="submit" class="btn btn-sm btn-outline-warning" style="margin-bottom:5px">
                        Undo
                    </button>
                </form>
            `;
        }
 
        return `
            <button class="btn btn-sm btn-outline-primary"
                data-bs-toggle="modal"
                data-bs-target="#editBorrowModal"
                data-id="${borrow.id}"
                data-student="${borrow.student_id}"
                data-item="${borrow.item_id}"
                data-qty="${borrow.qty}"
                data-notes="${escapeHtml(borrow.notes ?? '')}"
                style="margin-bottom:5px">
                Edit
            </button>
 
            ${buildViewButton(borrow)}
 
            <button class="btn btn-sm btn-outline-dark"
                data-bs-toggle="modal"
                data-bs-target="#returnModal"
                data-borrow-id="${borrow.id}"
                style="margin-bottom:5px">
                Return
            </button>
 
            <button class="btn btn-sm btn-outline-danger delete-borrow-btn"
                data-id="${borrow.id}"
                onclick="return confirm('Delete this borrow record?')">
                Delete
            </button>
        `;
    }
 
    // ── AJAX delete handler ──────────────────────────────────
    document.addEventListener('click', async function (e) {
        const btn = e.target.closest('.delete-borrow-btn');
        if (!btn) return;
 
        e.preventDefault();
        if (!confirm('Delete this borrow record?')) return;
 
        const id  = btn.dataset.id;
        const row = document.getElementById(`borrow-row-${id}`);
 
        btn.disabled    = true;
        btn.textContent = 'Deleting…';
 
        try {
            const response = await fetch(`/admin/borrows/${id}`, {
                method:  'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept':           'application/json',
                    'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: new URLSearchParams({ _method: 'DELETE' }),
            });
 
            const data = await response.json();
 
            if (data.success) {
                if (row) row.remove();
                showAjaxMessage(data.message, 'success');
            } else {
                showAjaxMessage(data.message || 'Delete failed.', 'error');
                btn.disabled    = false;
                btn.textContent = 'Delete';
            }
        } catch (err) {
            showAjaxMessage('Something went wrong.', 'error');
            btn.disabled    = false;
            btn.textContent = 'Delete';
        }
    });
</script>
@endsection
