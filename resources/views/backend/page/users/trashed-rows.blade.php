@forelse ($trashedUsers as $u)
    <tr>
        <td class="text-secondary">{{ $loop->iteration }}</td>
        <td class="fw-semibold text-muted">{{ $u->name }}</td>
        <td class="text-muted">{{ $u->email }}</td>
        <td>{{ $u->role ?? '-' }}</td>
        <td><span class="badge rounded-pill bg-danger px-3 py-2">Deleted</span></td>
        <td class="text-muted small">{{ $u->deleted_at->format('d M Y H:i') }}</td>
        <td class="text-end">
            <form method="POST" action="{{ route('users.restore', $u->id) }}" class="d-inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-sm btn-success"
                    onclick="return confirm('Restore this user?')">
                    <i class="bi bi-arrow-clockwise me-1"></i>Restore
                </button>
            </form>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center text-muted py-3">No deleted users</td>
    </tr>
@endforelse