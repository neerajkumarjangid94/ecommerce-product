<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductOptionValue extends Model
{
   use SoftDeletes;

    protected $fillable = [
        'option_id',
        'value',
    ];

    public function option()
    {
        return $this->belongsTo(ProductOption::class, 'option_id');
    }

    public function variantOptionValues()
    {
        return $this->hasMany(
            VariantOptionValue::class,
            'option_value_id'
        );
    }
}
