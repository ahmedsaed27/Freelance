<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfilePaper extends Model
{
    use HasFactory;

    protected $table = 'profile_paper';

    protected $fillable = [
        'profiles_id',
        'papers_id',
        'value',
        'status'
    ];

    public $timestamps = true;

    public function profile(){
        return $this->belongsTo(Profiles::class , 'profiles_id');
    }

    public function papers(){
        return $this->belongsTo(Paper::class , 'papers_id');
    }
}
