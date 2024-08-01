<?php

namespace App\Models;

use App\Enums\Api\V1\Types;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Profiles extends Model implements HasMedia
{
    use HasFactory , InteractsWithMedia , SoftDeletes;

    protected $connection = 'mysql';
    protected $table = 'profiles';

    protected $fillable = [
        'user_id' ,
        'type_id' ,
        'address' ,
        'areas_of_expertise' ,
        'hourly_rate' ,
        'years_of_experience' ,
        'career',
        'country_id',
        'city_id',
        'field',
        'specialization',
        'level',
        'currency_id',
        'status'
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
        // return $this->hasOne(ProfileSocials::class , 'profiles_id');
        return $this->belongsToMany(SocialMedia::class , 'profile_socials' , 'profile_id' , 'social_id')
        ->withTimestamps()
        ->withPivot([
            'link'
        ]);
    }


    public function workExperiences(){
        return $this->hasMany(ProfileWorkExperience::class , 'profile_id');
    }


    public function education(){
        return $this->hasMany(ProfileEducation::class , 'profile_id');
    }

    public function city(){
        return $this->belongsTo(Cities::class , 'city_id');
    }

    public function country(){
        return $this->belongsTo(Country::class , 'country_id');
    }

    public function currency(){
        return $this->belongsTo(Currency::class , 'currency_id');
    }

    public function profileType()
    {
        return $this->belongsToMany(Type::class, 'profile_type', 'profile_id', 'type_id')
                    ->withTimestamps()
                    ->withTrashed()
                    ->wherePivotNull('deleted_at');
    }

    public function receive(){
        return $this->belongsToMany(Cases::class , 'case_profile' , 'profile_id' , 'case_id')
        ->withPivot('suggested_rate', 'description', 'estimation_time' , 'currency_id')
        ->withTimestamps();
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
                    'uuid' => $mediaItem->uuid,
                    'original' => $mediaItem->getUrl(),
                    'type' => 'pdf',
                ];
            } else {
                $conversionNames = $mediaItem->getMediaConversionNames();

                foreach ($conversionNames as $conversionName) {
                    $conversionUrls[$conversionName] = $mediaItem->getUrl($conversionName);
                }

                $conversions[] = [
                    'uuid' => $mediaItem->uuid,
                    'original' => $mediaItem->getUrl(),
                    'type' => 'image',
                    'conversions' => $conversionUrls,
                ];
            }
        }

        return $conversions;
    }

}
