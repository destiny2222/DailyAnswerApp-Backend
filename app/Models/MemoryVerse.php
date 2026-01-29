<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemoryVerse extends Model
{
    protected $fillable = [
        'verse_text',
        'date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function prayerNotes()
    {
        return $this->hasMany(PrayerNote::class);
    }
}
