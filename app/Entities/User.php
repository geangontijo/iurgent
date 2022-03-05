<?php

namespace App\Entities;

use Firebase\JWT\JWT;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const PERMISSION_CLIENT = 'Cliente';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
        'email_verified_at' => 'datetime',
    ];

    public function permissions()
    {
        return $this->hasMany(UserPermissions::class);
    }

    public static function generateJwt(int $userId): string
    {
        $userSelected = DB::selectOne(
            "SELECT 
                users.id,
                users.name,
                users.email,
                users.created_at
            FROM users WHERE users.id = ?",
            [$userId]
        );

        return JWT::encode($userSelected, env('JWT_KEY'), 'HS256');
    }

    public static function findUserToLogin(string $email)
    {
        return DB::selectOne(
            "SELECT 
                users.password,
                users.id
            FROM users WHERE users.email = :email",
            ['email' => $email]
        );
    }
}
