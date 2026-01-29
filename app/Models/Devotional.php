<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Devotional extends Model
{
    protected $fillable = [
        'title',
        'content',
        'author',
        'subheading',
        'key_verse',
        'application_note',
        'prayer_note',
        'verses',
        'date',
        'status',
        'image',
        'published_at',
        'created_by',
        'published_by',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'published_at' => 'datetime',
        ];
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function publisher()
    {
        return $this->belongsTo(Admin::class, 'published_by');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function getStatusColorAttribute()
    {
        return [
            'draft' => 'secondary',
            'pending' => 'danger',
            'in_review' => 'warning',
            'published' => 'success',
        ][$this->status] ?? 'secondary';
    }
}
