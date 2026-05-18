@extends('backend.layout.master')

@section('title', 'Students')
@section('student_active', 'active')

@section('contents')
    @include('backend.partials.alert')

    <div class="container-fluid py-4">

        {{-- Toast Alerts --}}
        <div id="alert-container" class="position-fixed top-0 end-0 p-3" style="z-index: 9999; max-width: 400px;">

            @if (session('success'))
                <div class="alert custom-toast alert-success border-0 border-start border-5 border-success shadow-sm rounded-4 fade show bg-white mb-3"
                    role="alert">
                    <div class="d-flex align-items-center p-2">
                        <div class="me-3 fs-4 text-success"><i class="bi bi-check-circle-fill"></i></div>
                        <div>
                            <strong class="d-block text-dark">Success</strong>
                            <span class="text-muted small">{{ session('success') }}</span>
                        </div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                    </div>
                    <div class="progress-loader bg-success"></div>
                </div>
            @endif

            @if ($errors->any())
                @foreach ($errors->all() as $e)
                    <div class="alert custom-toast alert-danger border-0 border-start border-5 border-danger shadow-sm rounded-4 fade show bg-white mb-2"
                        role="alert">
                        <div class="d-flex align-items-center p-2">
                            <div class="me-3 fs-4 text-danger"><strong>!</strong></div>
                            <div>
                                <strong class="d-block text-dark">Error</strong>
                                <span class="text-muted small">{{ $e }}</span>
                            </div>
                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                        </div>
                        <div class="progress-loader bg-danger"></div>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- Page Header --}}
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div>
                <h2 class="fw-bold mb-1">{{ __('app.students') }}</h2>
                <div class="text-secondary">{{ __('app.Add, view, edit, and delete students.') }}</div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <form method="GET" action="{{ url()->current() }}" class="d-flex gap-2 align-items-center">
                    <div class="input-group gap-2">
                        <span class="input-group-text bg-white border-end-0" style="margin-right:-2%">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control border-start-0"
                            placeholder="{{ __('app.Search student name...') }}" style="min-width: 320px;">
                        <button type="submit" class="btn btn-primary rounded">{{ __('app.search') }}</button>
                        <a href="{{ url()->current() }}" class="btn btn-danger rounded">{{ __('app.reset') }}</a>
                    </div>
                </form>

                <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                    <i class="bi bi-plus-lg me-1"></i> {{ __('app.Add Student') }}
                </button>
            </div>
        </div>

        {{-- Stat Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <div class="text-secondary small">{{ __('app.total_students') }}</div>
                        <div class="fs-3 fw-bold">{{ $statTotal ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <div class="text-secondary small">{{ __('app.active_students') }}</div>
                        <div class="fs-3 fw-bold">{{ $statActive ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <div class="text-secondary small">{{ __('app.inactive_students') }}</div>
                        <div class="fs-3 fw-bold">{{ $statInactive ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Students Table --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body" style="overflow: visible;">
                <div class="table-responsive" style="overflow: visible;">
                    <table class="table align-middle mb-0 table-hover">
                        <thead>
                            <tr class="text-secondary small">
                                <th style="width:60px;">#</th>
                                <th>{{ __('app.Student Name') }}</th>
                                <th>{{ __('app.Gender') }}</th>
                                <th>{{ __('app.Phone Number') }}</th>

                                {{-- Group Filter Column --}}
                                <th style="position: relative; overflow: visible;">
                                    <div class="position-relative d-inline-block" style="overflow: visible;">
                                        <button type="button"
                                            class="btn btn-link text-dark text-decoration-none p-0 fw-semibold small"
                                            id="groupFilterBtn" onclick="toggleGroupDropdown(event)">
                                            {{ __('app.Group') }}
                                            @if (request('group_id'))
                                                <span class="text-primary">
                                                    ({{ $groups->firstWhere('group_id', request('group_id'))?->group_name }})
                                                </span>
                                            @endif
                                            <i class="bi bi-chevron-down ms-1" style="font-size: 10px;"></i>
                                        </button>

                                        <div class="position-absolute bg-white border rounded-3 shadow-lg p-2 d-none"
                                            id="groupFilterDropdown"
                                            style="z-index: 1055; min-width: 220px; left: 0; top: calc(100% + 6px);">
                                            <input type="text" class="form-control form-control-sm rounded-3 mb-2"
                                                id="groupSearchInput" placeholder="{{ __('app.Search group...') }}"
                                                autocomplete="off" onkeyup="filterGroupList()">

                                            <div style="max-height: 200px; overflow-y: auto;" id="groupListContainer">
                                                <a href="{{ url()->current() . '?' . http_build_query(array_diff_key(request()->query(), ['group_id' => ''])) }}"
                                                    class="dropdown-item rounded-2 px-2 py-1 small {{ !request('group_id') ? 'fw-bold text-primary bg-light' : '' }}"
                                                    data-group-name="all groups">
                                                    {{ __('app.All Groups') }}
                                                </a>

                                                @foreach ($groups as $g)
                                                    <a href="{{ url()->current() . '?' . http_build_query(array_merge(request()->query(), ['group_id' => $g->group_id, 'page' => 1])) }}"
                                                        class="dropdown-item rounded-2 px-2 py-1 small {{ request('group_id') == $g->group_id ? 'fw-bold text-primary bg-light' : '' }}"
                                                        data-group-name="{{ strtolower($g->group_name) }}">
                                                        {{ $g->group_name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </th>

                                <th>{{ __('app.Status') }}</th>
                                <th>
                                    <div class="position-relative d-inline-block">
                                        <button type="button"
                                            class="btn btn-link text-dark text-decoration-none p-0 fw-semibold small"
                                            onclick="toggleBorrowDropdown(event)">
                                            {{ __('app.Borrow') }}
                                            @if (request('borrow_sort'))
                                                <span class="text-primary">
                                                    ({{ request('borrow_sort') === 'asc' ? __('app.Ascending') : __('app.Descending') }})
                                                </span>
                                            @endif
                                            <i class="bi bi-chevron-down ms-1" style="font-size: 10px;"></i>
                                        </button>

                                        <div class="position-absolute bg-white border rounded-3 shadow-lg d-none"
                                            id="borrowSortDropdown"
                                            style="z-index: 1050; width: 180px; left: 0; top: 100%; margin-top: 6px;">
                                            <div style="max-height: 220px; overflow-y: auto;" class="py-1">
                                                <a href="{{ url()->current() . '?' . http_build_query(array_diff_key(request()->query(), ['borrow_sort' => '', 'page' => ''])) }}"
                                                    class="d-block text-decoration-none px-3 py-2 small {{ !request('borrow_sort') ? 'fw-bold text-primary bg-light' : 'text-dark' }}">
                                                    {{ __('app.Default') }}
                                                </a>
                                                <a href="{{ url()->current() . '?' . http_build_query(array_merge(request()->query(), ['borrow_sort' => 'asc', 'page' => 1])) }}"
                                                    class="d-block text-decoration-none px-3 py-2 small {{ request('borrow_sort') === 'asc' ? 'fw-bold text-primary bg-light' : 'text-dark' }}">
                                                    <i class="bi bi-sort-up me-1"></i>{{ __('app.Ascending') }}
                                                </a>
                                                <a href="{{ url()->current() . '?' . http_build_query(array_merge(request()->query(), ['borrow_sort' => 'desc', 'page' => 1])) }}"
                                                    class="d-block text-decoration-none px-3 py-2 small {{ request('borrow_sort') === 'desc' ? 'fw-bold text-primary bg-light' : 'text-dark' }}">
                                                    <i class="bi bi-sort-down me-1"></i>{{ __('app.Descending') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                                <th class="text-center" style="width:180px;">{{ __('app.action') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($students as $key => $s)
                                <tr>
                                    <td class="text-secondary">
                                        {{ ($students->currentPage() - 1) * $students->perPage() + $key + 1 }}
                                    </td>
                                    <td class="fw-semibold">{{ $s->student_name }}</td>
                                    <td>{{ $s->gender ?: '-' }}</td>
                                    <td>{{ $s->phone_number ?: '-' }}</td>
                                    <td>{{ $s->group?->group_name ?: '-' }}</td>
                                    <td>
                                        @if ($s->status == 1)
                                            <span class="badge rounded-pill bg-success text-white px-3 py-2">
                                                {{ __('app.active_students') }}
                                            </span>
                                        @else
                                            <span class="badge rounded-pill bg-secondary text-white px-3 py-2">
                                                {{ __('app.inactive_students') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill bg-primary text-white px-3 py-2">
                                            {{ $s->borrows_count ?? 0 }} {{ __('app.times') }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-light btn-sm me-1" data-bs-toggle="modal"
                                            data-bs-target="#viewStudentModal{{ $s->student_id }}" title="View">
                                            <i class="bi bi-eye"></i>
                                        </button>

                                        <button type="button" class="btn btn-light btn-sm me-1" data-bs-toggle="modal"
                                            data-bs-target="#editStudentModal{{ $s->student_id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        <form action="{{ route('students.destroy', ['student_id' => $s->student_id]) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm"
                                                onclick="return confirm('{{ __('app.Delete this student?') }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-danger py-4">
                                        {{ __('app.No Data found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $students->onEachSide(1)->appends(request()->query())->links() }}
                </div>
            </div>
        </div>

    </div>

    {{-- ==================== Add Student Modal ==================== --}}
    <div class="modal fade" id="addStudentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <form class="modal-content" method="POST" action="{{ route('students.store') }}" id="addStudentForm">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title fw-semibold">{{ __('app.Add Student') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                {{ __('app.Student Name') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="student_name" class="form-control"
                                value="{{ old('student_name') }}" required>
                            @error('student_name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">
                                {{ __('app.Gender') }} <span class="text-danger">*</span>
                            </label>
                            <select name="gender" class="form-select" required>
                                <option value="">-- {{ __('app.select_gender') }} --</option>
                                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>
                                    {{ __('app.male') }}</option>
                                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>
                                    {{ __('app.female') }}</option>
                            </select>
                            @error('gender')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">{{ __('app.Phone Number') }}</label>
                            <input type="text" name="phone_number" id="add_phone_number" class="form-control"
                                value="{{ old('phone_number') }}" maxlength="10" inputmode="numeric" required>
                            <div id="add_phone_error" class="text-danger small mt-1"></div>
                            @error('phone_number')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">{{ __('app.Group') }}</label>
                            <select name="group_id" class="form-select" required>
                                <option value="">-- {{ __('app.select_group') }} --</option>
                                @foreach ($groups as $g)
                                    <option value="{{ $g->group_id }}"
                                        {{ old('group_id') == $g->group_id ? 'selected' : '' }}>
                                        {{ $g->group_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('group_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">
                                {{ __('app.Status') }} <span class="text-danger">*</span>
                            </label>
                            <select name="status" class="form-select rounded-3 py-2" required>
                                <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>
                                    {{ __('app.active') }}</option>
                                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>
                                    {{ __('app.inactive') }}</option>
                            </select>
                            @error('status')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('app.Cancel') }}</button>
                    <button class="btn btn-dark">
                        <i class="bi bi-check2-circle me-1"></i> {{ __('app.Save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ==================== Edit Student Modals ==================== --}}
    @foreach ($students as $s)
        <div class="modal fade" id="editStudentModal{{ $s->student_id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <form class="modal-content" method="POST"
                    action="{{ route('students.update', ['student_id' => $s->student_id]) }}">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title fw-semibold">{{ __('app.edit') }} {{ __('app.students') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">

                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    {{ __('app.Student Name') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="student_name" class="form-control"
                                    value="{{ old('student_name', $s->student_name) }}" required>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">
                                    {{ __('app.Gender') }} <span class="text-danger">*</span>
                                </label>
                                <select name="gender" class="form-select" required>
                                    <option value="Male" {{ old('gender', $s->gender) == 'Male' ? 'selected' : '' }}>
                                        Male</option>
                                    <option value="Female" {{ old('gender', $s->gender) == 'Female' ? 'selected' : '' }}>
                                        Female</option>
                                </select>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">{{ __('app.Phone Number') }}</label>
                                <input type="text" name="phone_number" class="form-control"
                                    value="{{ old('phone_number', $s->phone_number) }}">
                                @error('phone_number')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    {{ __('app.Group') }} <span class="text-danger">*</span>
                                </label>
                                <select name="group_id" class="form-select" required>
                                    <option value="">-- {{ __('app.Select Group') }} --</option>
                                    @foreach ($groups as $g)
                                        <option value="{{ $g->group_id }}"
                                            {{ (string) old('group_id', $s->group_id) === (string) $g->group_id ? 'selected' : '' }}>
                                            {{ $g->group_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">
                                    {{ __('app.Status') }} <span class="text-danger">*</span>
                                </label>
                                <select name="status" class="form-select rounded-3 py-2" required>
                                    <option value="1" {{ old('status', $s->status) == 1 ? 'selected' : '' }}>Active
                                    </option>
                                    <option value="0" {{ old('status', $s->status) == 0 ? 'selected' : '' }}>
                                        Inactive</option>
                                </select>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light"
                            data-bs-dismiss="modal">{{ __('app.Cancel') }}</button>
                        <button class="btn btn-dark">
                            <i class="bi bi-check2-circle me-1"></i> {{ __('app.Update') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    {{-- ==================== View Student Modals ==================== --}}
    @foreach ($students as $s)
        <div class="modal fade" id="viewStudentModal{{ $s->student_id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow rounded-4">

                    <div class="modal-header">
                        <h5 class="modal-title fw-semibold">{{ __('app.Student Detail') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-4">

                            <div class="col-12">
                                <div class="p-3 rounded-4 bg-light border">
                                    <h4 class="fw-bold mb-1">{{ $s->student_name }}</h4>
                                    <div class="text-secondary">{{ $s->group?->group_name ?: '-' }}</div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="text-secondary small mb-1 d-block">{{ __('app.Student Name') }}</label>
                                <div class="fw-semibold">{{ $s->student_name ?: '-' }}</div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="text-secondary small mb-1 d-block">{{ __('app.Gender') }}</label>
                                <div class="fw-semibold">{{ $s->gender ?: '-' }}</div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="text-secondary small mb-1 d-block">{{ __('app.Phone Number') }}</label>
                                <div class="fw-semibold">{{ $s->phone_number ?: '-' }}</div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="text-secondary small mb-1 d-block">{{ __('app.Group') }}</label>
                                <div class="fw-semibold">{{ $s->group?->group_name ?: '-' }}</div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="text-secondary small mb-1 d-block">{{ __('app.Status') }}</label>
                                <div>
                                    @if ($s->status == 1)
                                        <span class="badge rounded-pill bg-success text-white px-3 py-2">Active</span>
                                    @else
                                        <span class="badge rounded-pill bg-secondary text-white px-3 py-2">Inactive</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="text-secondary small mb-1 d-block">{{ __('app.created_at') }}</label>
                                <div class="fw-semibold">
                                    {{ $s->created_at ? $s->created_at->timezone('Asia/Jakarta')->format('d M Y H:i') : '-' }}
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="text-secondary small mb-1 d-block">{{ __('app.updated_at') }}</label>
                                <div class="fw-semibold">
                                    {{ $s->updated_at ? $s->updated_at->timezone('Asia/Jakarta')->format('d M Y H:i') : '-' }}
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light"
                            data-bs-dismiss="modal">{{ __('app.close') }}</button>
                    </div>

                </div>
            </div>
        </div>
    @endforeach

    {{-- Auto-open Add modal on validation error --}}
    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                new bootstrap.Modal(document.getElementById('addStudentModal')).show();
            });
        </script>
    @endif

    <script>
        // ── Phone validation (Add form) ──────────────────────────────────
        document.addEventListener('DOMContentLoaded', function() {
            const addForm = document.getElementById('addStudentForm');
            const phoneInput = document.getElementById('add_phone_number');
            const phoneError = document.getElementById('add_phone_error');

            if (!addForm || !phoneInput || !phoneError) return;

            phoneInput.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '').slice(0, 10);
                phoneError.textContent = '';
                this.setCustomValidity('');
            });

            phoneInput.addEventListener('keypress', function(e) {
                if (!/[0-9]/.test(e.key)) e.preventDefault();
            });

            addForm.addEventListener('submit', function(e) {
                const value = phoneInput.value.trim();
                phoneError.textContent = '';
                phoneInput.setCustomValidity('');

                if (value !== '' && !/^0[0-9]{8,9}$/.test(value)) {
                    e.preventDefault();
                    phoneError.textContent =
                        '{{ __('app.Phone number must be 9 or 10 digits and start with 0.') }}';
                    phoneInput.setCustomValidity('Invalid phone number');
                    phoneInput.reportValidity();
                }
            });
        });

        // ── Group filter dropdown ────────────────────────────────────────
        function toggleGroupDropdown(e) {
            e.stopPropagation();
            const dropdown = document.getElementById('groupFilterDropdown');
            dropdown.classList.toggle('d-none');
            if (!dropdown.classList.contains('d-none')) {
                document.getElementById('groupSearchInput').focus();
            }
        }

        function filterGroupList() {
            const search = document.getElementById('groupSearchInput').value.toLowerCase();
            document.querySelectorAll('#groupListContainer a').forEach(item => {
                const name = item.getAttribute('data-group-name') || '';
                item.style.display = name.includes(search) ? '' : 'none';
            });
        }

        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('groupFilterDropdown');
            const btn = document.getElementById('groupFilterBtn');
            if (dropdown && !dropdown.contains(e.target) && e.target !== btn) {
                dropdown.classList.add('d-none');
            }
        });

        function toggleBorrowDropdown(e) {
            e.stopPropagation();
            var dropdown = document.getElementById('borrowSortDropdown');
            dropdown.classList.toggle('d-none');
        }

        document.addEventListener('click', function(e) {
            var dropdown = document.getElementById('borrowSortDropdown');
            if (dropdown && !dropdown.contains(e.target)) {
                dropdown.classList.add('d-none');
            }
        });
    </script>

@endsection
