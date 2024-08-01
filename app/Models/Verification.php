<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;


class Verification extends Model
{
    use HasFactory ,  LogsActivity, SoftDeletes;

    protected $table = 'verifications';

    protected $fillable = ['profile_id' , 'verified_at' , 'is_paid' , 'start_date' , 'end_date'];

    public $timestamps = true;

    public function profile(){
        return $this->belongsTo(Profiles::class , 'profile_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->setDescriptionForEvent(fn (string $eventName) => "This VerificationInformation has been {$eventName}")
            ->useLogName('Verification');

        // Chain fluent methods for configuration options
    }

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($verification) {
            $verification->verified_at = Carbon::now();
        });
    }

}
