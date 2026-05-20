<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavingGoal extends Model
{
    protected $fillable = ['name', 'target_amount', 'current_amount', 'target_date', 'color'];

    protected function casts(): array
    {
        return [
            'target_date' => 'date',
            'target_amount' => 'decimal:2',
            'current_amount' => 'decimal:2',
        ];
    }
}
