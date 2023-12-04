<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'send_mail_id',
        'started_at',
        'nextTime_at',
        'after_second',
        'status',
        'name',
    ];

    public function sendMail() : BelongsTo
    {
        return $this->belongsTo(SendMail::class);
    }
}
