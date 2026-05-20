<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    protected $fillable = ['category_id', 'limit_amount', 'month', 'year'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
