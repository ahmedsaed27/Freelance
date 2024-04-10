<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;

class Verification extends Model implements HasMedia
{
    use HasFactory , InteractsWithMedia;

    protected $table = 'verifications';

    protected $fillable = ['user_id' , 'national_id' , 'type'];

    public $timestamps = true;

    public function user(){
        return $this->belongsTo(User::class , 'user_id');
    }
}
