<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConnectionTag extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'connection_id',
        'tag_id',
    ];
}
