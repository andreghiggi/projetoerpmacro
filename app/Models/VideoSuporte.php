<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoSuporte extends Model
{
    use HasFactory;

    protected $fillable = [
        'url_video', 'url_servidor', 'pagina'
    ];
}
