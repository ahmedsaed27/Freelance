<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProfileWorkExperience extends Model implements HasMedia
{
    use HasFactory , InteractsWithMedia , SoftDeletes , LogsActivity;

    protected $table = 'profile_work_experiences';

    protected $fillable = [
      'profile_id',
      'company',
      'job_title',
      'country_id',
      'job_type',
      'work_place',
      'responsibilities',
      'career_level',
      'start_date',
      'end_date',
    ];

    public $timestamps = true;

    protected $appends = ['conversion_urls'];

    protected $hidden = [
        'media'
    ];

    public function profile(){
        return $this->belongsTo(Profiles::class , 'profile_id');
    }

    public function country(){
        return $this->belongsTo(Country::class , 'country_id');
    }

    public function registerMediaConversions(Media $media = null): void
    {
            $this
            ->addMediaConversion('thumb-320')
                ->width(320)
                ->height(200);

                $this
                ->addMediaConversion('thumb-100')
                    ->width(100)
                    ->height(100);
    }

    public function getConversionUrlsAttribute()
    {
        $mediaItems = $this->getMedia('certificates');
        $conversions = [];

        if ($mediaItems->isEmpty()) {
            return [];
        }

        foreach ($mediaItems as $mediaItem) {
            $mimeType = $mediaItem->mime_type;
            $conversionUrls = [];
            $column = $mediaItem->getCustomProperty('column');


            if ($mimeType === 'application/pdf') {
                $conversions[$column] = [
                    'original' => $mediaItem->getUrl(),
                    'type' => $mimeType,
                ];
            } else {
                $conversionNames = $mediaItem->getMediaConversionNames();

                foreach ($conversionNames as $conversionName) {
                    $conversionUrls[$conversionName] = $mediaItem->getUrl($conversionName);
                }

                $conversions[$column] = [
                    'original' => $mediaItem->getUrl(),
                    'type' => $mimeType,
                    'conversions' => $conversionUrls,
                ];
            }
        }

        return $conversions;
    }


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->setDescriptionForEvent(fn (string $eventName) => "This ProfileWorkExperienceInformation has been {$eventName}")
            ->useLogName('ProfileWorkExperience');

        // Chain fluent methods for configuration options
    }

}
