<?php

namespace App\Models;

use App\Enums\Api\V1\Types;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;

class Profiles extends Model implements HasMedia
{
    use HasFactory , InteractsWithMedia;

    protected $connection = 'mysql';

    protected $table = 'profiles';

    protected $fillable = ['user_id' , 'type' , 'location' , 'areas_of_expertise' , 'hourly_rate' , 'years_of_experience'];

    public $timestamps = true;

    protected $casts = [
        'areas_of_expertise' => 'array',
    ];

    public function getTypeAttribute($value){
        return Types::from($value)->name;
    }


    public function user(){
        return $this->belongsTo(User::class , 'user_id');
    }
}
