<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function connections(): BelongsToMany
    {
        return $this->belongsToMany(Connection::class, 'connection_tags')->whereNull('connection_tags.deleted_at');
    }

    public function connectionTags(): HasMany
    {
        return $this->hasMany(ConnectionTag::class);
    }

    public function deleteConnectionTags()
    {
        $this->connectionTags()->delete();
    }
}
