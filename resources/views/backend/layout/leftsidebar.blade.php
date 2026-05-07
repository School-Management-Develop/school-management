@php
use Illuminate\Support\Facades\Auth;

$user = Auth::user();
$role = strtolower($user->role ?? '');
$profilePhoto = !empty($user?->photo) ? Storage::url($user->photo) : null;
$userInitial = strtoupper(substr($user->name ?? 'A', 0, 1));

$pendingSubmissionCount = $pendingSubmissionCount ?? 0;
$overdueCount = $overdueCount ?? 0;
$lateReturnedCount = $lateReturnedCount ?? 0;
$notificationCount = $pendingSubmissionCount + $overdueCount;

$canManageUsers = in_array($role, ['admin', 'super admin', 'superadmin']);
@endphp

<style>
.mobile-only {
    font-size: 16px;
}

.sidebar-menu {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.sidebar-menu-item {
    margin: 0;
    padding: 0;
}

.sidebar-link {
    display: flex;
    align-items: center;
    gap: 12px;
    width: 100%;
    padding: 12px 14px;
    border-radius: 14px;
    text-decoration: none;
    color: #475569;
    font-weight: 500;
    transition: all 0.2s ease;
    background: transparent;
}

.sidebar-link:hover {
    background: #f8fafc;
    color: #0f172a;
    transform: translateX(2px);
}

.sidebar-link.active {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #fff !important;
    box-shadow: 0 8px 20px rgba(37, 99, 235, 0.18);
}

.sidebar-icon {
    width: 22px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}

.sidebar-label {
    flex: 1;
    min-width: 0;
}

.sidebar-badge {
    min-width: 22px;
    height: 22px;
    padding: 0 8px;
    border-radius: 999px;
    background: #ef4444;
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
}

.dashboard-user-avatar,
.dashboard-user-initial {
    width: 38px;
    height: 38px;
    object-fit: cover;
}

.dashboard-dropdown-avatar,
.dashboard-dropdown-initial {
    width: 52px;
    height: 52px;
    object-fit: cover;
}

.dashboard-user-initial,
.dashboard-dropdown-initial {
    background: #e2e8f0;
    color: #0f172a;
}

.dashboard-profile-btn {
    background: #fff;
}

.dropdown_menu {
    list-style: none;
    margin: 0;
    padding: 10px;
    min-width: 260px;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 16px 40px rgba(15, 23, 42, 0.12);
    position: absolute;
    right: 0;
    top: calc(100% + 10px);
    z-index: 1055;
    display: none;
}

.dropdown_menu.show {
    display: block;
}

.dropdown_menu li+li {
    margin-top: 8px;
}

.notification-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    border-radius: 12px;
    text-decoration: none;
    color: #334155;
    background: #f8fafc;
    font-weight: 500;
}

.notification-link:hover {
    background: #eff6ff;
    color: #0f172a;
}

.sidebar-language-switch .btn {
    border-radius: 10px;
}

.mobile-sidebar-header {
    line-height: 1.1;
}
</style>

