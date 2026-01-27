<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'country',
        'city',
        'postal_code',
        'document_type',
        'document_number',
        'phone',
        'bio',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
