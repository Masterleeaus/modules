<?php

namespace Modules\SupplyChain\Entities;

use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes, HasCompany;

    protected $table = 'inventory_items';

    protected $fillable = [
        'company_id',
        'field_item_id',
        'name',
        'sku',
        'unit',
    ];

    /**
     * Relation to FieldItems item (item catalogue source-of-truth).
     * Returns the Eloquent relation when FieldItems is present,
     * otherwise returns a BelongsTo pointing to inventory_items itself
     * (which will always return null gracefully).
     */
    public function fieldItem(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        if (class_exists(\Modules\FieldItems\Entities\Item::class)) {
            return $this->belongsTo(\Modules\FieldItems\Entities\Item::class, 'field_item_id');
        }
        // Fallback: self-referential with a column that will never match — always null
        return $this->belongsTo(static::class, 'field_item_id')->whereRaw('1 = 0');
    }

    public function stockLevels()
    {
        return $this->hasMany(StockLevel::class, 'item_id');
    }

    public function movements()
    {
        return $this->hasMany(Movement::class, 'item_id');
    }
}
