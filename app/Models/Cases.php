<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Cases extends Model implements HasMedia
{
    use HasFactory , InteractsWithMedia;

    protected $table = 'cases';

    protected $connection = 'mysql';


    protected $fillable = [
        'user_id' ,
        'notes'  ,
        'is_visible',
        'freelance_type',
        'countries_id',
        'cities_id',
        'title',
        'specialization',
        'proposed_budget',
        'currency',
        'keywords',
        'required_skills'
    ];

    protected $appends = ['conversion_urls'];

    protected $hidden = [
        'media'
    ];

    protected $casts = [
        'keywords' => 'array'
    ];

    public $timestamps = true;


    public function user(){
        return $this->belongsTo(User::class , 'user_id');
    }

    public function receive(){
        return $this->belongsToMany(User::class , 'cases_users' , 'cases_id' , 'user_id')
        ->withTimestamps();
    }


    public function countrie(){
        return $this->belongsTo(Country::class , 'countries_id');
    }
    public function city(){
        return $this->belongsTo(Cities::class , 'cities_id');
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
        $mediaItems = $this->getMedia('case');
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
