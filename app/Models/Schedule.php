<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'place',
        'type',
        'status',
        'classification',
        'started_at',
        'finished_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class, 'schedule_contacts')->whereNull('schedule_contacts.deleted_at');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'schedule_users')->whereNull('schedule_users.deleted_at');
    }
}
