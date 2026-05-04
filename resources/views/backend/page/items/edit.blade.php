@extends('Backend.layout.master')

@section('title', 'Edit Item')
@section('contents')
<div class="container-fluid py-4">

  {{-- Alerts --}}
  @if(session('success'))
    <div class="alert alert-success rounded-4">{{ session('success') }}</div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger rounded-4">
      <ul class="mb-0">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">

      <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="fw-bold mb-0">Edit Item</h4>
        <a href="{{ url('/items') }}" class="btn btn-light">Back</a>
      </div>

      <form method="POST"
            action="{{ route('items.update', ['itemid' => $item->Itemid]) }}"
            enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">

          {{-- LEFT: Image --}}
          <div class="col-12 col-md-5">
            <label class="form-label fw-semibold">Item Image</label>

            <div class="border border-2 border-secondary-subtle rounded-4 p-3">
              <div class="mb-3">
                <img class="rounded-3 border"
                     style="width:90px;height:90px;object-fit:cover;"
                     src="{{ $item->image ? Storage::url($item->image) : asset('assets/img/no-image.png') }}"
                     alt="item">
              </div>

              <input type="file" name="image" class="form-control" accept="image/*">
              <div class="form-text">Leave empty to keep current image.</div>
            </div>
          </div>

          {{-- RIGHT: Fields --}}
          <div class="col-12 col-md-7">
            <div class="mb-3">
              <label class="form-label fw-semibold">Item Name <span class="text-danger">*</span></label>
              <input type="text"
                     name="name"
                     class="form-control"
                     value="{{ old('name', $item->name) }}"
                     required>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Quantity <span class="text-danger">*</span></label>
                <input type="number"
                       name="qty"
                       class="form-control"
                       min="0"
                       value="{{ old('qty', $item->qty) }}"
                       required>
              </div>

              <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select rounded-3 py-2" required>
                  <option value="1" {{ old('status', $item->status) == 1 ? 'selected' : '' }}>Active</option>
                  <option value="0" {{ old('status', $item->status) == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
              </div>
            </div>

            <div class="d-flex gap-2 mt-4">
              <a href="{{ url('/items') }}" class="btn btn-light">Cancel</a>
              <button type="submit" class="btn btn-dark">
                <i class="bi bi-check2-circle me-1"></i> Update
              </button>
            </div>

          </div>
        </div>

      </form>

    </div>
  </div>

</div>
@endsection