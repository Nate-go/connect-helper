<?php

namespace App\Models;

use App\Constants\TemplateConstant\TemplateStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemplateGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'enterprise_id',
        'user_id',
        'name',
        'status'
    ];

    public function templates():HasMany
    {
        return $this->hasMany(Template::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeStatusFilter($query, $values)
    {
        if (count($values) == 0) {
            return $query;
        }

        $query->whereIn('status', $values);
    }

    public function scopeEnterpriseTemplate($query)
    {
        $query->where('user_id', auth()->user()->id)
            ->orWhere(function ($query) {
                $query->where('enterprise_id', auth()->user()->enterprise_id)
                    ->where('status', TemplateStatus::PUBLIC );
            });
    }

    public function publicTemplates():HasMany
    {
        if($this->user_id == auth()->user()->id) return $this->hasMany(Template::class);
        return $this->hasMany(Template::class)->where('status', TemplateStatus::PUBLIC);
    }
}
