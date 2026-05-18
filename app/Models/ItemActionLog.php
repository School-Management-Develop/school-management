<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemActionLog extends Model
{
    protected $fillable = [
        'item_id',
        'item_name',
        'user_id',
        'action',
        'details',
        'action_at',
    ];

    protected $casts = [
        'action_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'Itemid');
    }
}