@foreach ($locations as $locationData)
    <option value="{{ $locationData->location->id }}">{{ $locationData->location->location }}</option>
@endforeach
