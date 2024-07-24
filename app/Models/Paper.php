<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paper extends Model
{
    use HasFactory;

    protected $table = 'papers';

    protected $fillable = [
        'name',
        'description',
        'data_type',
        'is_unique',
        'is_required'
    ];

    public $timestamps = true;

    public function ProfileDocuments()
    {
        return $this->hasMany(ProfilePaper::class);
    }
}
