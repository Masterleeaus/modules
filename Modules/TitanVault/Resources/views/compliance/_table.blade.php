<table class="table table-sm table-hover mb-0">
    <thead class="thead-light">
        <tr>
            <th>Type</th>
            <th>Document</th>
            <th>Staff</th>
            <th>Chemical</th>
            <th>Expiry Date</th>
            <th>Added</th>
        </tr>
    </thead>
    <tbody>
        @forelse($items as $item)
        <tr class="{{ $rowClass ?? '' }}">
            <td>
                <span class="badge badge-{{ ['insurance'=>'info','police_check'=>'secondary','wwcc'=>'primary','sds'=>'warning','other'=>'light'][$item->compliance_type] ?? 'secondary' }}">
                    {{ ucfirst(str_replace('_', ' ', $item->compliance_type)) }}
                </span>
            </td>
            <td>
                @if($item->document)
                    <a href="{{ route('titan-vault.documents.show', $item->document_id) }}">{{ $item->document->title }}</a>
                @else
                    <span class="text-muted">—</span>
                @endif
            </td>
            <td>{{ $item->staff?->name ?? '—' }}</td>
            <td>{{ $item->chemical_name ?? '—' }}</td>
            <td>
                @if($item->expiry_date)
                    {{ $item->expiry_date->format('d M Y') }}
                    @if($item->expiry_date->isPast())
                        <span class="badge badge-danger ml-1">Expired</span>
                    @elseif($item->expiry_date->diffInDays(now()) <= 30)
                        <span class="badge badge-warning ml-1">Soon</span>
                    @else
                        <span class="badge badge-success ml-1">OK</span>
                    @endif
                @else
                    <span class="text-muted">No expiry</span>
                @endif
            </td>
            <td>{{ $item->created_at->format('d M Y') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center text-muted py-3">No records.</td>
        </tr>
        @endforelse
    </tbody>
</table>
