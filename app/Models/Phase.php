<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\PhaseStatusEnum;

class Phase extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'project_id',
    ];

    protected $cases = [
        'status' => PhaseStatusEnum::class,
    ];

    public function project () 
    {
        return $this->belongsTo(Project::class);
    }

    public function tasks ()
    {
        return $this->hasMany(Task::class);
    }
}
