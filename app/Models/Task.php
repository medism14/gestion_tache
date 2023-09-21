<?php

namespace App\Models;

use App\Models\Project;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\TaskStatusEnum;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'status',
        'confirmed_date',
        'comment',
        'user_id',
        'phase_id',
    ];

    protected $casts = [
        'status' => TaskStatusEnum::class,
    ];  

    public function user () {
        return $this->belongsTo(User::class);
    }

    public function phase () 
    {
        return $this->belongsTo(Phase::class);
    }

}
