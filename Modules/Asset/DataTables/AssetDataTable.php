<?php

namespace Modules\Asset\DataTables;

use Carbon\Carbon;
use App\DataTables\BaseDataTable;
use Modules\Asset\Entities\Asset;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class AssetDataTable extends BaseDataTable
{

    public function __construct()
    {
        parent::__construct();
        $this->editAssetPermission = user()->permission('edit_asset');
        $this->deleteAssetPermission = user()->permission('delete_asset');
        $this->viewAssetPermission = user()->permission('view_asset');
    }

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('action', function ($row) {
                // @codingStandardsIgnoreStart
                $actions = '<div class="task_view">
                    <div class="dropdown">
                        <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link" id="dropdownMenuLink-41" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="icon-options-vertical icons"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-41" tabindex="0" x-placement="bottom-end">';

                $actions .= '<a href="' . route('assets.show', [$row->id]) . '" class="dropdown-item openRightModal"><i class="fa fa-eye mr-2"></i>' . __('app.view') . '</a>';

                if ($this->editAssetPermission == 'all'
                    || (($this->editAssetPermission == 'added' || $this->editAssetPermission == 'both') && user()->id == $row->added_by)
                ) {
                    $actions .= '<a class="dropdown-item openRightModal" href="' . route('assets.edit', [$row->id]) . '">
                                    <i class="fa fa-edit mr-2"></i>
                                    ' . __('app.edit') . '
                            </a>';

                    if ($row->status == 'available') {
                        $actions .= '<a class="dropdown-item lend" href="javascript:;" data-asset-id="' . $row->id . '">
                                    <i class="fa fa-share mr-2"></i>
                                    ' . __('asset::app.lend') . '
                            </a>';
                    }

                    if ($row->status == 'lent' && isset($row->latestHistory)) {
                        $actions .= '<a class="dropdown-item returnAsset" href="javascript:;" data-history-id="' . $row->latestHistory->id . '" data-asset-id="' . $row->id . '">
                                    <i class="fa fa-undo mr-2"></i>
                                    ' . __('asset::app.return') . '
                            </a>';
                    }

                }

                if ($this->deleteAssetPermission == 'all' || ($this->deleteAssetPermission == 'added' && user()->id == $row->added_by)) {
                    $actions .= '<a class="dropdown-item delete-table-row" href="javascript:;" data-asset-id="' . $row->id . '">
                                <i class="fa fa-trash mr-2"></i>
                                ' . trans('app.delete') . '
                            </a>';
                }
                // @codingStandardsIgnoreEnd
                $actions .= '</div> </div> </div>';

                return $actions;
            })
            ->editColumn('image', function ($row) {

                return ($row->image) ? '<img src="' . $row->image_url . '" class="border rounded height-50" />' : '--';
            })
            // @codingStandardsIgnoreStart
            ->editColumn('name', function ($row) {
                return '<div class="media align-items-center">
                        <div class="media-body">
                    <h5 class="mb-0 f-13 text-darkest-grey"><a href="' . route('assets.show', [$row->id]) . '" class="taskView openRightModal">' . $row->name . '</a></h5>
                    </div>
                </div>';
            })
            ->addColumn('asset_name', fn($row) => $row->name)
            ->editColumn('status', function ($row) {
                $class = Asset::STATUSES;

                return '<i class="fa fa-circle mr-1 ' . $class[$row->status] . ' f-10"></i>' . __('asset::app.' . $row->status);
            })
            ->addColumn('date', function ($row) {

                if ($row->status == 'lent' && isset($row->latestHistory)) {
                    $dateGiven = $row->latestHistory->date_given->translatedFormat($this->company->date_format);
                    $returnDate = $row->latestHistory->return_date ? $row->latestHistory->return_date->translatedFormat($this->company->date_format) : __('asset::app.noReturnDate');

                    return '<p class="my-0">Given Date:<span> ' . $dateGiven . '</span></p>' .
                        '<p class="my-0">Estimated Return:<span> ' . $returnDate . '</span></p>';
                }
            })
            ->editColumn('history', function ($row) {
                if ($row->status == 'lent' && isset($row->latestHistory)) {
                    return view('components.employee', [
                        'user' => $row->latestHistory->user,
                    ]);
                }

                return '-';
            })
            ->addColumn('lent_to_employee', function ($row) {
                if ($row->status == 'lent' && isset($row->latestHistory)) {
                    return $row->latestHistory->user->name;
                }

                return '-';
            })
            ->addIndexColumn()
            ->setRowId(fn($row) => 'row-' . $row->id)
            ->setRowAttr([
                'style' => function ($row) {
                    if ($row->status == 'lent' && isset($row->latestHistory)) {
                        $date = $row->latestHistory->return_date ?: null;

                        if ($date?->isPast()) {
                            return 'background-color: rgb(255 180 180 / 32%);';
                        }
                    }
                },
            ])
            ->rawColumns(['action', 'status', 'history', 'name', 'image', 'date']);
    }

    /**
     * Get query source of dataTable.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Asset $model)
    {
        $request = $this->request();

        $assets = $model->with(
            [
                'assetType',
                'history',
                'latestHistory' => function ($query) {
                    return $query->orderByDesc('id');
                },
                'history.user:id,name,image'
            ])
            ->select('assets.id', 'assets.name', 'asset_type_id', 'description', 'serial_number', 'assets.status', 'assets.image');

        if ($request->asset_type != 'all' && $request->asset_type != '') {
            $assets = $assets->where('asset_type_id', $request->asset_type);
        }

        if ($this->viewAssetPermission == 'owned') {
            $assets = $assets->join('asset_lending_history', 'asset_lending_history.asset_id', '=', 'assets.id')
                ->where('assets.status', 'lent')
                ->where('asset_lending_history.user_id', user()->id)
                ->whereNull('asset_lending_history.date_of_return');

        }
        elseif ($this->viewAssetPermission == 'added') {
            $assets = $assets->where('assets.added_by', user()->id);

        }
        elseif ($this->viewAssetPermission == 'both') {
            $assets = $assets->leftJoin('asset_lending_history', 'asset_lending_history.asset_id', '=', 'assets.id');
            $assets = $assets->where(function ($query) {
                $query->where('assets.added_by', user()->id)
                    ->orWhere(function ($q) {
                        $q->where('assets.status', 'lent')
                            ->where('asset_lending_history.user_id', user()->id)
                            ->whereNull('asset_lending_history.date_of_return');
                    });
            });

        }
        elseif ($request->user_id != 'all' && $request->user_id != '') {
            $assets = $assets->join('asset_lending_history', 'asset_lending_history.asset_id', '=', 'assets.id')
                ->where('assets.status', 'lent')
                ->where('asset_lending_history.user_id', $request->user_id)
                ->whereNull('asset_lending_history.date_of_return');
        }

        if ($request->status != 'all' && $request->status != '') {
            $assets = $assets->where('assets.status', $request->status);
        }

        if ($request->searchText != '') {
            $assets = $assets->where(function ($query) {
                $query->where('name', 'like', '%' . request('searchText') . '%');
            });
        }

        return $assets;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return parent::setBuilder('assets-table')
            ->parameters([
                'initComplete' => 'function () {
                    window.LaravelDataTables["assets-table"].buttons().container()
                     .appendTo( "#table-actions")
                 }',
                'fnDrawCallback' => 'function( oSettings ) {
                   //
                   $(".select-picker").selectpicker();
                 }',
            ])
            // phpcs:ignore
            ->buttons(Button::make(['extend' => 'excel', 'text' => '<i class="fa fa-file-export"></i> ' . trans('app.exportExcel')]));
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'title' => __('#'), 'visible' => !showId()],
            __('app.id') => ['data' => 'id', 'name' => 'id', 'title' => __('app.id'), 'visible' => showId()],
            __('asset::app.assetPicture') => ['data' => 'image', 'name' => 'image', 'exportable' => false, 'title' => __('asset::app.assetPicture')],
            __('asset::app.assetName') => ['data' => 'name', 'name' => 'assets.name', 'exportable' => false, 'title' => __('asset::app.assetName')],
            __('app.name') => ['data' => 'asset_name', 'name' => 'asset_name', 'visible' => false, 'title' => __('app.name')],
            __('asset::app.lentTo') => ['data' => 'history', 'name' => 'history.user.name', 'exportable' => false, 'title' => __('asset::app.lentTo')],
            __('asset::app.lentToEmployee') => ['data' => 'lent_to_employee', 'name' => 'lent_to_employee', 'visible' => false, 'title' => __('asset::app.lentToEmployee')],
            __('asset::app.status') => ['data' => 'status', 'name' => 'status', 'title' => __('asset::app.status')],
            __('asset::app.date') => ['data' => 'date', 'name' => 'history.date_given', 'title' => __('asset::app.date')],
            Column::computed('action', __('app.action'))
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->width(250)
                ->addClass('text-right pr-20'),
        ];
    }

}
