<x-table class="table-hover bg-white rounded">
    <x-slot name="thead">
        <th>@lang('modules.projects.fileName')</th>
        <th>@lang('app.date')</th>
        <th class="text-right">@lang('app.action')</th>
    </x-slot>
    @forelse($product->files as $file)
        <tr>
            @if($product->default_image == $file->hashname)
                <td>
                    <img src="{{ $file->file_url }}" class="border rounded height-50"/><br>
                    <span class="badge badge-success" style="background-color:gray">@lang('purchase::app.defaultImage')</span>
                </td>
            @else
            <td>
                <img src="{{ $file->file_url }}" class="border rounded height-50"/>
            </td>
            @endif

            @if($file->created_at == null )
                <td>{{ $product->created_at->diffForHumans() }}</td>
            @else
                <td>{{ carbon\carbon::parse($file->created_at)->diffForHumans() }}</td>
            @endif

            <td class="text-right pr-20">
                @if ($viewPermission == 'all' || ($viewPermission == 'added' && $file->added_by == user()->id))
                    <div class="task_view">
                        <a class="taskView" href="{{ $file->file_url }}" target="_blank">
                            @lang('app.view')
                        </a>
                        <div class="dropdown">
                            <a href="{{ route('product-files.download', $file->id) }}"
                                class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle"
                                type="link" id="dropdownMenuLink-3" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="icon-options-vertical icons"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ route('product-files.download', $file->id) }}">
                                    <i class="fa fa-download mr-2"></i>
                                    @lang('app.download')
                                </a>
                                @if ($deletePermission == 'all' || ($deletePermission == 'added' && $file->added_by == user()->id))
                                 @if($product->default_image != $file->hashname)
                                    <a class="dropdown-item delete-product-file" href="javascript:;"
                                        data-file-id="{{ $file->id }}">
                                        <i class="fa fa-trash mr-2"></i>
                                        @lang('app.delete')
                                    </a>
                                @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="3" class="shadow-none">
                <x-cards.no-record icon="file-excel" :message="__('messages.noFileUploaded')" />
            </td>
        </tr>
    @endforelse
</x-table>
