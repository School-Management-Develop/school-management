@extends('backend.layout.master')

@section('title', 'Items')
@section('item_active', 'active')

@section('contents')

<div class="container-fluid py-4">

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

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">{{ __('app.items') }}</h2>
            <div class="text-secondary">{{ __('app.Add, view, edit and delete items') }}</div>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            {{-- Search --}}
            <form method="GET" action="{{ url()->current() }}" class="d-flex">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control border-start-0"
                        placeholder="{{ __('app.Search by item name...') }}" style="min-width: 320px;">
                </div>
            </form>

            {{-- Add --}}
            <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#addItemModal">
                <i class="bi bi-plus-lg me-1"></i> {{ __('app.add_item') }}
            </button>
        </div>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="text-secondary small">{{ __('app.total_items') }}</div>
                    <div class="fs-3 fw-bold">{{ $statTotal ?? 0 }}</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="text-secondary small">{{ __('app.item_active') }}</div>
                    <div class="fs-3 fw-bold">{{ $statActive ?? 0 }}</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="text-secondary small">{{ __('app.item_inactive') }}</div>
                    <div class="fs-3 fw-bold">{{ $statInactive ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr class="text-secondary small">
                            <th style="width:60px;">#</th>
                            <th style="width:90px;">{{ __('app.Image') }}</th>
                            <th>{{ __('app.item_name') }}</th>
                            <th class="text-end">{{ __('app.item_borrowed') }}</th>
                            <th class="text-end">{{ __('app.item_available') }}</th>
                            <th class="text-center">{{ __('app.item_qty') }}</th>
                            <th>{{ __('app.item_status') }}</th>
                            <th class="text-end" style="width:180px;">{{ __('app.Actions') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($items as $key => $it)
                        <tr>
                            <td class="text-secondary">{{ $key + 1 }}</td>

                            <td>
                                <img class="rounded-3 border" style="width:44px;height:44px;object-fit:cover;"
                                    src="{{ $it->image ? Storage::url($it->image) : '' }}" alt="thumb">
                            </td>

                            <td>
                                <div class="fw-semibold">{{ $it->display_name }}</div>
                            </td>

                            <td class="fw-medium text-end">{{ $it->borrowed_qty ?? 0 }}</td>

                            <td class="text-end fw-semibold">@php $available = max(0, ($it->qty ?? 0) -
                                ($it->borrowed_qty ?? 0)); @endphp
                                {{ $available }}</td>

                            <td class="text-center">{{ $it->qty }}</td>

                            <td>
                                @if ($it->status == 1)
                                <span
                                    class="badge rounded-pill bg-success text-white px-3 py-2">{{ __('app.item_active') }}</span>
                                @else
                                <span
                                    class="badge rounded-pill bg-secondary text-white px-3 py-2">{{ __('app.item_inactive') }}</span>
                                @endif
                            </td>

                            <td class="text-end">
                                <a href="{{ route('items.show', ['itemid' => $it->Itemid]) }}"
                                    class="btn btn-light btn-sm me-1" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                {{-- Edit --}}
                                <button class="btn btn-light btn-sm me-1" data-bs-toggle="modal"
                                    data-bs-target="#editItemModal{{ $it->Itemid }}">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                {{-- Delete --}}
                                {{-- <button type="button" class="btn btn-danger"
                                            onclick="confirmDelete({{ $it->Itemid }})">
                                Delete Item
                                </button> --}}
                                {{-- <form action="{{ route('items.destroy', ['itemid' => $it->Itemid]) }}"
                                method="POST" >
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="password" id="password-{{ $it->Itemid }}">
                                </form> --}}
                                <form id="delete-form-{{ $it->Itemid }}"
                                    action="{{ route('items.destroy', ['itemid' => $it->Itemid]) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')

                                    <input type="hidden" name="password" id="password-{{ $it->Itemid }}">

                                    <button class="btn btn-outline-danger btn-sm"
                                        onclick="confirmDelete({{ $it->Itemid }})" type="button">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-danger py-4">{{ __('app.No Data found') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $items->links() }}
            </div>

        </div>
    </div>

</div>

{{-- Add Item Modal --}}
<div class="modal fade" id="addItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <form class="modal-content" method="POST" action="{{ route('items.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="modal-header">
                <h5 class="modal-title fw-semibold">{{ __('app.add_item') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row g-4">

                    <div class="col-12 col-md-5">
                        <label class="form-label fw-semibold">{{ __('app.item_image') }}</label>

                        <div class="border border-2 border-secondary-subtle rounded-4 p-3">
                            <div class="d-flex gap-3 align-items-start">
                                <div class="border rounded-3 d-flex align-items-center justify-content-center"
                                    style="width:54px;height:54px;">
                                    <i class="bi bi-image fs-4 text-secondary"></i>
                                </div>

                                <div class="flex-grow-1">
                                    <div class="fw-semibold">{{ __('app.Upload image') }}</div>
                                    <div class="text-secondary small">JPG/PNG/WebP. Max 2MB.</div>

                                    <input class="form-control mt-3" type="file" name="image" accept="image/*">
                                    {{-- <div class="form-text">Saved to <code>storage/app/public/items</code>.</div> --}}
                                </div>
                            </div>

                            <div class="d-flex gap-2 mt-3">
                                <button type="reset" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-x-circle me-1"></i> {{ __('app.Remove image') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-7">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('app.item_name_en') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('app.item_name_kh') }}</label>
                            <input type="text" name="name_kh" class="form-control" value="{{ old('name_kh') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('app.item_description') }}</label>
                            <textarea name="description" class="form-control" rows="4"
                                placeholder="{{ __('app.Write detail about this item...') }}">{{ old('description') }}</textarea>
                            {{-- <div class="form-text">Example: USB flash drive 32GB used for lab computers.</div> --}}
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold">{{ __('app.item_qty') }} <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="qty" class="form-control" min="0" step="1" value="0"
                                    required>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold">{{ __('app.item_status') }} <span
                                        class="text-danger">*</span></label>
                                <select name="status" class="form-select rounded-3 py-2" required>
                                    <option value="1" selected>{{ __('app.item_active') }}</option>
                                    <option value="0">{{ __('app.item_inactive') }}</option>
                                </select>
                            </div>
                        </div>

                        {{-- <div class="text-secondary small">
                                Note: <code>available</code> and <code>borrow</code> will be saved as <b>0</b>
                                automatically.
                            </div> --}}
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-dark">
                    <i class="bi bi-check2-circle me-1"></i> Save
                </button>
            </div>

        </form>
    </div>
</div>

{{-- ✅ Edit Modals (OUTSIDE TABLE, rendered once) --}}
@foreach ($items as $it)
<div class="modal fade" id="editItemModal{{ $it->Itemid }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <form class="modal-content" method="POST" action="{{ route('items.update', ['itemid' => $it->Itemid]) }}"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="modal-header">
                <h5 class="modal-title fw-semibold">{{ __('app.edit_item') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row g-4">

                    <div class="col-12 col-md-5">
                        <label class="form-label fw-semibold">{{ __('app.item_image') }}</label>

                        <div class="border border-2 border-secondary-subtle rounded-4 p-3">
                            <div class="mb-3">
                                <img class="rounded-3 border" style="width:80px;height:80px;object-fit:cover;"
                                    src="{{ $it->image ? Storage::url($it->image) : '' }}" alt="thumb">
                            </div>

                            <input class="form-control" type="file" name="image" accept="image/*">
                            <div class="form-text">{{ __('app.Leave empty to keep current image.') }}</div>
                        </div>
                    </div>

                    <div class="col-12 col-md-7">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('app.item_name_en') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $it->name) }}"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('app.item_name_kh') }}</label>
                            <input type="text" name="name_kh" class="form-control"
                                value="{{ old('name_kh', $it->name_kh) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('app.item_description') }}</label>
                            <textarea name="description" class="form-control" rows="4"
                                placeholder="{{ __('app.Write detail about this item...') }}">{{ old('description', $it->description) }}</textarea>
                            {{-- <div class="form-text">{{ __('app.Example: USB flash drive 32GB used for lab computers.') }}
                        </div> --}}
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold">{{ __('app.item_qty') }} <span
                                    class="text-danger">*</span></label>
                            <input type="number" name="qty" class="form-control" min="0" step="1" value="{{ $it->qty }}"
                                required>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold">{{ __('app.item_status') }} <span
                                    class="text-danger">*</span></label>
                            <select name="status" class="form-select rounded-3 py-2" required>
                                <option value="1" {{ $it->status == 1 ? 'selected' : '' }}>
                                    {{ __('app.item_active') }}</option>
                                </option>
                                <option value="0" {{ $it->status == 0 ? 'selected' : '' }}>
                                    {{ __('app.item_inactive') }}
                                </option>
                            </select>
                        </div>

                    </div>


                    {{-- <div class="text-secondary small">
                                    Note: <code>available</code> and <code>borrow</code> are not changed here.
                                </div> --}}
                </div>

            </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-dark">
            <i class="bi bi-check2-circle me-1"></i> Update
        </button>
    </div>

    </form>
</div>
</div>
@endforeach

{{-- Delete Confirmation with Password --}}
<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Confirm Deletion',
        text: "Please enter your password to confirm.",
        input: 'password',
        inputPlaceholder: 'Enter your password',
        showCancelButton: true,
        confirmButtonText: 'Confirm Delete',
        confirmButtonColor: '#dc3545',
        preConfirm: (password) => {
            if (!password) {
                Swal.showValidationMessage('Password is required');
            }
            return password;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Find the hidden input and the form using the ID passed to the function
            const passwordInput = document.getElementById('password-' + id);
            const form = document.getElementById('delete-form-' + id);

            if (passwordInput && form) {
                passwordInput.value = result.value; // Put the password in the hidden field
                form.submit(); // Now the controller will receive the password!
            } else {
                console.error("Could not find the form or password input for ID: " + id);
            }
        }
    });
}
</script>

@endsection