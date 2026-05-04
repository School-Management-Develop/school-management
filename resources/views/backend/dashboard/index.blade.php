@extends('backend.layout.master')

@section('title', 'Dashboard')
@section('dashboard_active', 'active')

@section('contents')
    <style>
        .dropdown_menu-7 {
            animation: rotateMenu 400ms ease-in-out forwards;
            transform-origin: top center;
        }

        @keyframes rotateMenu {
            0% {
                transform: rotateX(-90deg);
                opacity: 0;
            }

            70% {
                transform: rotateX(20deg);
                opacity: 1;
            }

            100% {
                transform: rotateX(0deg);
                opacity: 1;
            }
        }

        .drop {
            width: 44px;
            height: 44px;
            border: none;
            border-radius: 50%;
            background: #ffffff;
            color: #1f2937;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            transition: all 0.25s ease;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.08);
            overflow: visible;
        }

        .drop:hover {
            background: #f8fafc;
            color: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.15);
        }

        .drop i {
            font-size: 18px;
        }

        .drop .badge {
            min-width: 18px;
            height: 18px;
            font-size: 10px;
            line-height: 18px;
            padding: 0 5px;
            background: linear-gradient(135deg, #ef4444, #dc2626) !important;
            color: #fff;
            border: 2px solid #fff;
            box-shadow: 0 4px 10px rgba(220, 38, 38, 0.35);
        }

        .dropdown_menu {
            position: absolute;
            top: 55px;
            right: 0;
            left: auto;
            min-width: 260px;
            margin: 0;
            padding: 10px;
            list-style: none;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.16);
            border: 1px solid #e5e7eb;
            z-index: 999;
            display: none;
        }

        .dropdown_menu.show {
            display: block;
        }

        .dropdown_menu li {
            display: block;
            opacity: 1;
            margin-bottom: 6px;
        }

        .dropdown_menu li:last-child {
            margin-bottom: 0;
        }

        .dropdown_menu li a {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #1f2937;
            background: #f8fafc;
            padding: 12px 14px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.25s ease;
        }

        .dropdown_menu li a i {
            color: #2563eb;
            font-size: 16px;
        }

        .dropdown_menu li a:hover {
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
            color: #1d4ed8;
            transform: translateX(4px);
        }

        .dropdown_menu::before {
            content: "";
            position: absolute;
            top: -8px;
            right: 14px;
            width: 14px;
            height: 14px;
            background: #ffffff;
            border-left: 1px solid #e5e7eb;
            border-top: 1px solid #e5e7eb;
            transform: rotate(45deg);
        }
    </style>
    <div class="container-fluid py-4">

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-0">
                <div class="p-3 p-md-4 text-white rounded-4"
                    style="background: linear-gradient(135deg, #111827 0%, #1f2937 45%, #4338ca 100%);">
                    <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-3">

                        <div class="d-flex align-items-start align-items-sm-center gap-3">
                            <div class="rounded-4 d-flex align-items-center justify-content-center shadow-sm"
                                style="width:64px; height:64px; background: rgba(255,255,255,0.14); backdrop-filter: blur(8px);">
                                <i class="bi bi-speedometer2 fs-3"></i>
                            </div>

                            <div>
                                <h2 class="fw-bold mb-1 text-white">{{ __('app.dashboard') }}</h2>
                                <div class="text-white-50 mb-1">
                                    {{ __('app.welcome_back', ['name' => Auth::user()->name ?? 'Admin']) }}
                                </div>
                                <div class="small text-white-50 d-flex flex-wrap gap-3">
                                    <span>
                                        <i class="bi bi-calendar3 me-1"></i>
                                        {{ now()->timezone('Asia/Jakarta')->format('d M Y') }}
                                    </span>
                                    <span>
                                        <i class="bi bi-clock me-1"></i>
                                        {{ now()->timezone('Asia/Jakarta')->format('H:i') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2">
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('students.index') }}"
                                    class="btn btn-outline-light rounded-pill px-3 fw-medium">
                                    <i class="bi bi-people me-1"></i> {{ __('app.students') }}
                                </a>

                                <a href="{{ route('items.index') }}"
                                    class="btn btn-outline-light rounded-pill px-3 fw-medium">
                                    <i class="bi bi-box-seam me-1"></i> {{ __('app.items') }}
                                </a>

                                <a href="{{ route('submissions.index') }}"
                                    class="btn btn-outline-light rounded-pill px-3 fw-medium">
                                    <i class="bi bi-inbox me-1"></i> {{ __('app.submissions') }}
                                </a>
                            </div>
                            <div class="dropdown">
                                <button type="button" class="btn btn-light rounded-pill px-3 fw-medium dropdown-toggle"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    🌐 {{ __('app.language') }}
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item"
                                            href="{{ route('language.switch', 'en') }}">{{ __('app.english') }}</a>
                                    </li>
                                    <li><a class="dropdown-item"
                                            href="{{ route('language.switch', 'kh') }}">{{ __('app.khmer') }}</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="d-flex align-items-center gap-2 position-relative phone-hide">
                                <div class="position-relative d-inline-block">
                                    <button type="button" id="notification-desktop"
                                        class="drop btn btn-light rounded-circle d-flex align-items-center justify-content-center position-relative">
                                        <i class="bi bi-bell"></i>
                                        <span class="position-absolute start-100 translate-middle badge rounded-pill " style="padding-bottom: 18px">
                                            {{ $pendingSubmissions + $overdueCount }}
                                        </span>

                                    </button>

                                    <ul class="dropdown_menu dropdown_menu-7" id="dropdown-desktop">
                                        <li>
                                            <a href="{{ url('admin/submissions') }}" class="nav-pill @yield('submission_active')">
                                                <i class="bi bi-inbox"></i>
                                                {{ __('app.submissions') }}
                                                @if (($pendingSubmissions ?? 0) > 0)
                                                    <span
                                                        class="ms-auto d-inline-flex align-items-center justify-content-center text-white fw-bold"
                                                        style="
                                                            min-width: 24px;
                                                            height: 24px;
                                                            padding: 0 8px;
                                                            background: linear-gradient(135deg, #ef4444, #dc2626);
                                                            border-radius: 999px;
                                                            font-size: 12px;
                                                            line-height: 1;
                                                            box-shadow: 0 4px 10px rgba(220, 38, 38, 0.25);
                                                            border: 2px solid #fff;
                                                        ">
                                                        {{ $pendingSubmissions }}
                                                    </span>
                                                @endif
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('borrows.overdue') }}" class="nav-pill @yield('overdue_active')">
                                                <i class="bi bi-exclamation-circle"></i>
                                                {{ __('app.overdue_borrow') }}

                                                @if (($overdueCount ?? 0) > 0)
                                                    <span
                                                        class="ms-auto d-inline-flex align-items-center justify-content-center text-white fw-bold"
                                                        style="
                                                            min-width: 24px;
                                                            height: 24px;
                                                            padding: 0 8px;
                                                            background: linear-gradient(135deg, #ef4444, #dc2626);
                                                            border-radius: 999px;
                                                            font-size: 12px;
                                                            line-height: 1;
                                                            box-shadow: 0 4px 10px rgba(220, 38, 38, 0.25);
                                                            border: 2px solid #fff;
                                                        ">
                                                        {{ $overdueCount }}
                                                    </span>
                                                @endif
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                @php
                                    $user = Auth::user();
                                    $profilePhoto = !empty($user?->photo) ? Storage::url($user->photo) : null;
                                    $userInitial = strtoupper(substr($user->name ?? 'A', 0, 1));
                                @endphp

                                <div class="dropdown">

                                    <button
                                        class="btn btn-light border-0 rounded-pill px-2 px-sm-3 py-2 d-flex align-items-center gap-2 shadow-sm"
                                        type="button" data-bs-toggle="dropdown" data-bs-display="static"
                                        aria-expanded="false">

                                        <div class="position-relative">
                                            <div class="mb-3" style="margin-bottom:0 !important;">
                                                @if (!empty($user->photo))
                                                    <img src="{{ asset('storage/' . $user->photo) }}" alt="Profile"
                                                        class="rounded-circle border"
                                                        style="width:42px; height:42px; object-fit:cover;">
                                                @else
                                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-uppercase border"
                                                        style="width:72px; height:72px; background:#e0e7ff; color:#3730a3; font-size:24px;">
                                                        {{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}
                                                    </div>
                                                @endif
                                            </div>

                                            <span
                                                class="position-absolute bottom-0 end-0 translate-middle p-1 bg-success border border-white rounded-circle"
                                                style="width:10px; height:10px;"></span>
                                        </div>

                                        <div class="text-start lh-sm d-none d-sm-block">
                                            <div class="fw-semibold text-dark" style="font-size: 14px;">
                                                {{ $user->name ?? 'Admin' }}
                                            </div>
                                            <div class="text-muted" style="font-size: 12px;">
                                                {{ $user->email ?? 'Administrator' }}
                                            </div>
                                        </div>

                                        <i class="bi bi-chevron-down small text-muted"></i>
                                    </button>

                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow rounded-4 p-2 mt-2"
                                        style="min-width: 280px; z-index: 1055;">
                                        <li class="px-3 py-3">
                                            <div class="d-flex align-items-center gap-3">
                                                @if ($profilePhoto)
                                                    <img src="{{ asset('storage/' . $user->photo) }}" alt="Profile"
                                                        class="rounded-circle border"
                                                        style="width:56px; height:56px; object-fit:cover;">
                                                @else
                                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-uppercase border"
                                                        style="width:56px; height:56px; background:#e0e7ff; color:#3730a3; font-size:20px;">
                                                        {{ $userInitial }}
                                                    </div>
                                                @endif

                                                <div>
                                                    <div class="fw-bold">{{ $user->name ?? 'Admin' }}</div>
                                                    <div class="text-muted small">{{ $user->email ?? '' }}</div>
                                                    <span
                                                        class="badge bg-success-subtle text-success mt-1">{{ __('app.online') }}</span>
                                                </div>
                                            </div>
                                        </li>

                                        <li>
                                            <hr class="dropdown-divider my-2">
                                        </li>

                                        <li>
                                            <a class="dropdown-item rounded-3 py-2" href="{{ route('profile') }}">
                                                <i class="bi bi-person me-2"></i> {{ __('app.my_profile') }}
                                            </a>
                                        </li>

                                        <li>
                                            <a class="dropdown-item rounded-3 py-2" href="{{ route('profile') }}">
                                                <i class="bi bi-gear me-2"></i> {{ __('app.account_settings') }}
                                            </a>
                                        </li>

                                        <li>
                                            <a class="dropdown-item rounded-3 py-2" href="{{ route('profile') }}">
                                                <i class="bi bi-shield-lock me-2"></i> {{ __('app.change_password') }}
                                            </a>
                                        </li>

                                        <li>
                                            <hr class="dropdown-divider my-2">
                                        </li>

                                        <li>
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit" class="dropdown-item rounded-3 py-2 text-danger">
                                                    <i class="bi bi-box-arrow-right me-2"></i> {{ __('app.logout') }}
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        {{-- Stats --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-secondary small">{{ __('app.total_students') }}</div>
                                <div class="fs-2 fw-bold mt-1">{{ $totalStudents ?? 0 }}</div>
                            </div>
                            <div class="bg-success-subtle text-success rounded-3 p-2">
                                <i class="bi bi-people fs-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-secondary small">{{ __('app.active_students') }}</div>
                                <div class="fs-2 fw-bold mt-1 text-success">{{ $activeStudents ?? 0 }}</div>
                            </div>
                            <div class="bg-success-subtle text-success rounded-3 p-2">
                                <i class="bi bi-person-check fs-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-secondary small">{{ __('app.inactive_students') }}</div>
                                <div class="fs-2 fw-bold mt-1 text-secondary">{{ $inactiveStudents ?? 0 }}</div>
                            </div>
                            <div class="bg-secondary-subtle text-secondary rounded-3 p-2">
                                <i class="bi bi-person-dash fs-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-secondary small">{{ __('app.total_groups') }}</div>
                                <div class="fs-2 fw-bold mt-1">{{ $totalGroups ?? 0 }}</div>
                            </div>
                            <div class="bg-primary-subtle text-primary rounded-3 p-2">
                                <i class="bi bi-diagram-3 fs-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-secondary small">{{ __('app.total_items') }}</div>
                                <div class="fs-2 fw-bold mt-1">{{ $totalItems ?? 0 }}</div>
                            </div>
                            <div class="bg-warning-subtle text-warning rounded-3 p-2">
                                <i class="bi bi-box-seam fs-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-secondary small">{{ __('app.socket_items') }}</div>
                                <div class="fs-2 fw-bold mt-1 text-dark">{{ $socketAvailable ?? 0 }}</div>
                            </div>
                            <div class="rounded-3 d-flex align-items-center justify-content-center"
                                style="width: 78px; height: 78px;   ">
                                <img src="{{ asset('storage/'. $imageSocket?->image ?? '') }}"
                                    alt="socket" width="54" height="54"
                                    style="object-fit:cover; border-radius:10px; border: 2px solid #e5e7eb;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-secondary small">{{ __('app.borrowed_items') }}</div>
                                <div class="fs-2 fw-bold mt-1 text-danger">{{ $borrowedItems ?? 0 }}</div>
                            </div>
                            <div class="bg-danger-subtle text-danger rounded-3 p-2">
                                <i class="bi bi-arrow-left-right fs-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-secondary small">{{ __('app.pending_submissions') }}</div>
                                <div class="fs-2 fw-bold mt-1 text-warning">{{ $pendingSubmissions ?? 0 }}</div>
                            </div>
                            <div class="bg-warning-subtle text-warning rounded-3 p-2">
                                <i class="bi bi-inbox fs-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            {{-- Recent Submissions --}}
            <div class="col-12 col-xl-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="fw-bold mb-1">{{ __('app.recent_submissions') }}</h5>
                                <div class="text-secondary small">{{ __('app.latest_student_requests') }}</div>
                            </div>
                            <a href="{{ route('submissions.index') }}"
                                class="btn btn-sm btn-secondary">{{ __('app.view_all') }}</a>
                        </div>
                    </div>

                    <div class="card-body px-4">
                        @forelse ($recentSubmissions ?? [] as $submission)
                            <div class="d-flex justify-content-between align-items-start py-3 border-bottom">
                                <div>
                                    <div class="fw-semibold">{{ $submission->student_name }}</div>
                                    <div class="text-secondary small">
                                        {{ $submission->group?->group_name ?? '-' }}
                                        •
                                        {{ $submission->item?->display_name ?? '-' }}
                                    </div>
                                </div>

                                <div class="text-end">
                                    @if ($submission->is_borrow_approved)
                                        <span class="badge bg-success">{{ __('app.approved') }}</span>
                                    @else
                                        <span class="badge bg-warning text-dark">{{ __('app.pending') }}</span>
                                    @endif
                                    <div class="text-secondary small mt-1">
                                        {{ $submission->created_at ? $submission->created_at->timezone('Asia/Jakarta')->format('d M Y H:i') : '-' }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-secondary py-5">{{ __('app.no_recent_submissions') }}</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Recent Borrows --}}
            <div class="col-12 col-xl-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="fw-bold mb-1">{{ __('app.recent_borrows') }}</h5>
                                <div class="text-secondary small">{{ __('app.latest_borrow_activity') }}</div>
                            </div>
                            <a href="{{ route('borrows.index') }}"
                                class="btn btn-sm btn-secondary">{{ __('app.view_all') }}</a>
                        </div>
                    </div>

                    <div class="card-body px-4">
                        @forelse ($recentBorrows ?? [] as $borrow)
                            <div class="d-flex align-items-start gap-3 py-3 border-bottom">
                                <div>
                                    @if (!empty($borrow->item?->image))
                                        <img src="{{ Storage::url($borrow->item->image) }}" width="54"
                                            height="54" style="object-fit:cover;border-radius:10px;">
                                    @else
                                        <div class="bg-light rounded-3 d-flex align-items-center justify-content-center"
                                            style="width:54px;height:54px;">
                                            <i class="bi bi-image text-secondary"></i>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex-grow-1">
                                    <div class="fw-semibold">{{ $borrow->student?->student_name ?? '-' }}</div>
                                    <div class="text-secondary small">
                                        {{ $borrow->item?->display_name ?? '-' }} • {{ __('app.qty') }}:
                                        {{ $borrow->qty ?? 0 }}
                                    </div>
                                </div>

                                <div class="text-end">
                                    <span class="badge bg-primary">{{ $borrow->status ?? 'BORROWED' }}</span>
                                    <div class="text-secondary small mt-1">
                                        {{ $borrow->borrow_date ? \Carbon\Carbon::parse($borrow->borrow_date)->timezone('Asia/Jakarta')->format('d M Y H:i') : '-' }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-secondary py-5">{{ __('app.no_recent_borrows') }}</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick actions --}}
        <div class="card border-0 shadow-sm rounded-4 mt-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">{{ __('app.quick_actions') }}</h5>

                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <a href="{{ route('students.index') }}" class="btn btn-light border w-100 py-3">
                            <i class="bi bi-people d-block fs-4 mb-2"></i>
                            {{ __('app.students') }}
                        </a>
                    </div>

                    <div class="col-6 col-md-3">
                        <a href="{{ route('groups.index') }}" class="btn btn-light border w-100 py-3">
                            <i class="bi bi-diagram-3 d-block fs-4 mb-2"></i>
                            {{ __('app.groups') }}
                        </a>
                    </div>

                    <div class="col-6 col-md-3">
                        <a href="{{ route('items.index') }}" class="btn btn-light border w-100 py-3">
                            <i class="bi bi-box-seam d-block fs-4 mb-2"></i>
                            {{ __('app.items') }}
                        </a>
                    </div>

                    <div class="col-6 col-md-3">
                        <a href="{{ route('borrows.index') }}" class="btn btn-light border w-100 py-3">
                            <i class="bi bi-arrow-left-right d-block fs-4 mb-2"></i>
                            {{ __('app.borrows') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection
