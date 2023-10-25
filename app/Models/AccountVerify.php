<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountVerify extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'verify_code',
        'overtimed_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
