<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SendMail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'name',
        'type',
    ];

    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class, 'send_mail_contacts')->whereNull('send_mail_contacts.deleted_at');
    }

    public function sendMailContacts(): HasMany
    {
        return $this->hasMany(SendMailContact::class);
    }

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
