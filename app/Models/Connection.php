<?php

namespace App\Models;

use App\Constants\ConnectionConstant\ConnectionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Connection extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 
        'note',
        'type',
        'status',
        'user_id',
        'enterprise_id'

    ];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'connection_users', 'connection_id', 'user_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'connection_tags')->whereNull('connection_tags.deleted_at');
    }

    public function histories() : HasMany
    {
        return $this->hasMany(ConnectionHistory::class);
    }

    public function scopeTagFilter($query, $values)
    {
        if (count($values) == 0) {
            return $query;
        }

        $query->whereHas('tags', function ($query) use ($values) {
            $query->whereIn('tag_id', $values);
        });
    }

    public function scopeStatusFilter($query, $values)
    {
        if (count($values) == 0) {
            return $query;
        }

        $query->whereIn('status', $values);
    }

    public function scopeEnterpriseConnection($query) {
        $query->where('user_id', auth()->user()->id)
            ->orWhere(function ($query) {
                $query->where('enterprise_id', auth()->user()->enterprise_id)
                    ->where('status', ConnectionStatus::PUBLIC);
            });
    }

}
