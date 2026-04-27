<div class="row">
    <div class="col-sm-12">
        <x-cards.data :title="__('app.note').' '.__('app.details')" class=" mt-4">
            <x-cards.data-row :label="__('modules.client.noteTitle')"
                :value="$note->note_title" />

            <x-cards.data-row :label="__('modules.client.noteType')" :value="$note->note_type == 0 ? __('app.public') : __('app.private')" />

            @if($note->note_type == 1)
                <div class="col-12 px-0 pb-3 d-flex">
                    <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                        @lang('modules.tasks.assignTo')</p>
                    <p class="mb-0 text-dark-grey f-14">
                        @foreach ($employees as $item)
                            <div class="taskEmployeeImg rounded-circle mr-1">
                                <a href="{{ route('employees.show', $item->id) }}">
                                    <img data-toggle="tooltip" data-original-title="{{ mb_ucwords($item->name) }}"
                                        src="{{ $item->image_url }}">
                                </a>
                            </div>
                        @endforeach
                    </p>
                </div>
            @endif

            <x-cards.data-row :label="__('modules.client.noteDetail')" :value="$note->note_details ?? '--'" html="true" />

        </x-cards.data>
    </div>
</div>
