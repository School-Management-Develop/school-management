@extends('backend.layout.master')
@section('title', 'Teachers')
@section('teacher_active', 'active')

@section('contents')
<div class="container-fluid" style="padding: 3% 1%">

    @include('backend.page.alerts.alert')

    {{-- Header --}}
    <div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h2 class="mb-1">{{ __('app.Teachers') }}</h2>
          
        </div>
        <button class="btn btn-dark d-flex align-items-center gap-2"
                data-bs-toggle="modal" data-bs-target="#addTeacherModal">
            <i class="bi bi-plus-lg"></i> {{ __('app.Add Teacher') }}
        </button>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">{{ __('app.Total Teachers') }}</div>
                        <div class="fs-3 fw-bold">{{ $stats['total'] }}</div>
                    </div>
                    <div class="bg-primary-subtle text-primary rounded-3 p-2">
                        <i class="bi bi-person-badge fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        {{-- <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Active</div>
                        <div class="fs-3 fw-bold text-success">{{ $stats['active'] }}</div>
                    </div>
                    <div class="bg-success-subtle text-success rounded-3 p-2">
                        <i class="bi bi-person-check fs-4"></i>
                    </div>
                </div>
            </div>
        </div> --}}
        {{-- <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Inactive</div>
                        <div class="fs-3 fw-bold text-secondary">{{ $stats['inactive'] }}</div>
                    </div>
                    <div class="bg-secondary-subtle text-secondary rounded-3 p-2">
                        <i class="bi bi-person-dash fs-4"></i>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>

    {{-- Search --}}
    <form method="GET" class="d-flex gap-2 mb-3" style="max-width:400px;">
        <div class="input-group">
            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
            <input type="text" name="q" value="{{ request('q') }}"
                   class="form-control" placeholder="Search name or phone…">
        </div>
        <button type="submit" class="btn btn-primary btn-sm">{{ __('app.search') }}</button>
        @if(request('q'))
            <a href="{{ route('teachers.index') }}" class="btn btn-danger btn-sm">{{ __('app.Reset') }}</a>
        @endif
    </form>

    {{-- Table --}}
    <div class="card border-0 shadow-sm rounded-4" style="overflow:hidden;">
    <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
                <thead class="border-bottom">
                    <tr>
                        <th style="width:50px;">#</th>
                        <th>{{ __('app.Name') }}</th>
                        <th>{{ __('app.Gender') }}</th>
                        <th>{{ __('app.Phone Number') }}</th>
                        <th>{{ __('app.Group') }}</th>
                        {{-- <th>{{ __('app.Status') }}</th> --}}
                        <th class="text-end" style="width:120px;">{{ __('app.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teachers as $key => $teacher)
                    <tr>
                        <td class="text-muted">
                            {{ ($teachers->currentPage() - 1) * $teachers->perPage() + $key + 1 }}
                        </td>
                        <td class="fw-semibold">{{ $teacher->student_name }}</td>
                        <td class="text-muted">{{ $teacher->gender }}</td>
                        <td>
                            @if(is_null($teacher->phone_number))
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill">Unknown</span>
                            @else
                                {{ $teacher->phone_number }}
                            @endif
                        </td>
                        <td>
                            <span class="badge rounded-pill"
                                  style="background:#ede9fe;color:#6d28d9;">
                                {{ $teacherGroup->group_name }}
                            </span>
                        </td>
                        {{-- <td>
                            @if($teacher->status == 1)
                                <span class="badge bg-success-subtle text-success rounded-pill px-3">Active</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3">Inactive</span>
                            @endif
                        </td> --}}
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                <button class="btn btn-sm btn-outline-secondary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editTeacherModal{{ $teacher->student_id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('teachers.destroy', $teacher->student_id) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete this teacher?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            {{-- <div style="font-size:28px;opacity:.3;margin-bottom:8px;">👩‍🏫</div> --}}
                            {{ __('app.No teachers found.') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($teachers->hasPages())
        <div class="px-4 py-3 border-top">
            {{ $teachers->links() }}
        </div>
        @endif
    </div>

</div>{{-- /container-fluid --}}

{{-- ════════════════ ADD MODAL ════════════════ --}}
<div class="modal fade" id="addTeacherModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="POST" action="{{ route('teachers.store') }}">
            @csrf
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">{{ __('app.Add Teacher') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">

                    <div class="col-12">
                        <label class="form-label fw-semibold">{{ __('app.teacher_name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="student_name" class="form-control"
                               value="{{ old('student_name') }}"
                               placeholder="teacher name" required>
                        @error('student_name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">{{ __('app.Gender') }} <span class="text-danger">*</span></label>
                        <select name="gender" class="form-select" required>
                            <option value="">-- Select --</option>
                            <option value="Male"   {{ old('gender') == 'Male'   ? 'selected' : '' }}>{{ __('app.male') }}</option>
                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>{{ __('app.female') }}</option>
                        </select>
                    </div>

                    {{-- <div class="col-md-6">
                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="1" {{ old('status','1') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status')     == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div> --}}

                    <div class="col-12">
                        <label class="form-label fw-semibold">{{ __('app.Phone Number') }}</label>
                        <input type="text" name="phone_number" id="add_phone"
                               class="form-control" value="{{ old('phone_number') }}"
                               placeholder="e.g. 012 345 678" maxlength="15" inputmode="numeric">
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox"
                                   id="add_phone_unknown" name="phone_unknown" value="1"
                                   {{ old('phone_unknown') ? 'checked' : '' }}>
                            <label class="form-check-label text-muted" for="add_phone_unknown">
                                {{ __('app.unknown_number') }}
                            </label>
                        </div>
                    </div>

                    {{-- Group info --}}
                    <div class="col-12">
                        <div class="rounded-3 p-3 d-flex align-items-center gap-2"
                             style="background:#f5f3ff;border:1px solid #ddd6fe;">
                            <i class="bi bi-info-circle" style="color:#6d28d9;"></i>
                            <span style="font-size:13px;color:#5b21b6;">
                                Auto-assigned to group <strong>{{ $teacherGroup->group_name }}</strong>
                            </span>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-dark">
                    <i class="bi bi-check2-circle me-1"></i> Save
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════ EDIT MODALS ════════════════ --}}
@foreach($teachers as $teacher)
<div class="modal fade" id="editTeacherModal{{ $teacher->student_id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="POST"
              action="{{ route('teachers.update', $teacher->student_id) }}">
            @csrf @method('PUT')
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Edit — {{ $teacher->student_name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">

                    <div class="col-12">
                        <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="student_name" class="form-control"
                               value="{{ $teacher->student_name }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Gender <span class="text-danger">*</span></label>
                        <select name="gender" class="form-select" required>
                            <option value="Male"   {{ $teacher->gender === 'Male'   ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ $teacher->gender === 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>

                    {{-- <div class="col-md-6">
                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="1" {{ $teacher->status == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ $teacher->status == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div> --}}

                    <div class="col-12">
                        <label class="form-label fw-semibold">Phone Number</label>
                        <input type="text" name="phone_number"
                               id="edit_phone_{{ $teacher->student_id }}"
                               class="form-control"
                               value="{{ $teacher->phone_number }}"
                               placeholder="e.g. 012 345 678" maxlength="15"
                               {{ is_null($teacher->phone_number) ? 'disabled' : '' }}>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox"
                                   id="edit_unknown_{{ $teacher->student_id }}"
                                   name="phone_unknown" value="1"
                                   {{ is_null($teacher->phone_number) ? 'checked' : '' }}>
                            <label class="form-check-label text-muted"
                                   for="edit_unknown_{{ $teacher->student_id }}">
                                Unknown number
                            </label>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="rounded-3 p-3 d-flex align-items-center gap-2"
                             style="background:#f5f3ff;border:1px solid #ddd6fe;">
                            <i class="bi bi-info-circle" style="color:#6d28d9;"></i>
                            <span style="font-size:13px;color:#5b21b6;">
                                Group: <strong>{{ $teacherGroup->group_name }}</strong> (auto-assigned)
                            </span>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-dark">
                    <i class="bi bi-check2-circle me-1"></i> Update
                </button>
            </div>
        </form>
    </div>
</div>
@endforeach

@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        new bootstrap.Modal(document.getElementById('addTeacherModal')).show();
    });
</script>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const addUnknown = document.getElementById('add_phone_unknown');
    const addPhone   = document.getElementById('add_phone');

    if (addUnknown && addPhone) {
        if (addUnknown.checked) {
            addPhone.disabled    = true;
            addPhone.placeholder = 'Unknown number';
        }
        addUnknown.addEventListener('change', function () {
            addPhone.disabled    = this.checked;
            addPhone.value       = this.checked ? '' : addPhone.value;
            addPhone.placeholder = this.checked ? 'Unknown number' : 'e.g. 012 345 678';
        });
    }

    document.querySelectorAll('[id^="edit_unknown_"]').forEach(function (checkbox) {
        const id    = checkbox.id.replace('edit_unknown_', '');
        const input = document.getElementById('edit_phone_' + id);
        if (!input) return;

        checkbox.addEventListener('change', function () {
            input.disabled    = this.checked;
            input.value       = this.checked ? '' : input.value;
            input.placeholder = this.checked ? 'Unknown number' : 'e.g. 012 345 678';
        });
    });

});
</script>
@endpush

@endsection