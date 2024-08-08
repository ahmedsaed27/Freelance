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

class Cases extends Model implements HasMedia
{
    use HasFactory , InteractsWithMedia , SoftDeletes , LogsActivity;

    protected $table = 'cases';

    protected $fillable = [
        'user_id' ,
        'description',
        'is_visible',
        'currency_id',
        'country_id',
        'city_id',
        'title',
        'specialization',
        'proposed_budget',
        'min_amount',
        'max_amount',
        'type_id',
        'status',
        'number_of_days',
        'is_anonymous',
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
        return $this->belongsToMany(Profiles::class , 'case_profile' , 'case_id' , 'profile_id')
        ->withTimestamps()
        ->withPivot(['suggested_rate' , 'description' , 'status' , 'estimation_time' , 'currency_id']);

        // return $this->hasMany(CasesProfile::class , 'case_id');
    }


    public function countrie(){
        return $this->belongsTo(Country::class , 'country_id');
    }

    public function currency(){
        return $this->belongsTo(Currency::class , 'currency_id');
    }

    public function city(){
        return $this->belongsTo(Cities::class , 'city_id');
    }

    public function caseKeyword(){
        return $this->belongsToMany(keyword::class , 'case_keyword' , 'case_id' , 'keyword_id')
        ->withTimestamps()
        ->withTrashed()
        ->wherePivotNull('deleted_at');
    }

    public function caseSkill(){
        return $this->belongsToMany(Skill::class , 'case_skill' , 'case_id' , 'skill_id')
        ->withTimestamps()
        ->withTrashed()
        ->wherePivotNull('deleted_at');
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
            $column = $mediaItem->getCustomProperty('column');


            if ($mimeType === 'application/pdf') {
                $conversions[$column] = [
                    'original' => $mediaItem->getUrl(),
                    'type' => 'pdf',
                ];
            } else {
                $conversionNames = $mediaItem->getMediaConversionNames();

                foreach ($conversionNames as $conversionName) {
                    $conversionUrls[$conversionName] = $mediaItem->getUrl($conversionName);
                }

                $conversions[$column] = [
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
            ->setDescriptionForEvent(fn (string $eventName) => "This CasesInformation has been {$eventName}")
            ->useLogName('Cases');
    }
}
