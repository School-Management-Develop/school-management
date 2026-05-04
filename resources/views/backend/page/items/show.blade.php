@extends('backend.layout.master')

@section('title', 'Item Details')
@section('item_active', 'active')

@section('contents')
    <div class="container-fluid py-4">

        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h3 class="fw-bold mb-0">{{ __('app.item_detail') }}</h3>
                <div class="text-secondary">{{ __('app.View full information for this item.') }}</div>
            </div>

            <a href="{{ route('items.index') }}" class="btn btn-light">
                <i class="bi bi-arrow-left me-1"></i> {{ __('app.back') }}
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <div class="row g-4">

                    {{-- Image + Status --}}
                    <div class="col-12 col-md-4">
                        <div class="border rounded-4 p-3 text-center">
                            <img class="rounded-4 border" style="width:100%;max-width:260px;height:260px;object-fit:cover;"
                                <img src="{{ asset('storage/' . $item->image) }}" alt="thumb"
                                alt="item">
                        </div>

                        <div class="mt-3">
                            @if ($item->status == 1)
                                <span
                                    class="badge rounded-pill bg-success text-white px-3 py-2">{{ __('app.item_active') }}</span>
                            @else
                                <span
                                    class="badge rounded-pill bg-secondary text-white px-3 py-2">{{ __('app.item_inactive') }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="col-12 col-md-8">



                        <form method="POST" action="{{ route('items.update', ['itemid' => $item->Itemid]) }}">
                            @csrf
                            @method('PUT')

                            <h4 class="fw-bold mb-2">{{ $item->display_name }}</h4>

                            <label class="form-label fw-semibold">{{ __('app.description') }}</label>
                            <textarea name="description" class="form-control" rows="4"
                                placeholder="{{ __('app.Write detail about this item...') }}">{{ old('description', $item->description) }}</textarea>
                            <br>
                            {{-- <div class="form-text mb-3">{{ __('app.You can update the description here.') }}</div> --}}



                        </form>
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <div class="border rounded-4 p-3">
                                    <div class="text-secondary small">{{ __('app.item_qty') }}</div>
                                    <div class="fs-4 fw-bold">{{ $item->qty  }}</div>
                                </div>
                            </div>

                            <div class="col-12 col-md-4">
                                <div class="border rounded-4 p-3">
                                    <div class="text-secondary small">{{ __('app.item_borrowed') }}</div>
                                    <div class="fs-4 fw-bold">{{ $borrowed }}</div>
                                </div>
                            </div>

                            <div class="col-12 col-md-4">
                                <div class="border rounded-4 p-3">
                                    <div class="text-secondary small">{{ __('app.item_available') }}</div>
                                    <div class="fs-4 fw-bold">{{ $item->qty - $borrowed }}</div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row g-3">
                            {{-- <div class="col-12 col-md-4">
                                <div class="text-secondary small">{{ __('app.Item ID') }}</div>
                                <div class="fw-semibold">{{ $item->Itemid }}</div>
                            </div> --}}

                            <div class="col-12 col-md-4">
                                <div class="text-secondary small">{{ __('app.created_at') }}</div>
                                <div class="fw-semibold">{{ $item->created_at }}</div>
                            </div>

                            <div class="col-12 col-md-4">
                                <div class="text-secondary small">{{ __('app.updated_at') }}</div>
                                <div class="fw-semibold">{{ $item->updated_at }}</div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <a href="{{ route('items.index') }}" class="btn btn-dark">
                                <i class="bi bi-list me-1"></i> {{ __('app.item') }}
                            </a>

                            <form action="{{ route('items.destroy', ['itemid' => $item->Itemid]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger"
                                    onclick="return confirm('Delete this item?')">
                                    <i class="bi bi-trash me-1"></i> {{ __('app.delete') }}
                                </button>
                            </form>
                        </div>

                    </div>

                </div>
            </div>
        </div>

    </div>
@endsection
