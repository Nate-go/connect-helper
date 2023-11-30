<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Template extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'template_group_id',
        'name',
        'subject',
        'content',
        'type',
        'status',
    ];

    public function templateGroup(): BelongsTo
    {
        return $this->belongsTo(TemplateGroup::class);
    }
}
