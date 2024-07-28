<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;


class WorkedCases extends Model
{
    use HasFactory , SoftDeletes , LogsActivity;

    protected $table = 'worked_cases';

    protected $fillable = [
        'profile_id',
        'case_id',
        'rate',
        'currency_id',
        'status',
        'start_time',
        'end_time',
        'is_paid',
        'status'
    ];

    public $timestamps = true;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->setDescriptionForEvent(fn (string $eventName) => "This WorkedCases has been {$eventName}")
            ->useLogName('WorkedCases');

        // Chain fluent methods for configuration options
    }

    public function profile(){
        return $this->belongsTo(Profiles::class , 'profile_id');
    }

    public function case(){
        return $this->belongsTo(Cases::class , 'case_id');
    }

    public function currency(){
        return $this->belongsTo(Currency::class , 'currency_id');
    }
}
