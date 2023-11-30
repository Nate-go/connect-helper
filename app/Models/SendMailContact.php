<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SendMailContact extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'send_mail_id',
        'contact_id',
        'title',
        'content',
        'type',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
}
