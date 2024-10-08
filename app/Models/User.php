<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'login',
        'password',
        'fio',
        'phone',
        'sys_Comment',
        'delete_mark',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'phone_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function news()
    {
        return $this->hasMany(News::class);
    }

    public function peopleContent()
    {
        return $this->hasMany(PeopleContent::class);
    }

    public function vedeo()
    {
        return $this->hasMany(Video::class);
    }

    public function hasRole($role)
    {
        return $this->roles->contains('name', $role);
    }

    public function hasAllRoles(array $roles)
    {
        $userRoles = $this->roles->pluck('name');

        return collect($roles)->every(function ($role) use ($userRoles) {
            return $userRoles->contains($role);
        });
    }

    public function hasAnyRoles($roles)
    {
        $userRoles = $this->roles->pluck('name');

        if (is_array($roles)) {
            return collect($roles)->some(function ($role) use ($userRoles) {
                return $userRoles->contains($role);
            });
        }

        return $userRoles->contains($roles);
    }
}
