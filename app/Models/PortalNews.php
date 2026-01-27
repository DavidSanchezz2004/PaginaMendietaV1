<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalNews extends Model
{
    protected $table = 'portal_news';

    protected $fillable = [
        'title','slug','category','excerpt','body','cover_image_url',
        'status','published_at','created_by','updated_by'
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];
}
