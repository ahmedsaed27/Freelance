<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileSocials extends Model
{
    use HasFactory;

    protected $table = 'profile_socials';

    protected $fillable = [
      'profiles_id',
      'instagram',
      'linkedin',
      'facebook',
    ];

    public $timestamps = true;

    public function profile(){
        return $this->belongsTo(Profiles::class , 'profiles_id');
    }
}
