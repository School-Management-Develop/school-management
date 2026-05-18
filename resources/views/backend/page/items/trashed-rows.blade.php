@forelse ($trashedItems as $item)
    <tr>
        <td class="text-secondary">{{ $loop->iteration }}</td>
        <td class="fw-semibold text-muted">{{ $item->name }}</td>
        <td class="text-muted">{{ $item->name_kh ?? '—' }}</td>
        <td class="text-center">{{ $item->qty ?? 0 }}</td>
        <td><span class="badge rounded-pill bg-danger px-3 py-2">Deleted</span></td>
        <td class="text-muted small">{{ $item->deleted_at->format('d M Y H:i') }}</td>
        <td class="text-end">
            <form method="POST" action="{{ route('items.restore', $item->Itemid) }}" class="d-inline restore-form">
                @csrf
                @method('PATCH')
                <button type="button" class="btn btn-sm btn-success btn-restore">
                    <i class="bi bi-arrow-clockwise me-1"></i>Restore
                </button>
            </form>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center text-muted py-3">No deleted items</td>
    </tr>
@endforelse