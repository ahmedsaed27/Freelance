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

class ProfileEducation extends Model implements HasMedia
{
    use HasFactory , InteractsWithMedia , SoftDeletes , LogsActivity;

    protected $table = 'profile_education';

    protected $fillable = [
      'profile_id',
      'major',
      'grade',
      'degree',
      'qualification',
      'university',
      'country_id',
      'additional_information',
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

            if ($mimeType === 'application/pdf') {
                $conversions[] = [
                    'original' => $mediaItem->getUrl(),
                    'type' => 'pdf',
                ];
            } else {
                $conversionNames = $mediaItem->getMediaConversionNames();

                foreach ($conversionNames as $conversionName) {
                    $conversionUrls[$conversionName] = $mediaItem->getUrl($conversionName);
                }

                $conversions[] = [
                    'original' => $mediaItem->getUrl(),
                    'type' => 'image',
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
            ->setDescriptionForEvent(fn (string $eventName) => "This ProfileEducationInformation has been {$eventName}")
            ->useLogName('ProfileEducation');

        // Chain fluent methods for configuration options
    }

}
