<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ProjectStatusEnum;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'status' => ProjectStatusEnum::class,
    ];

    public function phases () 
    {
        return $this->hasMany(Phase::class);
    }

    public function projectmembers ()
    {
        return $this->hasMany(ProjectMember::class);
    }
}
