<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;
    public function user (){
        return $this->belongsTo(User::class);
    }
    protected $fillable = [
        'nhk_title',
        'nhk_description',
        'nhk_genres',
        'start_time',
        'end_time',
        'nhk_tvEpisodeId',
        'nhk_code',
        'is_active',
        'notify_at',
        'notify_before_min',
        'user_id',
    ];
protected $casts = [
    'start_time' => 'datetime',
    'end_time' => 'datetime',
];
}
