<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'connection_id',
        'type',
        'content',
        'title'
    ];

    public function histories(): HasMany
    {
        return $this->hasMany(ConnectionHistory::class)->orderBy('contacted_at', 'desc');
    }

    public function deleteHistories() 
    {
        $this->histories()->delete();
    }
}
