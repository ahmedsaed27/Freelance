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


class CaseProfileNotes extends Model implements HasMedia
{
    use HasFactory , SoftDeletes , LogsActivity , InteractsWithMedia;

    protected $table = 'case_profile_notes';

    protected $fillable = [
        'case_profile_id',
        'created_by_user_id',
        'content',
        'parent_id',
    ];

    protected $appends = ['conversion_urls'];

    protected $hidden = [
        'media'
    ];

    public $timestamps = true;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->setDescriptionForEvent(fn (string $eventName) => "This CaseProfileNotes has been {$eventName}")
            ->useLogName('CaseProfileNotes');

        // Chain fluent methods for configuration options
    }

    public function caseProfile(){
        return $this->belongsTo(CasesProfile::class , 'case_profile_id');
    }

    public function createdBy(){
        return $this->belongsTo(User::class , 'created_by_user_id');
    }

    public function parent(){
        return $this->belongsTo($this , 'currency_id');
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
        $mediaItems = $this->getMedia('profile_case_note');
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
