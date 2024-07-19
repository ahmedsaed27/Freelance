<?php

namespace App\Models;

use App\Enums\Api\V1\Types;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Profiles extends Model implements HasMedia
{
    use HasFactory , InteractsWithMedia;

    protected $table = 'profiles';

    protected $fillable = [
        'user_id' ,
        'type' ,
        'location' ,
        'areas_of_expertise' ,
        'hourly_rate' ,
        'years_of_experience' ,
        'career',
        'countries_id',
        'cities_id',
        'field',
        'specialization',
        'experience'
    ];

    public $timestamps = true;

    protected $appends = ['conversion_urls'];

    protected $casts = [
        'areas_of_expertise' => 'array',
    ];

    protected $hidden = [
        'media'
    ];

    public function getTypeAttribute($value){
        return Types::from($value)->name;
    }


    public function user(){
        return $this->belongsTo(User::class , 'user_id');
    }

    public function socials(){
        return $this->hasOne(ProfileSocials::class , 'profiles_id');
    }


    public function workExperiences(){
        return $this->hasMany(ProfileWorkExperience::class , 'profiles_id');
    }


    public function education(){
        return $this->hasMany(ProfileEducation::class , 'profiles_id');
    }

    public function city(){
        return $this->belongsTo(Cities::class , 'cities_id');
    }

    public function country(){
        return $this->belongsTo(Country::class , 'countries_id');
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

}
