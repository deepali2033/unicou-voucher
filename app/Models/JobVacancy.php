<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobVacancy extends Model
{
    protected $fillable = ['title', 'description', 'category', 'country', 'status'];

    public function applications()
    {
        return $this->hasMany(JobApplication::class, 'vacancy_id');
    }
}
