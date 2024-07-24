<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;


class keyword extends Model
{
    use HasFactory , SoftDeletes , LogsActivity;

    protected $table = 'keywords';

    protected $fillable = ['word'];

    public $timestamps = true;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->setDescriptionForEvent(fn (string $eventName) => "This keyword has been {$eventName}")
            ->useLogName('keyword');

        // Chain fluent methods for configuration options
    }
}
