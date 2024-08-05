<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;


class Booking extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'bookings';

    protected $fillable = ['title', 'description', 'date', 'status', 'start_time', 'end_time', 'hours', 'is_paid', 'profile_id', 'user_id'];

    public $timestamps = true;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->setDescriptionForEvent(fn (string $eventName) => "This Booking has been {$eventName}")
            ->useLogName('Booking');

        // Chain fluent methods for configuration options
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function profile()
    {
        return $this->belongsTo(Profiles::class, 'profile_id');
    }
}