<div class="d-lg-none mobile-topbar bg-white border-bottom px-3 py-2 d-flex align-items-center justify-content-between">
    <button class="btn btn-dark btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar"
        aria-controls="mobileSidebar">
        <i class="bi bi-list"></i>
    </button>

    <div class="fw-semibold mobile-only">IT Department</div>

    <div style="width: 38px;">
        <div class="dashboard-user-row d-flex align-items-center justify-content-end gap-2">
            <div class="position-relative d-inline-block">
                <button type="button" id="notification"
                    class="drop btn btn-light rounded-circle d-flex align-items-center justify-content-center position-relative">
                    <i class="bi bi-bell"></i>

                    @if ($notificationCount > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        {{ $notificationCount }}
                    </span>
                    @endif
                </button>

                <ul class="dropdown_menu dropdown_menu-7" id="dropdown">
                    <li>
                        <a href="{{ route('submissions.index') }}"
                            class="notification-link {{ request()->routeIs('submissions.*') ? 'active' : '' }}">
                            <i class="bi bi-inbox"></i>
                            <span class="flex-grow-1">{{ __('app.submissions') }}</span>

                            @if ($pendingSubmissionCount > 0)
                            <span class="sidebar-badge">{{ $pendingSubmissionCount }}</span>
                            @endif
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('borrows.overdue') }}"
                            class="notification-link {{ request()->routeIs('borrows.overdue') ? 'active' : '' }}">
                            <i class="bi bi-exclamation-circle"></i>
                            <span class="flex-grow-1">{{ __('app.overdue_borrow') }}</span>

                            @if ($overdueCount > 0)
                            <span class="sidebar-badge">{{ $overdueCount }}</span>
                            @endif
                        </a>
                    </li>
                </ul>
            </div>

            <div class="dropdown">
                <button
                    class="dashboard-profile-btn btn btn-light border-0 rounded-pill px-2 px-sm-3 py-2 d-flex align-items-center gap-2 shadow-sm"
                    type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">

                    <div class="position-relative flex-shrink-0">
                        @if ($profilePhoto)
                        <img src="{{ $profilePhoto }}" alt="Profile"
                            class="dashboard-user-avatar rounded-circle border">
                        @else
                        <div
                            class="dashboard-user-initial rounded-circle d-flex align-items-center justify-content-center fw-bold text-uppercase border">
                            {{ $userInitial }}
                        </div>
                        @endif

                        <span
                            class="position-absolute bottom-0 end-0 translate-middle p-1 bg-success border border-white rounded-circle"
                            style="width:10px; height:10px;"></span>
                    </div>

                    <div class="text-start lh-sm d-none d-sm-block">
                        <div class="fw-semibold text-dark" style="font-size: 14px;">
                            {{ $user->name ?? 'Admin' }}
                        </div>
                        <div class="text-muted text-truncate" style="font-size: 12px; max-width: 180px;">
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
                            <img src="{{ $profilePhoto }}" alt="Profile"
                                class="dashboard-dropdown-avatar rounded-circle border">
                            @else
                            <div
                                class="dashboard-dropdown-initial rounded-circle d-flex align-items-center justify-content-center fw-bold text-uppercase border">
                                {{ $userInitial }}
                            </div>
                            @endif

                            <div class="min-w-0">
                                <div class="fw-bold text-truncate">{{ $user->name ?? 'Admin' }}</div>
                                <div class="text-muted small text-truncate">{{ $user->email ?? '' }}</div>
                                <span class="badge bg-success-subtle text-success mt-1">Online</span>
                            </div>
                        </div>
                    </li>

                    <li>
                        <hr class="dropdown-divider my-2">
                    </li>

                    <li>
                        <a class="dropdown-item rounded-3 py-2" href="{{ route('profile') }}">
                            <i class="bi bi-person me-2"></i> My Profile
                        </a>
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

