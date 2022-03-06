<?php

namespace App\Entities;

use Firebase\JWT\JWT;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    public const PERMISSION_CLIENT = 'Cliente';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    public function permissions()
    {
        return $this->hasMany(UserPermissions::class);
    }

    public static function generateJwt(int $userId): string
    {
        $userSelected = DB::selectOne(
            'SELECT 
                users.id,
                users.name,
                users.email,
                users.created_at
            FROM users WHERE users.id = ?',
            [$userId]
        );

        return JWT::encode($userSelected, env('JWT_KEY'), 'HS256');
    }

    public static function findUserToLogin(string $email)
    {
        return DB::selectOne(
            'SELECT 
                users.password,
                users.id
            FROM users WHERE users.email = :email',
            ['email' => $email]
        );
    }
}
