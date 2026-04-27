<div class="col-12 border-bottom-grey">
    <h3 class="heading-h1 mb-3">@lang('asset::app.history')</h3>
</div>
@forelse($asset->history as $history)
    <div class="col-11 border-bottom-grey py-3">
        <div class="col-12 px-0 pb-3 d-flex">
            <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                @lang('asset::app.lentTo')</p>
            <p class="mb-0 text-dark-grey f-14">
                <x-employee :user="$history->user"/>
            </p>
        </div>

        <x-cards.data-row :label="__('asset::app.dateGiven')"
                          :value="$history->date_given->setTimezone($global->timezone)->format('d F Y H:i A') .' ('. $history->date_given->setTimezone($global->timezone)->diffForHumans(now()->setTimezone($global->timezone)) .')'"
                          html="true"/>

        <x-cards.data-row :label="__('asset::app.returnDate')"
                          :value="!is_null($history->return_date) ? $history->return_date->setTimezone($global->timezone)->format('d F Y H:i A'). ' ('.$history->return_date->setTimezone($global->timezone)->diffForHumans(now()->setTimezone($global->timezone)) .')' : '--'"
                          html="true"/>

        <x-cards.data-row :label="__('asset::app.dateOfReturn')"
                          :value="!is_null($history->date_of_return) ? $history->date_of_return->setTimezone($global->timezone)->format('d F Y H:i A'). ' ('.$history->date_of_return->setTimezone($global->timezone)->diffForHumans(now()->setTimezone($global->timezone)) .')' : '--'"
                          html="true"/>

        <div class="col-12 px-0 pb-3 d-flex">
            <p class="mb-0 text-lightest f-14 w-30 d-inline-block ">
                @lang('asset::app.returnedBy')</p>
            <p class="mb-0 text-dark-grey f-14">
                @if ($history->returner)
                    <x-employee :user="$history->returner"/>
                @else
                    --
                @endif
            </p>
        </div>
        <x-cards.data-row :label="__('asset::app.notes')" :value="is_null($history->notes) ? '--' : $history->notes"
                          html="true"/>
    </div>
    @if (user()->permission('delete_assets_history') == 'all' || user()->permission('delete_assets_history') == 'added' || user()->permission('edit_assets_history') == 'all' || user()->permission('edit_assets_history') == 'added')
    <div class="col-md-1 border-bottom-grey py-3 text-right">
        <div class="dropdown ml-auto comment-action">
            <button class="btn btn-lg f-14 py-1 text-lightest  rounded dropdown-toggle" type="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-ellipsis-h"></i>
            </button>

            <div class="dropdown-menu dropdown-menu-right border-grey rounded b-shadow-4 p-0"
                 aria-labelledby="dropdownMenuLink" tabindex="0">
                @if (user()->permission('edit_assets_history') == 'all' || user()->permission('edit_assets_history') == 'added')
                    <a class="dropdown-item edit-history" href="javascript:;" data-history-id="{{ $history->id }}"
                       data-asset-id="{{ $asset->id }}">@lang('app.edit')</a>
                @endif

                @if (user()->permission('delete_assets_history') == 'all' || user()->permission('delete_assets_history') == 'added')
                    <a class="dropdown-item delete-history" data-history-id="{{ $history->id }}"
                       data-asset-id="{{ $asset->id }}" href="javascript:;">@lang('app.delete')</a>
                @endif
            </div>
        </div>
    </div>
    @endif

@empty
    <div class="align-items-center d-flex flex-column text-lightest p-20 w-100">
        <i class="fa fa-comment-alt f-21 w-100"></i>

        <div class="f-15 mt-4">
            - @lang('asset::app.noLendingHistoryFound') -
        </div>
    </div>
@endforelse
