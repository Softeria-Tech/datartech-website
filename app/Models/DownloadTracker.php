<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DownloadTracker extends Model
{
    protected $fillable = ['user_id','date','downloads','resources'];

    protected $casts = [
        'date'      => 'date:Y-m-d',
        'downloads' => 'integer',
        'resources' => 'array'
    ];

    function user()
    {
        return $this->belongsTo(User::class);
    }
}
