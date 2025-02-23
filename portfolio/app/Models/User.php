<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'location',
        'profile_picture',
        'cover_photo',
        'bio',
        'role_id',
        'is_active',
        'violation_count'
    ];
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    // Phương thức kiểm tra quyền của người dùng
    public function hasRole($role)
    {
        return $this->role->roleName === $role;
    }
    public function photos()
    {
        return $this->hasMany(Photo::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function galleries()
    {
        return $this->hasMany(Gallery::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
    // Mối quan hệ đến các báo cáo mà người này đã tạo
    public function reports()
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    // Mối quan hệ đến các báo cáo mà người này bị tố cáo
    public function violations()
    {
        return $this->hasMany(Report::class, 'violator_id');
    }
    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id');
    }

    public function followings()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id');
    }
    /**
     * Kiểm tra xem user hiện tại có follow user khác không
     */
    public function isFollowing($userId)
    {
        return Follow::where('follower_id', $this->id)
            ->where('following_id', $userId)
            ->exists();
    }
    public function blockedUsers()
    {
        return $this->hasMany(Block::class, 'blocker_id');
    }

    public function blockedBy()
    {
        return $this->hasMany(Block::class, 'blocked_id');
    }
    /**
     * Kiểm tra xem người dùng hiện tại có bị một user khác chặn không
     */
    public function isBlockedBy($userId)
    {
        return Block::where('blocker_id', $userId)
            ->where('blocked_id', $this->id)
            ->exists();
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $dates = ['join_date'];
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
