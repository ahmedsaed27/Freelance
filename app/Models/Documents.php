<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;

class Documents extends Model implements HasMedia
{
    use HasFactory , InteractsWithMedia;

    protected $table = 'documents';

    protected $fillable = ['user_id' , 'expected_price'];

    protected $appends = ['conversion_urls'];

    protected $hidden = [
        'media'
    ];

    public $timestamps = true;

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function docs(){
        return $this->belongsToMany(Documents::class , 'docs_translators' , 'documents_id' , 'translators_id')
        ->withTimestamps();
    }

    public function getConversionUrlsAttribute()
    {
        $mediaItems = $this->getMedia('docs');
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
