@extends('backend.layout.master')

@section('title', 'Submissions')
@section('submission_active', 'active')
@section('contents')

<div class="container-fluid py-4">

    @include('backend.page.alerts.alert')

    @if (session('group_change_request'))
    @php $change = session('group_change_request'); @endphp

    {{-- Backdrop --}}
    <div id="groupChangeBackdrop" style="position:fixed; inset:0; background:rgba(15,23,42,.45);
                backdrop-filter:blur(3px); z-index:1050; animation:fadeIn .25s ease;">
    </div>

    {{-- Sliding banner --}}
    <div id="groupChangeBanner" style="position:fixed; top:0; left:50%; transform:translateX(-50%);
                width:100%; max-width:640px; z-index:1051;
                animation:slideDown .4s cubic-bezier(.25,.8,.25,1);">

        <div style="margin:20px 16px 0; background:#fff; border-radius:16px;
                    box-shadow:0 20px 60px rgba(0,0,0,.25);
                    border-top:5px solid #f59e0b; overflow:hidden;">

            {{-- Header --}}
            <div class="d-flex align-items-center gap-3 px-4 pt-4 pb-2">
                <div style="width:48px; height:48px; border-radius:12px;
                            background:linear-gradient(135deg,#fef3c7,#fde68a);
                            display:flex; align-items:center; justify-content:center;
                            box-shadow:0 4px 12px rgba(245,158,11,.25); flex-shrink:0;">
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-4"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="fw-bold mb-0 text-dark">
                        {{ __('app.Group Change Required') ?? 'Group Change Required' }}</h6>
                    <small
                        class="text-muted">{{ __('app.Please choose an action to continue') ?? 'Please choose an action to continue' }}</small>
                </div>
            </div>

            {{-- Message --}}
            <div class="px-4 py-3" style="font-size:14px; line-height:1.6; color:#374151;">

                {{ __('app.This student') }}
                <span class="fw-bold text-dark">{{ $change['student_name'] }}</span>
                {{ __('app.is already in group') }}
                <span class="badge rounded-pill bg-secondary-subtle text-dark border px-3 py-2 mx-1">
                    <i class="bi bi-people-fill me-1"></i>{{ $change['current_group'] }}
                </span>
                <br>
                {{ __('app.Do you want to change the student to group') }}
                <span
                    class="badge rounded-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2 mx-1">
                    <i class="bi bi-arrow-right-circle-fill me-1"></i>{{ $change['new_group'] }}
                </span>
                ?
            </div>

            {{-- Actions --}}
            <div class="px-4 py-3 d-flex gap-2 flex-wrap" style="background:#f9fafb; border-top:1px solid #f1f5f9;">

                <form method="POST" action="{{ route('submissions.changeGroup', $change['submission_id']) }}"
                    class="flex-grow-1">
                    @csrf
                    <button type="submit" class="btn btn-success w-100 fw-semibold" style="border-radius:10px;">
                        <i class="bi bi-check-circle-fill me-1"></i>
                        {{ __('app.Yes, Change Group') }}
                    </button>
                </form>

                <form method="POST" action="{{ route('submissions.approveBorrow', $change['submission_id']) }}"
                    class="flex-grow-1">
                    @csrf
                    <input type="hidden" name="skip_group_change" value="1">
                    <button type="submit" class="btn btn-primary w-100 fw-semibold" style="border-radius:10px;">
                        <i class="bi bi-hand-thumbs-up-fill me-1"></i>
                        {{ __('app.Approve Borrow') }}
                    </button>
                </form>

                <a href="{{ route('submissions.index') }}" class="btn btn-outline-danger fw-semibold"
                    style="border-radius:10px;">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </div>
    </div>

    <style>
    @keyframes slideDown {
        from {
            transform: translate(-50%, -120%);
            opacity: 0;
        }

        to {
            transform: translate(-50%, 0);
            opacity: 1;
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    body.banner-open {
        overflow: hidden;
    }
    </style>

    <script>
    document.body.classList.add('banner-open');
    // prevent closing on backdrop click — user must choose an action
    document.getElementById('groupChangeBackdrop').addEventListener('click', function(e) {
        const banner = document.getElementById('groupChangeBanner').querySelector('div');
        banner.style.animation = 'none';
        banner.offsetHeight; // force reflow
        banner.style.animation = 'shake .4s';
    });
    </script>

    <style>
    @keyframes shake {

        0%,
        100% {
            transform: translateX(0);
        }

        25% {
            transform: translateX(-8px);
        }

        75% {
            transform: translateX(8px);
        }
    }
    </style>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-0">{{ __('app.submissions') }}</h2>
            <div class="text-muted">{{ __('app.New student registrations waiting for approval') }}</div>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <form method="GET" class="d-flex gap-2">
                <input class="form-control" name="q" value="{{ request('q') }}"
                    placeholder="{{ __('app.Search student name...') }}">
                <button class="btn btn-primary">{{ __('app.search') }}</button>
            </form>

            <a href="{{ url()->current() }}" class="btn btn-secondary">{{ __('app.reset') }}</a>

            <form method="POST" action="{{ route('submissions.cancelAll') }}"
                onsubmit="return confirm('Are you sure you want to cancel all submissions?');">
                @csrf
                <button type="submit" class="btn btn-danger">{{ __('app.delete all') }}</button>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body table-responsive">
            <table class="table table-bordered align-middle ">
                <thead>
                    <tr>
                        <th>{{ __('app.Student Name') }}</th>
                        <th>{{ __('app.Phone Number') }}</th>
                        <th>{{ __('app.Group') }}</th>
                        <th>{{ __('app.item') }}</th>
                        <th>{{ __('app.notes') }}</th>
                        <th>{{ __('app.Image') }}</th>
                        <th>{{ __('app.qty') }}</th>
                        <th>{{ __('app.Status') }}</th>
                        <th style="width: 260px;">{{ __('app.Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($submissions as $row)
                    <tr>
                        <td>{{ $row->student_name }}</td>
                        <td>{{ $row->phone_number }}</td>
                        <td>{{ $row->group->group_name ?? '-' }}</td>

                        <td>
                            @if ($row->item && $row->item_id)
                            {{ $row->item->display_name }}
                            @else
                            <span class="badge bg-warning text-dark">
                                {{ $row->other_item ?? '-' }}
                            </span>
                            @endif
                        </td>

                        <td>{{ $row->note ?? '-' }}</td>

                        <td>
                            @if (!empty($row->item?->image))
                            <img src="{{ Storage::url($row->item->image) }}" width="60" height="60"
                                style="object-fit:cover;border-radius:8px;">
                            @else
                            <span>No image</span>
                            @endif
                        </td>

                        <td>{{ $row->qty }}</td>

                        <td>
                            @if ($row->is_borrow_approved)
                            <span class="badge bg-success">Approved</span>
                            @elseif ($row->match_status === 'different_group')
                            <span class="badge bg-warning text-dark">Group Change Needed</span>
                            @elseif ($row->match_status === 'same_group' || $row->student_id)
                            <span class="badge bg-warning text-dark">Pending</span>
                            @else
                            <span class="badge bg-secondary">New Student</span>
                            @endif
                        </td>

                        <td>
                            <div class="d-flex flex-column gap-2">
                                @if ($row->is_borrow_approved)
                                <span class="text-success small">{{ __('app.Borrow already approved') }}</span>
                                @elseif ($row->match_status === 'different_group')
                                <form method="POST" action="{{ route('submissions.addStudent', $row->id) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-warning w-100">
                                        {{ __('app.Review Group Change') }}
                                    </button>
                                </form>
                                @elseif ($row->match_status === 'same_group' || $row->student_id)
                                <form method="POST" action="{{ route('submissions.approveBorrow', $row->id) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-primary w-100">
                                        {{ __('app.Approve Borrow') }}
                                    </button>
                                </form>
                                @else
                                <form method="POST" action="{{ route('submissions.addStudent', $row->id) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-dark w-100">
                                        {{ __('app.Add Student') }}
                                    </button>
                                </form>
                                @endif

                                <form method="POST" action="{{ route('submissions.remove', $row->id) }}"
                                    onsubmit="return confirm('Remove this submission?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger w-100">{{ __('app.Remove') }}</button>
                                </form>
                                <div>
                                    <button class="btn btn-outline-primary w-100" data-bs-toggle="modal"
                                        data-bs-target="#viewSubmissionModal{{ $row->id }}">
                                        {{ __('app.View Details') }}
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted">No submissions found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-end">
                {{ $submissions->links() }}
            </div>
        </div>
    </div>
</div>
{{-- View Submission Detail Modals --}}
@foreach ($submissions as $row)
<div class="modal fade" id="viewSubmissionModal{{ $row->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow rounded-4">

            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">{{ __('app.Submission Details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body pt-3">
                {{-- Student Info Card --}}
                <div class="p-3 rounded-4 bg-light border mb-3">
                    <h5 class="fw-bold mb-1">{{ $row->student_name }}</h5>
                    <span class="text-secondary small">{{ $row->group->group_name ?? '-' }}</span>
                </div>

                <div class="row g-3">
                    <div class="col-6">
                        <div class="text-secondary small">{{ __('app.Gender') }}</div>
                        <div class="fw-semibold">{{ $row->gender ?: '-' }}</div>
                    </div>

                    <div class="col-6">
                        <div class="text-secondary small">{{ __('app.Phone Number') }}</div>
                        <div class="fw-semibold">{{ $row->phone_number ?: '-' }}</div>
                    </div>

                    <div class="col-6">
                        <div class="text-secondary small">{{ __('app.item') }}</div>
                        <div class="fw-semibold">
                            @if ($row->item && $row->item_id)
                            {{ $row->item->display_name }}
                            @else
                            {{ $row->other_item ?? '-' }}
                            @endif
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="text-secondary small">{{ __('app.qty') }}</div>
                        <div class="fw-semibold">{{ $row->qty }}</div>
                    </div>

                    <div class="col-12">
                        <div class="text-secondary small">{{ __('app.notes') }}</div>
                        <div class="fw-semibold">{{ $row->note ?: '-' }}</div>
                    </div>

                    <div class="col-6">
                        <div class="text-secondary small">{{ __('app.Status') }}</div>
                        <div>
                            @if ($row->is_borrow_approved)
                            <span class="badge bg-success">Approved</span>
                            @elseif ($row->match_status === 'different_group')
                            <span class="badge bg-warning text-dark">Group Change Needed</span>
                            @elseif ($row->match_status === 'same_group' || $row->student_id)
                            <span class="badge bg-warning text-dark">Pending</span>
                            @else
                            <span class="badge bg-secondary">New Student</span>
                            @endif
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="text-secondary small">{{ __('app.Submitted At') }}</div>
                        <div class="fw-semibold">
                            {{ $row->created_at ? $row->created_at->timezone('Asia/Phnom_Penh')->format('d M Y, H:i') : '-' }}
                        </div>
                    </div>

                    @if ($row->item && !empty($row->item->image))
                    <div class="col-12">
                        <div class="text-secondary small mb-1">{{ __('app.Image') }}</div>
                        <img src="{{ Storage::url($row->item->image) }}" class="rounded-3 border"
                            style="max-width:100%; max-height:200px; object-fit:cover;">
                    </div>
                    @endif
                </div>
            </div>

            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('app.close') }}</button>
            </div>

        </div>
    </div>
</div>
@endforeach
@endsection