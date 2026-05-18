@forelse ($logs as $k => $log)
    <tr>
        <td class="text-secondary">{{ $logs->firstItem() + $k }}</td>
        <td class="fw-semibold">{{ $log->item_name ?? '—' }}</td>
        <td>
            @php
                $badge = match($log->action) {
                    'Created'  => 'bg-success',
                    'Updated'  => 'bg-primary',
                    'Deleted'  => 'bg-danger',
                    'Restored' => 'bg-warning text-dark',
                    default    => 'bg-secondary',
                };
            @endphp
            <span class="badge rounded-pill {{ $badge }} px-3 py-2">
                {{ $log->action }}
            </span>
        </td>
        <td class="text-muted small">{{ $log->details ?? '—' }}</td>
        <td>
            <div class="fw-semibold small">{{ $log->user->name ?? '—' }}</div>
        </td>
        <td class="text-center text-muted small">
            {{ $log->action_at?->timezone('Asia/Phnom_Penh')->format('d M Y H:i') }}
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center text-muted py-4">No logs found</td>
    </tr>
@endforelse