<div class="app d-flex">
    <aside id="sidebar" class="sidebar d-flex flex-column p-2">
        <div class="brand">
            <div>
                <a href="{{ route('admin.dashboard') }}">
                    <img src="{{ asset('assets/img/image.png') }}" alt="Profile" class="rounded-circle border"
                        style="width:38px; height:38px; object-fit:cover;">
                </a>
            </div>

            <div>
                <a href="{{ route('admin.dashboard') }}" style="text-decoration: none; color: inherit;">
                    <div style="line-height:1;">IT <span class="text-muted">Department</span></div>
                </a>
                <div class="text-muted" style="font-size:12px; font-weight:600;">
                    {{ $user->name ?? 'Guest' }} • {{ ucfirst($user->role ?? 'User') }}
                </div>
            </div>
        </div>

        {{-- Mobile Sidebar --}}
        <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="mobileSidebar">
            <div class="offcanvas-header border-bottom">
                <h5 class="offcanvas-title mb-0">{{ __('app.menu') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>

            <div class="offcanvas-body p-0">
                <div class="p-3 border-bottom mobile-sidebar-header">
                    <div class="fw-bold">Setec School</div>
                    <small class="text-muted">{{ __('app.admin_panel') }}</small>
                </div>

                <div class="p-2">
                    <ul class="sidebar-menu">
                        <li class="sidebar-menu-item">
                            <a href="{{ route('admin.dashboard') }}"
                                class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <span class="sidebar-icon"><i class="bi bi-speedometer2"></i></span>
                                <span class="sidebar-label">{{ __('app.dashboard') }}</span>
                            </a>
                        </li>

                        <li class="sidebar-menu-item">
                            <a href="{{ route('students.index') }}"
                                class="sidebar-link {{ request()->routeIs('students.*') ? 'active' : '' }}">
                                <span class="sidebar-icon"><i class="bi bi-people"></i></span>
                                <span class="sidebar-label">{{ __('app.students') }}</span>
                            </a>
                        </li>
                        <li class="sidebar-menu-item">
                            <a href="{{ route('teachers.index') }}"
                                class="sidebar-link {{ request()->routeIs('teachers.*') ? 'active' : '' }}">
                                <span class="sidebar-icon"><i class="bi bi-people-fill"></i></span>
                                <span class="sidebar-label">{{ __('app.teachers') }}</span>
                            </a>
                        </li>


                        <li class="sidebar-menu-item">
                            <a href="{{ route('groups.index') }}"
                                class="sidebar-link {{ request()->routeIs('groups.*') ? 'active' : '' }}">
                                <span class="sidebar-icon"><i class="bi bi-diagram-3"></i></span>
                                <span class="sidebar-label">{{ __('app.groups') }}</span>
                            </a>
                        </li>

                        <li class="sidebar-menu-item">
                            <a href="{{ route('items.index') }}"
                                class="sidebar-link {{ request()->routeIs('items.*') ? 'active' : '' }}">
                                <span class="sidebar-icon"><i class="bi bi-box-seam"></i></span>
                                <span class="sidebar-label">{{ __('app.items') }}</span>
                            </a>
                        </li>

                        <li class="sidebar-menu-item">
                            <a href="{{ route('borrows.index') }}"
                                class="sidebar-link {{ request()->routeIs('borrows.index') || request()->routeIs('borrows.borrow') || request()->routeIs('borrows.return') || request()->routeIs('borrows.update') || request()->routeIs('borrows.destroy') || request()->routeIs('borrows.undoReturn') ? 'active' : '' }}">
                                <span class="sidebar-icon"><i class="bi bi-arrow-left-right"></i></span>
                                <span class="sidebar-label">{{ __('app.manage_items') }}</span>
                            </a>
                        </li>

                        <li class="sidebar-menu-item">
                            <a href="{{ route('borrows.history') }}"
                                class="sidebar-link {{ request()->routeIs('borrows.history') ? 'active' : '' }}">
                                <span class="sidebar-icon"><i class="bi bi-clock-history"></i></span>
                                <span class="sidebar-label">{{ __('app.manage_item_history') }}</span>
                            </a>
                        </li>

                        <li class="sidebar-menu-item">
                            <a href="{{ route('submissions.index') }}"
                                class="sidebar-link {{ request()->routeIs('submissions.*') ? 'active' : '' }}">
                                <span class="sidebar-icon"><i class="bi bi-inbox"></i></span>
                                <span class="sidebar-label">{{ __('app.submissions') }}</span>

                                @if ($pendingSubmissionCount > 0)
                                <span class="sidebar-badge">{{ $pendingSubmissionCount }}</span>
                                @endif
                            </a>
                        </li>

                        @if ($canManageUsers)
                        <li class="sidebar-menu-item">
                            <a href="{{ route('users.index') }}"
                                class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                <span class="sidebar-icon"><i class="bi bi-person-gear"></i></span>
                                <span class="sidebar-label">{{ __('app.users') }}</span>
                            </a>
                        </li>
                        @endif

                        <li class="sidebar-menu-item">
                            <a href="{{ route('borrows.late_returns') }}"
                                class="sidebar-link {{ request()->routeIs('borrows.late_returns') ? 'active' : '' }}">
                                <span class="sidebar-icon"><i class="bi bi-hourglass-split"></i></span>
                                <span class="sidebar-label">{{ __('app.returned_late') }}</span>

                                @if ($lateReturnedCount > 0)
                                <span class="sidebar-badge">{{ $lateReturnedCount }}</span>
                                @endif
                            </a>
                        </li>

                        <li class="sidebar-menu-item">
                            <a href="{{ route('borrows.overdue') }}"
                                class="sidebar-link {{ request()->routeIs('borrows.overdue') ? 'active' : '' }}">
                                <span class="sidebar-icon"><i class="bi bi-exclamation-circle"></i></span>
                                <span class="sidebar-label">{{ __('app.overdue_borrow') }}</span>

                                @if ($overdueCount > 0)
                                <span class="sidebar-badge">{{ $overdueCount }}</span>
                                @endif
                            </a>
                        </li>
                    </ul>

                    <div class="mt-3 pt-2 border-top sidebar-language-switch">
                        <div class="small text-muted px-2 mb-2">{{ __('app.language') }}</div>
                        <div class="d-flex gap-2 px-2">
                            <a href="{{ route('language.switch', 'en') }}"
                                class="btn btn-sm btn-light w-100">{{ __('app.english') }}</a>
                            <a href="{{ route('language.switch', 'kh') }}"
                                class="btn btn-sm btn-light w-100">{{ __('app.khmer') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Desktop Sidebar --}}
        <div class="px-2 mt-2">
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a href="{{ route('admin.dashboard') }}"
                        class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <span class="sidebar-icon"><i class="bi bi-speedometer2"></i></span>
                        <span class="sidebar-label">{{ __('app.dashboard') }}</span>
                    </a>
                </li>

                <li class="sidebar-menu-item">
                    <a href="{{ route('students.index') }}"
                        class="sidebar-link {{ request()->routeIs('students.*') ? 'active' : '' }}">
                        <span class="sidebar-icon"><i class="bi bi-people"></i></span>
                        <span class="sidebar-label">{{ __('app.students') }}</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('teachers.index') }}"
                        class="sidebar-link {{ request()->routeIs('teachers.*') ? 'active' : '' }}">
                        <span class="sidebar-icon"><i class="bi bi-people-fill"></i></span>
                        <span class="sidebar-label">{{ __('app.teachers') }}</span>
                    </a>
                </li>

                <li class="sidebar-menu-item">
                    <a href="{{ route('items.index') }}"
                        class="sidebar-link {{ request()->routeIs('items.*') ? 'active' : '' }}">
                        <span class="sidebar-icon"><i class="bi bi-box-seam"></i></span>
                        <span class="sidebar-label">{{ __('app.items') }}</span>
                    </a>
                </li>

                <li class="sidebar-menu-item">
                    <a href="{{ route('groups.index') }}"
                        class="sidebar-link {{ request()->routeIs('groups.*') ? 'active' : '' }}">
                        <span class="sidebar-icon"><i class="bi bi-diagram-3"></i></span>
                        <span class="sidebar-label">{{ __('app.groups') }}</span>
                    </a>
                </li>

                <li class="sidebar-menu-item">
                    <a href="{{ route('borrows.index') }}"
                        class="sidebar-link {{ request()->routeIs('borrows.index') || request()->routeIs('borrows.borrow') || request()->routeIs('borrows.return') || request()->routeIs('borrows.update') || request()->routeIs('borrows.destroy') || request()->routeIs('borrows.undoReturn') ? 'active' : '' }}">
                        <span class="sidebar-icon"><i class="bi bi-arrow-left-right"></i></span>
                        <span class="sidebar-label">{{ __('app.manage_items') }}</span>
                    </a>
                </li>

                <li class="sidebar-menu-item">
                    <a href="{{ route('borrows.history') }}"
                        class="sidebar-link {{ request()->routeIs('borrows.history') ? 'active' : '' }}">
                        <span class="sidebar-icon"><i class="bi bi-clock-history"></i></span>
                        <span class="sidebar-label">{{ __('app.manage_item_history') }}</span>
                    </a>
                </li>

                <li class="sidebar-menu-item">
                    <a href="{{ route('submissions.index') }}"
                        class="sidebar-link {{ request()->routeIs('submissions.*') ? 'active' : '' }}">
                        <span class="sidebar-icon"><i class="bi bi-inbox"></i></span>
                        <span class="sidebar-label">{{ __('app.submissions') }}</span>

                        @if ($pendingSubmissionCount > 0)
                        <span class="sidebar-badge">{{ $pendingSubmissionCount }}</span>
                        @endif
                    </a>
                </li>

                @if ($canManageUsers)
                <li class="sidebar-menu-item">
                    <a href="{{ route('users.index') }}"
                        class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <span class="sidebar-icon"><i class="bi bi-person-gear"></i></span>
                        <span class="sidebar-label">{{ __('app.users') }}</span>
                    </a>
                </li>
                @endif

                <li class="sidebar-menu-item">
                    <a href="{{ route('borrows.late_returns') }}"
                        class="sidebar-link {{ request()->routeIs('borrows.late_returns') ? 'active' : '' }}">
                        <span class="sidebar-icon"><i class="bi bi-hourglass-split"></i></span>
                        <span class="sidebar-label">{{ __('app.returned_late') }}</span>

                        @if ($lateReturnedCount > 0)
                        <span class="sidebar-badge">{{ $lateReturnedCount }}</span>
                        @endif
                    </a>
                </li>

                <li class="sidebar-menu-item">
                    <a href="{{ route('borrows.overdue') }}"
                        class="sidebar-link {{ request()->routeIs('borrows.overdue') ? 'active' : '' }}">
                        <span class="sidebar-icon"><i class="bi bi-exclamation-circle"></i></span>
                        <span class="sidebar-label">{{ __('app.overdue_borrow') }}</span>

                        @if ($overdueCount > 0)
                        <span class="sidebar-badge">{{ $overdueCount }}</span>
                        @endif
                    </a>
                </li>
            </ul>

            <div class="mt-3 pt-2 border-top sidebar-language-switch">
                <div class="small text-muted px-2 mb-2">{{ __('app.language') }}</div>
                <div class="d-flex gap-2 px-2">
                    <a href="{{ route('language.switch', 'en') }}"
                        class="btn btn-sm btn-light w-100">{{ __('app.english') }}</a>
                    <a href="{{ route('language.switch', 'kh') }}"
                        class="btn btn-sm btn-light w-100">{{ __('app.khmer') }}</a>
                </div>
            </div>
        </div>
        <h6 class="text-center text-secondary opacity-50 mt-4" style="font-size: 0.75rem; letter-spacing: 1px;">
            RELEASE 1.2.1
        </h6>
    </aside>
    {{-- </div> --}}
    {{-- 
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const notificationBtn = document.getElementById('notification');
            const dropdown = document.getElementById('dropdown');

            if (notificationBtn && dropdown) {
                notificationBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dropdown.classList.toggle('show');
                });

                document.addEventListener('click', function(e) {
                    if (!dropdown.contains(e.target) && !notificationBtn.contains(e.target)) {
                        dropdown.classList.remove('show');
                    }
                });
            }
        });
    </script> --}}