@php
    $addPermission = user()->permission('add_job_application');
    $viewPermission = user()->permission('view_job_application');
    $deletePermission = user()->permission('delete_job_application');
@endphp

<div class="tab-pane fade show active" role="tabpanel" aria-labelledby="nav-email-tab">
<div class="table-responsive p-20 bg-white">
    
    <x-table class="table-bordered">
        <x-slot name="thead">
            <th>@lang('recruit::modules.jobApplication.question')</th>
            <th>@lang('recruit::modules.jobApplication.answers')</th>
        
        </x-slot>
        @forelse ($allAnswers as $answer)
            <tr class="row{{ $answer->id }}">
                <td>
                    {{ ucwords($answer->question->question) }}
                </td>
                <td class="col-md-6">
                    @if (is_null($answer->answer) && !is_null($answer->hashname))
                        <a class="taskView" href="{{ $answer->file_url }}" target="_blank">
                            @lang('app.view')
                        </a>
                    @else
                        {{ $answer->answer ?? '-' }}
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4">
                    <x-cards.no-record icon="user" :message="__('messages.noRecordFound')"/>
                </td>
            </tr>
        @endforelse
    </x-table>
</div>
</div>  
<!-- TAB CONTENT END -->



