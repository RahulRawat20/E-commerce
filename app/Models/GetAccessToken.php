<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GetAccessToken extends Model
{
    //
    protected $fillable = [
        'accesstoken',
        'expires_in',  // If this field exists in your database
        // Add other fields here if needed
    ];
}
