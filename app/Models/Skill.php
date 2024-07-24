<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;


class Skill extends Model
{
    use HasFactory , SoftDeletes , LogsActivity;

    protected $table = 'skills';

    protected $fillable = ['title' , 'description'];

    public $timestamps = true;


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->setDescriptionForEvent(fn (string $eventName) => "This SkillInformation has been {$eventName}")
            ->useLogName('Skill');
    }
}
