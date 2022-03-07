<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    public function category()
    {
        return $this->belongsTo(JobCategory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function openingHours()
    {
        return $this->hasMany(JobOpeningHour::class);
    }
}
