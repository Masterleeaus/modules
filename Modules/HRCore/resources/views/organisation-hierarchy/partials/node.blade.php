<div class="org-node mb-3">
    <div class="row justify-content-center mb-2">
        <div class="avatar avatar-lg text-center">
            @if($node['profile_picture'])
                <img src="{{ $node['profile_picture'] }}" alt="Avatar" class="avatar rounded-circle" />
            @else
                <span class="avatar-initial rounded-circle bg-label-primary">{{ $node['initials'] }}</span>
            @endif
        </div>
    </div>
    <h6>{{ $node['name'] }}</h6>
    <p>{{ $node['designation'] }}</p>
</div>
@if (!empty($node['children']))
    <div class="org-children">
        @foreach ($node['children'] as $child)
            @include('hrcore::organisation-hierarchy.partials.node', ['node' => $child])
        @endforeach
    </div>
@endif
