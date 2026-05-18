<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Item extends Model
{
    use SoftDeletes;
    protected $primaryKey = 'Itemid';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'name_kh',
        'available',
        'image',
        'qty',
        'borrow',
        'status',
        'description',
    ];

    protected $appends = ['display_name'];

    public function borrows()
    {
        return $this->hasMany(Borrow::class, 'item_id', 'Itemid');
    }

    public function getAvailableAttribute(): int
    {
        $borrowed = $this->borrows()
            ->whereIn('status', ['BORROWED', 'OVERDUE'])
            ->sum('qty');
        return max(0, ($this->qty ?? 0) - $borrowed);
    }

    public function getBorrowAttribute(): int
    {
        return $this->borrows()
            ->whereIn('status', ['BORROWED', 'OVERDUE'])
            ->sum('qty');
    }

    // public function getDisplayNameAttribute(): string
    // {
    //     $locale = app()->getLocale();

    //     if ($locale === 'kh' && !empty($this->name_kh)) {
    //         return $this->name_kh;
    //     }

    //     return $this->name ?? $this->name_kh ?? '';
    // }

    public function getDisplayNameAttribute(): string
{
    if (!empty($this->name_kh)) {
        return $this->name . ' (' . $this->name_kh . ')';
    }

    return $this->name ?? '';
}
}