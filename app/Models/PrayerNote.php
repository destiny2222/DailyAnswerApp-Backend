<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrayerNote extends Model
{
    protected $fillable = [
        'user_id',
        'memory_verse_id',
        'title',
        'note',
        'is_answered',
    ];

    protected $casts = [
        'is_answered' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function memoryVerse()
    {
        return $this->belongsTo(MemoryVerse::class);
    }
}
