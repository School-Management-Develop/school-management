<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $primaryKey = 'student_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'student_name',
        'student_name_normalized',
        'phone_number',
        'gender',
        'group_id',
        'status',
        'inactive_note',
    ];

    // ---------------------------------------------------------------------------
    // Mutators
    // ---------------------------------------------------------------------------

    /**
     * Whenever student_name is written, keep student_name_normalized in sync.
     * Normalization rule: lowercase + all spaces removed.
     * e.g. "Chan Dara" → "chandara"
     */
    public function setStudentNameAttribute(string $value): void
    {
        $this->attributes['student_name']            = $value;
        $this->attributes['student_name_normalized'] = strtolower(
        preg_replace('/[\s\x{200B}\x{200C}\x{FEFF}\x{00A0}]+/u', '', $value)
    );
    }

    // ---------------------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------------------

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id', 'group_id');
    }

    public function borrows()
    {
        return $this->hasMany(Borrow::class, 'student_id', 'student_id');
    }
}