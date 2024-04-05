<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;

class Profiles extends Model implements HasMedia
{
    use HasFactory , InteractsWithMedia;

    protected $table = 'profiles';

    protected $fillable = ['user_id' , 'location' , 'areas_of_expertise' , 'hourly_rate' , 'years_of_experience'];

    public $timestamps = true;

    protected $casts = [
        'areas_of_expertise' => 'array'
    ];

    public function user(){
        return $this->belongsTo(User::class , 'user_id');
    }
}
