<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VariantOptionValue extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_variant_id',
        'option_id',
        'option_value_id',
    ];

    public function variant()
    {
        return $this->belongsTo(
            ProductVariant::class,
            'product_variant_id'
        );
    }

    public function option()
    {
        return $this->belongsTo(ProductOption::class, 'option_id');
    }

    public function optionValue()
    {
        return $this->belongsTo(
            ProductOptionValue::class,
            'option_value_id'
        );
    }
}
