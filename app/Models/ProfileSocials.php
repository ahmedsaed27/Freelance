<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ProfileSocials extends Model
{
    use HasFactory , SoftDeletes , LogsActivity;

    protected $table = 'profile_socials';

    protected $fillable = [
      'profile_id',
      'social_id',
      'link'
    ];

    public $timestamps = true;

    public function profile(){
        return $this->belongsTo(Profiles::class , 'profile_id');
    }

    public function social(){
        return $this->belongsTo(SocialMedia::class , 'social_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->setDescriptionForEvent(fn (string $eventName) => "This ProfileSocialsInformation has been {$eventName}")
            ->useLogName('ProfileSocials');

        // Chain fluent methods for configuration options
    }
}
