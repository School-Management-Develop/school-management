@extends('backend.layout.master')

@section('title', 'Manage Item History')
@section('history_active', 'active')

@section('contents')
    <style>
        .table-responsive {
            overflow: visible !important;
        }
    </style>
    <div class="container-fluid py-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
                    <div>
                        <h2 class="fw-bold mb-1">{{ __('app.manage_item_history') }}</h2>
                    </div>

                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <form method="GET" action="{{ url()->current() }}" class="d-flex gap-2 align-items-center">
                            @foreach (request()->except(['student', 'page']) as $key => $val)
                                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                            @endforeach
                            <div class="input-group" style="width: 240px;">
                                <span class="input-group-text bg-white border-end-0" style="margin-right:-2%">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" name="student" value="{{ request('student') }}"
                                    class="form-control border-start-0"
                                    placeholder="{{ __('app.Search student name...') }}">
                            </div>
                            <button type="submit" class="btn btn-primary rounded">{{ __('app.search') }}</button>
                        </form>

                        <a href="{{ url()->current() }}" class="btn btn-danger rounded">{{ __('app.reset') }}</a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr class="text-secondary small">
                                {{-- User Filter --}}
                                <th>
                                    <div class="position-relative d-inline-block">
                                        <button type="button"
                                            class="btn btn-link text-dark text-decoration-none p-0 fw-semibold small"
                                            onclick="toggleDropdown('userDropdown', 'userSearch')">
                                            {{ __('app.users') }}
                                            @if (request('user_id'))
                                                <span
                                                    class="text-primary">({{ $users->firstWhere('id', request('user_id'))?->name }})</span>
                                            @endif
                                            <i class="bi bi-chevron-down ms-1" style="font-size: 10px;"></i>
                                        </button>
                                        <div class="position-absolute bg-white border rounded-3 shadow-lg d-none"
                                            id="userDropdown"
                                            style="z-index: 1050; width: 220px; left: 0; top: 100%; margin-top: 6px;">
                                            <div class="p-2 border-bottom">
                                                <input type="text" class="form-control form-control-sm rounded-3"
                                                    id="userSearch" placeholder="{{ __('app.Search...') }}"
                                                    autocomplete="off" onkeyup="filterList('userSearch', 'userList')">
                                            </div>
                                            <div style="max-height: 220px; overflow-y: auto;" id="userList" class="py-1">
                                                <a href="{{ url()->current() . '?' . http_build_query(array_diff_key(request()->query(), ['user_id' => '', 'page' => ''])) }}"
                                                    class="d-block text-decoration-none px-3 py-2 small {{ !request('user_id') ? 'fw-bold text-primary bg-light' : 'text-dark' }}"
                                                    data-name="all">{{ __('app.All Users') }}</a>
                                                @foreach ($users as $u)
                                                    <a href="{{ url()->current() . '?' . http_build_query(array_merge(request()->query(), ['user_id' => $u->id, 'page' => 1])) }}"
                                                        class="d-block text-decoration-none px-3 py-2 small {{ (string) request('user_id') === (string) $u->id ? 'fw-bold text-primary bg-light' : 'text-dark' }}"
                                                        data-name="{{ strtolower($u->name) }}">{{ $u->name }}</a>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </th>

                                {{-- Action Filter --}}
                                <th>
                                    <div class="position-relative d-inline-block">
                                        <button type="button"
                                            class="btn btn-link text-dark text-decoration-none p-0 fw-semibold small"
                                            onclick="toggleDropdown('actionDropdown', null)">
                                            {{ __('app.action') }}
                                            @if (request('action'))
                                                <span class="text-primary">({{ request('action') }})</span>
                                            @endif
                                            <i class="bi bi-chevron-down ms-1" style="font-size: 10px;"></i>
                                        </button>
                                        <div class="position-absolute bg-white border rounded-3 shadow-lg d-none"
                                            id="actionDropdown"
                                            style="z-index: 1050; width: 180px; left: 0; top: 100%; margin-top: 6px;">
                                            <div style="max-height: 220px; overflow-y: auto;" class="py-1">
                                                <a href="{{ url()->current() . '?' . http_build_query(array_diff_key(request()->query(), ['action' => '', 'page' => ''])) }}"
                                                    class="d-block text-decoration-none px-3 py-2 small {{ !request('action') ? 'fw-bold text-primary bg-light' : 'text-dark' }}">
                                                    {{ __('app.All Actions') }}
                                                </a>
                                                @foreach (['Borrowed', 'Returned', 'Undo return', 'Called'] as $act)
                                                    <a href="{{ url()->current() . '?' . http_build_query(array_merge(request()->query(), ['action' => $act, 'page' => 1])) }}"
                                                        class="d-block text-decoration-none px-3 py-2 small {{ request('action') === $act ? 'fw-bold text-primary bg-light' : 'text-dark' }}">
                                                        {{ $act }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </th>

                                <th>{{ __('app.students') }}</th>

                                {{-- Item Filter --}}
                                <th>
                                    <div class="position-relative d-inline-block">
                                        <button type="button"
                                            class="btn btn-link text-dark text-decoration-none p-0 fw-semibold small"
                                            onclick="toggleDropdown('itemDropdown', 'itemSearch')">
                                            {{ __('app.item') }}
                                            @if (request('item_id'))
                                                <span
                                                    class="text-primary">({{ $items->firstWhere('Itemid', request('item_id'))?->display_name }})</span>
                                            @endif
                                            <i class="bi bi-chevron-down ms-1" style="font-size: 10px;"></i>
                                        </button>
                                        <div class="position-absolute bg-white border rounded-3 shadow-lg d-none"
                                            id="itemDropdown"
                                            style="z-index: 1050; width: 240px; left: 0; top: 100%; margin-top: 6px;">
                                            <div class="p-2 border-bottom">
                                                <input type="text" class="form-control form-control-sm rounded-3"
                                                    id="itemSearch" placeholder="{{ __('app.Search...') }}"
                                                    autocomplete="off" onkeyup="filterList('itemSearch', 'itemList')">
                                            </div>
                                            <div style="max-height: 220px; overflow-y: auto;" id="itemList" class="py-1">
                                                <a href="{{ url()->current() . '?' . http_build_query(array_diff_key(request()->query(), ['item_id' => '', 'page' => ''])) }}"
                                                    class="d-block text-decoration-none px-3 py-2 small {{ !request('item_id') ? 'fw-bold text-primary bg-light' : 'text-dark' }}"
                                                    data-name="all">{{ __('app.All Items') }}</a>
                                                @foreach ($items as $it)
                                                    <a href="{{ url()->current() . '?' . http_build_query(array_merge(request()->query(), ['item_id' => $it->Itemid, 'page' => 1])) }}"
                                                        class="d-block text-decoration-none px-3 py-2 small {{ (string) request('item_id') === (string) $it->Itemid ? 'fw-bold text-primary bg-light' : 'text-dark' }}"
                                                        data-name="{{ strtolower($it->display_name) }}">{{ $it->display_name }}</a>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </th>

                                <th>{{ __('app.approved_by') }}</th>
                                <th>{{ __('app.returned_by') }}</th>
                                <th>{{ __('app.details') }}</th>
                                <th>{{ __('app.time') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($histories as $history)
                                <tr>
                                    <td class="fw-semibold">{{ $history->user->name ?? '-' }}</td>

                                    <td>
                                        @if ($history->action == 'Borrowed')
                                            <span class="badge bg-primary">Borrowed</span>
                                        @elseif($history->action == 'Returned')
                                            <span class="badge bg-success">Returned</span>
                                        @elseif($history->action == 'Called')
                                            <span class="badge bg-info">Called</span>
                                        @elseif($history->action == 'Undo return')
                                            <span class="badge bg-warning text-dark">Undo Return</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $history->action }}</span>
                                        @endif
                                    </td>

                                    <td>{{ $history->borrow->student->student_name ?? '-' }}</td>
                                    <td>{{ $history->borrow->item->display_name ?? '-' }}</td>
                                    <td>{{ $history->approvedByUser->name ?? '-' }}</td>
                                    <td>{{ $history->returnedByUser->name ?? '-' }}</td>
                                    <td class="text-muted">{{ $history->details }}</td>
                                    <td>{{ optional($history->action_at)->format('d M Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        No history found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $histories->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleDropdown(dropdownId, searchInputId) {
            document.querySelectorAll('.filter-dropdown').forEach(function(d) {
                if (d.id !== dropdownId) d.classList.add('d-none');
            });

            var dropdown = document.getElementById(dropdownId);
            dropdown.classList.toggle('d-none');


            var tableWrapper = document.querySelector('.table-responsive');
            if (tableWrapper) {
                var anyOpen = document.querySelector('.position-absolute.bg-white.border.rounded-3.shadow-lg:not(.d-none)');
                tableWrapper.style.overflow = anyOpen ? 'visible' : '';
            }

            if (!dropdown.classList.contains('d-none') && searchInputId) {
                var input = document.getElementById(searchInputId);
                input.value = '';
                input.focus();
            }
        }

        function filterList(inputId, containerId) {
            var search = document.getElementById(inputId).value.toLowerCase();
            var items = document.querySelectorAll('#' + containerId + ' a');
            items.forEach(function(item) {
                var name = item.getAttribute('data-name') || item.textContent.toLowerCase();
                item.style.display = name.includes(search) ? '' : 'none';
            });
        }

        document.addEventListener('click', function(e) {
            var dropdowns = document.querySelectorAll('.position-absolute.bg-white.border.rounded-3.shadow-lg');
            var closed = false;
            dropdowns.forEach(function(dropdown) {
                var btn = dropdown.parentElement.querySelector('button');
                if (!dropdown.contains(e.target) && !btn.contains(e.target)) {
                    dropdown.classList.add('d-none');
                    closed = true;
                }
            });
            if (closed) {
                var tableWrapper = document.querySelector('.table-responsive');
                if (tableWrapper) tableWrapper.style.overflow = '';
            }
        });
    </script>
@endsection
