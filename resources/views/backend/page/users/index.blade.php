@extends('backend.layout.master')

@section('title', 'Users')
@section('user_active', 'active')

@section('contents')
    <style>
        .tg-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 500;
            background: #E3F2FD;
            color: #0088cc;
            border: 1px solid #BBDEFB;
        }

        .tg-active-label {
            background: #0088cc;
            color: #fff;
            border: none;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            margin-left: 2px;
        }

        .tg-remove {
            background: none;
            border: none;
            color: #999;
            cursor: pointer;
            font-size: 16px;
            padding: 0 2px;
            margin-left: 2px;
            line-height: 1;
        }

        .tg-remove:hover {
            color: #DC2626;
        }

        .tg-add {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 12px;
            border: 1px dashed #ccc;
            color: #999;
            background: none;
            cursor: pointer;
            transition: all 0.15s;
        }

        .tg-add:hover {
            border-color: #0088cc;
            color: #0088cc;
            background: #E3F2FD;
        }
    </style>

    <div class="container-fluid py-4">

        @include('backend.page.alerts.alert')

        {{-- @if ($errors->any())
            <div class="alert alert-danger rounded-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif --}}

        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div>
                <h2 class="fw-bold mb-1">{{ __('app.users') }}</h2>
                <div class="text-secondary">{{ __('app.Manage system users (admin accounts).') }}</div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <form method="GET" action="{{ url()->current() }}" class="d-flex">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control border-start-0"
                            placeholder="{{ __('app.Search name or email...') }}" style="min-width: 320px;">
                    </div>
                    <button class="btn btn-primary ms-2">{{ __('app.search') }}</button>
                </form>
                <a href="{{ url()->current() }}" class="btn btn-danger">{{ __('app.reset') }}</a>

                <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-plus-lg me-1"></i> {{ __('app.Add User') }}
                </button>
                <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#restoreUserModal">
                    <i class="bi bi-arrow-clockwise me-1"></i> {{ __('app.Restore Users') }}
                </button>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <div class="text-secondary small">{{ __('app.Total Users') }}</div>
                        <div class="fs-3 fw-bold">{{ $statTotal ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover">
                        <thead>
                            <tr class="text-secondary small">
                                <th style="width:60px;">#</th>
                                <th>{{ __('app.Name') }}</th>
                                <th>{{ __('app.Email') }}</th>
                                <th>{{ __('app.Role') }}</th>
                                <th>{{ __('app.Telegram') }}</th>
                                <th>{{ __('app.Status') }}</th>
                                <th class="text-end" style="width:180px;">{{ __('app.Actions') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($users as $key => $u)
                                <tr>
                                    <td class="text-secondary">{{ $users->firstItem() + $key }}</td>
                                    <td class="fw-semibold">{{ $u->name }}</td>
                                    <td>{{ $u->email }}</td>
                                    <td>{{ $u->role ?? 'Admin' }}</td>

                                    {{-- Telegram --}}
                                    <td>
                                        @if (strtolower($u->role ?? '') === 'admin' ||
                                                strtolower($u->role ?? '') === 'super admin' ||
                                                strtolower($u->role ?? '') === 'superadmin')
                                            @if ($u->telegram_username)
                                                <div class="d-flex align-items-center gap-1">
                                                    <div class="tg-badge">
                                                        <i class="bi bi-telegram"></i>
                                                        {{ $u->telegram_username }}
                                                        <span class="tg-active-label">ACTIVE</span>
                                                    </div>

                                                    <button class="tg-add" style="border-style:solid; padding:3px 6px;"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editTelegramModal{{ $u->id }}"
                                                        title="Edit">
                                                        <i class="bi bi-pencil" style="font-size:11px;"></i>
                                                    </button>

                                                    <form method="POST"
                                                        action="{{ route('users.telegram.remove', $u->id) }}"
                                                        class="d-inline"
                                                        onsubmit="return confirm('Remove Telegram for {{ $u->name }}?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="tg-remove"
                                                            title="Remove">&times;</button>
                                                    </form>
                                                </div>
                                            @else
                                                <button class="tg-add" data-bs-toggle="modal"
                                                    data-bs-target="#addTelegramModal{{ $u->id }}">
                                                    <i class="bi bi-plus"></i> {{ __('app.Add Telegram') }}
                                                </button>
                                            @endif
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if (($u->status ?? 1) == 1)
                                            <span class="badge rounded-pill bg-success text-white px-3 py-2">Active</span>
                                        @else
                                            <span
                                                class="badge rounded-pill bg-secondary text-white px-3 py-2">Inactive</span>
                                        @endif
                                    </td>

                                    <td class="text-end">
                                        <button class="btn btn-light btn-sm me-1" data-bs-toggle="modal"
                                            data-bs-target="#editUserModal{{ $u->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        <form action="{{ route('users.destroy', $u->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-outline-danger btn-sm"
                                                onclick="return confirm('Delete this user?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-danger py-4">No Users found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $users->onEachSide(1)->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Add User Modal --}}
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <form class="modal-content" method="POST" action="{{ route('users.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold">{{ __('app.Add User') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">{{ __('app.Name') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">{{ __('app.Email') }} <span
                                    class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                                required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">{{ __('app.Password') }} <span
                                    class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" minlength="6" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ __('app.Role') }} <span
                                    class="text-danger">*</span></label>
                            <select name="role" class="form-select" required>
                                <option value="admin" {{ old('role', 'admin') == 'admin' ? 'selected' : '' }}>Admin
                                </option>
                                <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ __('app.Status') }} <span
                                    class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="1" selected>Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-dark"><i class="bi bi-check2-circle me-1"></i> Save</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit User Modals --}}
    @foreach ($users as $u)
        <div class="modal fade" id="editUserModal{{ $u->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <form class="modal-content" method="POST" action="{{ route('users.update', $u->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title fw-semibold">{{ __('app.Edit User') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">{{ __('app.Name') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ $u->name }}"
                                    required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">{{ __('app.Email') }} <span
                                        class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" value="{{ $u->email }}"
                                    required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">{{ __('app.Password') }} <small
                                        class="text-muted">(optional)</small></label>
                                <input type="password" name="password" class="form-control" minlength="6">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                                <select name="role" class="form-select" required>
                                    <option value="admin" {{ old('role', $u->role) == 'admin' ? 'selected' : '' }}>Admin
                                    </option>
                                    <option value="staff" {{ old('role', $u->role) == 'staff' ? 'selected' : '' }}>Staff
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="1" {{ old('status', $u->status ?? 1) == 1 ? 'selected' : '' }}>
                                        Active</option>
                                    <option value="0" {{ old('status', $u->status ?? 1) == 0 ? 'selected' : '' }}>
                                        Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-dark"><i class="bi bi-check2-circle me-1"></i> Update</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    {{-- Add Telegram Modals --}}
    @foreach ($users as $u)
        @if (!$u->telegram_username)
            <div class="modal fade" id="addTelegramModal{{ $u->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <form class="modal-content border-0 shadow rounded-4" method="POST"
                        action="{{ route('users.telegram.update', $u->id) }}">
                        @csrf
                        @method('PATCH')
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold">
                                <i class="bi bi-telegram me-2" style="color:#0088cc;"></i>{{ __('app.Add Telegram') }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="p-3 rounded-4 bg-light border mb-3">
                                <div class="fw-semibold">{{ $u->name }}</div>
                                <div class="text-muted small">{{ $u->email }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">{{ __('app.Telegram username') }} <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">t.me/</span>
                                    <input type="text" name="telegram_username" class="form-control"
                                        placeholder="Ex: @ admin" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-light"
                                data-bs-dismiss="modal">{{ __('app.Cancel') }}</button>
                            <button type="submit" class="btn btn-dark"><i class="bi bi-check2-circle me-1"></i>
                                {{ __('app.Save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    @endforeach

    {{-- Edit Telegram Modals --}}
    @foreach ($users as $u)
        @if ($u->telegram_username)
            <div class="modal fade" id="editTelegramModal{{ $u->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <form class="modal-content border-0 shadow rounded-4" method="POST"
                        action="{{ route('users.telegram.update', $u->id) }}">
                        @csrf
                        @method('PATCH')
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold">
                                <i class="bi bi-telegram me-2" style="color:#0088cc;"></i>{{ __('app.Edit Telegram') }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="p-3 rounded-4 bg-light border mb-3">
                                <div class="fw-semibold">{{ $u->name }}</div>
                                <div class="text-muted small">{{ $u->email }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">{{ __('app.Telegram username') }} <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">t.me/</span>
                                    <input type="text" name="telegram_username" class="form-control"
                                        value="{{ $u->telegram_username }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-light"
                                data-bs-dismiss="modal">{{ __('app.Cancel') }}</button>
                            <button type="submit" class="btn btn-dark"><i class="bi bi-check2-circle me-1"></i>
                                {{ __('app.Update') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    @endforeach
            {{-- Restore Users Modal --}}
<div class="modal fade" id="restoreUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-arrow-clockwise me-2"></i>{{ __('app.Restore Users') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="trashedUsersBody">
                <div class="text-center py-3">
                    <div class="spinner-border spinner-border-sm text-primary"></div> Loading...
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('app.close') }}</button>
            </div>
        </div>
    </div>
</div>
<script>
    document.getElementById('restoreUserModal')?.addEventListener('show.bs.modal', function () {
        var body = document.getElementById('trashedUsersBody');
        body.innerHTML = '<div class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary"></div> Loading...</div>';

        fetch('{{ route("users.trashed") }}', {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            body.innerHTML = '<div class="table-responsive"><table class="table align-middle mb-0"><thead><tr class="text-secondary small"><th>#</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Deleted at</th><th class="text-end">Action</th></tr></thead><tbody>' + data.html + '</tbody></table></div>';
        })
        .catch(() => {
            body.innerHTML = '<div class="text-center text-danger py-3">Failed to load</div>';
        });
    });
</script>
@endsection
