@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', $title)
@section('titlebar_subtitle', $description)
@section('titlebar_actions')

@endsection

@section('content')
    <div class="py-10">
        <x-table>
            <x-slot:head>
                <tr>
                    <th>
                        {{ __('id') }}
                    </th>
                    <th>
                        {{ __('Name') }}
                    </th>
                    <th>
                        {{ __('Email') }}
                    </th>
                    <th>
                        {{ __('Phone') }}
                    </th>
                    <th>
                        {{ __('Channel') }}
                    </th>
                    <th>
                        {{ __('Created At') }}
                    </th>
                    <th class="text-end">
                        {{ __('Actions') }}
                    </th>
                </tr>
            </x-slot:head>

            <x-slot:body>
                @foreach ($items as $entry)
                    <tr id="template-{{ $entry->id }}">
                        <td>
                            {{ $entry->id }}
                        </td>
                        <td>
                            {{ __($entry->name) }}
                        </td>
                        <td>
                            {{ __($entry->email) }}
                        </td>
                        <td>
                            {{ __($entry->phone) }}
                        </td>
                        <td>
                            {{ __($entry->chatbot_channel) }}
                        </td>
                        <td>
                            <p class="m-0">
                                {{ date('j.n.Y', strtotime($entry->updated_at)) }}
                                <span class="block opacity-60">
                                    {{ date('H:i:s', strtotime($entry->updated_at)) }}
                                </span>
                            </p>
                        </td>
                        <td class="whitespace-nowrap text-end">
                            <x-button
                                class="size-9"
                                size="none"
                                variant="ghost-shadow"
                                hover-variant="primary"
                                href="{{ route('dashboard.chatbot.chatbot-customer.edit', $entry->id) }}"
                                title="{{ __('Edit') }}"
                            >
                                <x-tabler-pencil class="size-4" />
                            </x-button>
                            <form
                                method="POST"
                                action="{{ route('dashboard.chatbot.chatbot-customer.destroy', $entry->id) }}"
                                style="display: inline;"
                            >
                                @csrf
                                @method('DELETE')
                                <x-button
                                    class="size-9"
                                    size="none"
                                    variant="ghost-shadow"
                                    hover-variant="danger"
                                    type="submit"
                                    onclick="return confirm('{{ __('Are you sure? This is permanent.') }}')"
                                    title="{{ __('Delete') }}"
                                >
                                    <x-tabler-x class="size-4" />
                                </x-button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </x-slot:body>
        </x-table>
    </div>
	{{ $items->links() }}
@endsection

@push('script')
@endpush
