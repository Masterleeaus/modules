@forelse($locations as $locationData)
    @if(!is_null($locationData->job))
        <li class="border-bottom-grey cursor-pointer job-opening-card">
            <div class="card border-0 p-4">
                <div class="card-block" onclick="openJobDetail({{$locationData->job->id}},
                {{ $locationData->company_address_id }})">
                    <input type="hidden" name="job_id{{$locationData->job->id}}" id="job_id"
                        value="{{ $locationData->job->id }}">
                    <h5 class="card-title mb-0 heading-h5">{{ $locationData->job->title }}
                    </h5>
                    <div class="d-flex flex-wrap justify-content-between card-location mt-1">
                        <small class="text-dark-grey">{{ $locationData->job->jobType->job_type }}</small>
                        <span class="f-13 text-dark-grey">{{ isset($locationData->job->team->team_name) ? $locationData->job->team->team_name : null; }}<i class="ml-2 fa fa-graduation-cap"></i></span>
                    </div>

                    <div class="d-flex flex-wrap justify-content-between card-location mt-3">
                        <span class="fw-400 f-13"><i class="mr-2 fa fa-map-marker"></i>{{ ucwords($locationData->location->location) }}</span>
                        <div class="row">
                            <div class="dropdown d-sm-none">
                                <x-forms.button-secondary class="mr-3 dropdown-toggle" id="dropdownMenuButton"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">@lang('app.share')
                                </x-forms.button-secondary>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" target="_blank" href='https://wa.me/?text={{ route('job_apply',[$locationData->job->slug, $locationData->location->id, $locationData->job->company->hash]) }}'><i class="fab fa-whatsapp mr-2"></i> </a>

                                    <a class="dropdown-item btn-copy " data-clipboard-text="{{ route('job_apply',[$locationData->job->slug, $locationData->location->id, $locationData->job->company->hash]) }}"> <i class="fa fa-copy mr-2"></i> @lang('recruit::modules.front.copyLink')</a>
                                </div>
                            </div>
                            <a href="{{ route('job_apply',[$locationData->job->slug, $locationData->location->id, $locationData->job->company->hash]) }}" class="btn btn-primary f-14 d-block d-sm-none">
                                <i class="fa fa-briefcase mr-1"></i>@lang('recruit::modules.front.apply')
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </li>
    @endif
@empty
    <x-cards.no-record icon="list" :message="__('recruit::messages.noOpenings')"/>
@endforelse
