<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminContact extends Model
{
    protected $fillable = [
        'name',
        'telegram_username',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}