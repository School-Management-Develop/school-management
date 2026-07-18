@extends('backend.layout.master')

@section('title', 'Staff Ranking')

@section('contents')
    <style>
        .ranking-card .table tbody tr:first-child td {
            font-weight: 700;
        }

        .rank-badge {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
            background: #f1f5f9;
            color: #475569;
        }

        .rank-badge.rank-1 {
            background: #fde68a;
            color: #92400e;
        }

        .rank-badge.rank-2 {
            background: #e2e8f0;
            color: #334155;
        }

        .rank-badge.rank-3 {
            background: #fdba74;
            color: #7c2d12;
        }
    </style>

    <div class="container-fluid py-4">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
                    <div>
                        <h2 class="fw-bold mb-1">{{ __('app.staff_ranking') }}</h2>
                        <p class="text-muted mb-0">Top staff ranked by borrow approvals, returns, and calls handled.
                        </p>
                    </div>

                    <form method="GET" action="{{ url()->current() }}" class="d-flex flex-wrap gap-2 align-items-center">
                        <input type="date" name="start_date" value="{{ $startDate }}"
                            class="form-control form-control-sm" style="width: 160px;">
                        <span class="text-muted small">to</span>
                        <input type="date" name="end_date" value="{{ $endDate }}"
                            class="form-control form-control-sm" style="width: 160px;">
                        <button type="submit" class="btn btn-primary btn-sm rounded">{{ __('app.search') }}</button>
                        <a href="{{ url()->current() }}" class="btn btn-outline-secondary btn-sm rounded">{{ __('app.reset') }}</a>
                    </form>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-4 ranking-card">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-trophy-fill text-warning me-2"></i>Overall Top Staff (All
                    Activity)</h5>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr class="text-secondary small">
                                <th style="width: 60px;">#</th>
                                <th>Staff</th>
                                <th class="text-end">Total Activity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($overall as $index => $row)
                                <tr>
                                    <td>
                                        <span class="rank-badge rank-{{ $index + 1 }}">{{ $index + 1 }}</span>
                                    </td>
                                    <td>{{ $row->user->name ?? 'Unknown' }}</td>
                                    <td class="text-end">{{ $row->total }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">No activity found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row g-4">
            @php
                $categoryMeta = [
                    'borrow' => ['label' => 'Top Staff — Borrow Approvals', 'icon' => 'bi-box-arrow-in-right', 'color' => 'text-primary'],
                    'return' => ['label' => 'Top Staff — Returns Processed', 'icon' => 'bi-box-arrow-left', 'color' => 'text-success'],
                    'call' => ['label' => 'Top Staff — Calls Handled', 'icon' => 'bi-telephone-fill', 'color' => 'text-info'],
                ];
            @endphp

            @foreach ($rankings as $key => $rows)
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 ranking-card">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3">
                                <i class="bi {{ $categoryMeta[$key]['icon'] }} {{ $categoryMeta[$key]['color'] }} me-2"></i>
                                {{ $categoryMeta[$key]['label'] }}
                            </h6>
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead>
                                        <tr class="text-secondary small">
                                            <th style="width: 50px;">#</th>
                                            <th>Staff</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($rows as $index => $row)
                                            <tr>
                                                <td>
                                                    <span class="rank-badge rank-{{ $index + 1 }}">{{ $index + 1 }}</span>
                                                </td>
                                                <td>{{ $row->user->name ?? 'Unknown' }}</td>
                                                <td class="text-end">{{ $row->total }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-4">No activity found
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
