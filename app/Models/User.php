<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Constants\ConnectionConstant\ConnectionStatus;
use App\Constants\ScheduleConstant\ScheduleStatus;
use App\Constants\TemplateConstant\TemplateStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'enterprise_id',
        'image_url',
        'phonenumber',
        'gender',
        'date_of_birth',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function gmailToken(): HasOne
    {
        return $this->hasOne(GmailToken::class);
    }

    public function enterprise(): BelongsTo
    {
        return $this->belongsTo(Enterprise::class);
    }

    public function connections(): BelongsToMany
    {
        return $this->belongsToMany(Connection::class, 'connection_users')->whereNull('connection_users.deleted_at')
            ->orWhere('connections.status', ConnectionStatus::COWORKER);
    }

    public function ownConnections(): HasMany
    {
        return $this->hasMany(Connection::class);
    }

    public function templateGroups(): HasMany
    {
        return $this->hasMany(TemplateGroup::class);
    }

    public function publicTemplateGroups(): HasMany
    {
        return $this->templateGroups->where('status', TemplateStatus::PUBLIC);
    }

    public function coworkers(): HasMany
    {
        return $this->hasMany(User::class, 'enterprise_id', 'enterprise_id')
            ->where('id', '!=', $this->id);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function hasSchedules()
    {
        return $this->belongsToMany(Schedule::class, 'schedule_users')
            ->whereNull('schedule_users.deleted_at')->where('status', ScheduleStatus::PUBLISH);
    }

    public function scopeUserCoworkers($query, $user)
    {
        $query->where('enterprise_id', $user->enterprise_id)->whereNot('id', $user->id);
    }
}
