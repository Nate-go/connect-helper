<?php

namespace App\Models;

use App\Constants\ConnectionConstant\ConnectionStatus;
use App\Constants\ContactConstant\ContactType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Connection extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 
        'note',
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
        return $this->belongsToMany(User::class, 'connection_users')->whereNull('connection_users.deleted_at');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function mailContacts() : HasMany
    {
        return $this->contacts()->where('type', ContactType::MAIL);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'connection_tags')->whereNull('connection_tags.deleted_at');
    }

    public function histories(): HasManyThrough
    {
        return $this->hasManyThrough(ConnectionHistory::class, Contact::class);
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
                    ->where(function ($query) {
                        $query->where('status', ConnectionStatus::PUBLIC)
                            ->orWhere('status', ConnectionStatus::COWORKER);
                    });
            });
    }

}
