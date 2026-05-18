@extends('backend.layout.master')

@section('title', 'Groups')
@section('group_active', 'active')
@section('contents')

    {{-- Full width CSS (safe override) --}}
    <style>
        /* Force full width in case layout uses fixed containers */
        .content-wrapper,
        .content,
        .container,
        .container-lg,
        .container-md,
        .container-sm {
            max-width: 100% !important;
            width: 100% !important;
        }

        /* Optional: reduce card side spacing */
        .page-full-width {
            padding-left: 0 !important;
            padding-right: 0 !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
        }
    </style>

{{-- Alerts --}}
        <div id="alert-container" class="position-fixed top-0 end-0 p-3" style="z-index: 9999; max-width: 400px;">

            {{-- Success --}}
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

            {{-- Errors --}}
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
    <div class="container-fluid" style="padding: 3%;">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div>
                <h2 class="fw-bold mb-1">{{ __('app.groups') }}</h2>
                <div class="text-secondary">{{ __('app.Add, view, edit, and delete groups.') }}</div>
            </div>

            <div class="d-flex flex-wrap align-items-start gap-2">
                {{-- Search --}}
                <form method="GET" action="{{ url()->current() }}" class="d-flex flex-wrap gap-2 align-items-center">
                    <div class="input-group" style="min-width: 320px;">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search"></i>
                        </span>
                        <input
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            class="form-control border-start-0"
                            placeholder="{{ __('app.Search by group name...') }}"
                        >
                    </div>

                    <button class="btn btn-primary">{{ __('app.search') }}</button>
                    <a href="{{ url()->current() }}" class="btn btn-danger">{{ __('app.reset') }}</a>
                </form>

                {{-- Add --}}
                <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#addGroupModal">
                    <i class="bi bi-people me-1"></i> {{ __('app.Add Group') }}
                </button>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-4 w-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-secondary small">
                        {{ __('app.total_groups') }}: {{ $groups->total() }}
                    </span>
                </div>

                <h5 class="fw-bold mb-3" style="display:flex; justify-content: space-between;">
                        {{ __('app.groups') }}
                </h5>

                <div class="table-responsive">
                    <table class="table align-middle mb-0 w-100 table-hover">
                        <thead>
                            <tr class="text-secondary small">
                                <th style="width:80px;">#</th>
                                <th class="text-center">{{ __('app.Group Name') }}</th>
                                <th class="text-center" style="width:160px;">{{ __('app.Total Students') }}</th>
                                <th class="text-end" style="width:120px;">{{ __('app.Action') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($groups as $k => $g)
                                <tr>
                                    <td class="text-secondary">
                                        {{ $groups->firstItem() + $k }}
                                    </td>

                                    <td class="fw-semibold text-center">
                                        {{ $g->group_name }}
                                    </td>

                                    <td class="text-center fw-semibold">
                                        {{ $g->students_count }}
                                    </td>

                                    <td class="text-end">
                                        <form
                                            action="{{ route('groups.destroy', $g->group_id) }}"
                                            method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Delete this group?')"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-danger py-3">
                                        No groups found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 d-flex justify-content-end">
                    {{ $groups->onEachSide(1)->links('vendor.pagination.adminlte-simple') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Add Group Modal --}}
    <div class="modal fade" id="addGroupModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <form class="modal-content" method="POST" action="{{ route('groups.store') }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title fw-semibold">{{ __('app.Add Group') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <label class="form-label fw-semibold">
                        {{ __('app.Group Name') }} <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        name="group_name"
                        class="form-control"
                        placeholder="group"
                        required
                    >
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
@endsection