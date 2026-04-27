<?php

namespace Modules\Asset\Entities;

use App\Models\BaseModel;
use App\Models\User;
use App\Scopes\ActiveScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Asset\Database\factories\AssetHistoryFactory;

class AssetHistory extends BaseModel
{
    use HasFactory;

    //region Properties

    protected $table = 'asset_lending_history';

    protected $default = [
        'id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'asset_id',
        'user_id',
        'lender_id',
        'returner_id',
    ];

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
        'asset_id',
        'user_id',
        'lender_id',
        'returner_id',
    ];

    protected $filterable = [
        'id',
        'asset_id',
        'user_id',
        'date_given',
        'return_date',
        'date_of_return',
    ];

    protected $appends = [

    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'date_given',
        'return_date',
        'date_of_return',
    ];

    protected $with = ['user', 'lender', 'returner'];

    //endregion

    //region Relations

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withoutGlobalScope(ActiveScope::class);
    }

    public function lender(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function returner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    //endregion

    //region Custom Functions

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return AssetHistoryFactory::new();
    }
}
