<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'it',
        'full_name',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'grades')->withPivot('grade')->withTimestamps();
    }

    public function getRouteKeyName(): string
    {
        return 'it';
    }

}
