<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalTutorial extends Model
{
    protected $table = 'portal_tutorials';

    protected $fillable = [
        'title','slug','category','excerpt','body',
        'cover_image_url','youtube_url','duration_label',
        'status','published_at','created_by','updated_by'
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];
}
