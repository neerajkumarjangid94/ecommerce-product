<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;


class ProductOption extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'name',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function values()
    {
        return $this->hasMany(ProductOptionValue::class, 'option_id');
    }

    public function variantOptionValues()
    {
        return $this->hasMany(VariantOptionValue::class, 'option_id');
    }
}
