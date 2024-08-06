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

class WorkedCaseNotes extends Model implements HasMedia
{
    use HasFactory , SoftDeletes , LogsActivity , InteractsWithMedia;

    protected $table = 'worked_case_notes';

    protected $fillable = [
        'worked_case_id' ,
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
            ->setDescriptionForEvent(fn (string $eventName) => "This WorkedCaseNotes has been {$eventName}")
            ->useLogName('WorkedCaseNotes');

        // Chain fluent methods for configuration options
    }

    public function workedCase(){
        return $this->belongsTo(WorkedCases::class , 'worked_case_id');
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
        $mediaItems = $this->getMedia('worked_case_notes');
        $conversions = [];

        if ($mediaItems->isEmpty()) {
            return [];
        }

        foreach ($mediaItems as $mediaItem) {
            $mimeType = $mediaItem->mime_type;
            $conversionUrls = [];
            $column = $mediaItem->getCustomProperty('column');

            if(in_array($mimeType , ['image/jpg' ,'image/jpeg' ,'image/png'])){
                $conversionNames = $mediaItem->getMediaConversionNames();

                foreach ($conversionNames as $conversionName) {
                    $conversionUrls[$conversionName] = $mediaItem->getUrl($conversionName);
                }
            }


            $conversions[$column][] = [
                'original' => $mediaItem->getUrl(),
                'type' => $mimeType,
                'conversions' => $conversionUrls ?? null,
            ];
        }

        return $conversions;
    }
}
