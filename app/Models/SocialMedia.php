<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SocialMedia extends Model implements HasMedia
{
    use HasFactory , SoftDeletes , LogsActivity , InteractsWithMedia;

    protected $table = 'social_media';

    protected $fillable = ['name'];

    protected $appends = ['conversion_urls'];

    protected $hidden = [
        'media'
    ];

    public $timestamps = true;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->setDescriptionForEvent(fn (string $eventName) => "This SocialMediaInformation has been {$eventName}")
            ->useLogName('SocialMedia');

        // Chain fluent methods for configuration options
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
        $mediaItems = $this->getMedia('profiles');
        $conversions = [];

        if ($mediaItems->isEmpty()) {
            return [];
        }

        foreach ($mediaItems as $mediaItem) {
            $conversionUrls = [];

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

        return $conversions;
    }

}
