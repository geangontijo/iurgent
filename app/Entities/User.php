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

    public function hasAddress(): bool|array
    {
        $addressFields = [
            'address_street_name' => ['required', 'max:255'],
            'address_neighborhood' => ['required', 'max:255'],
            'address_number' => ['required', 'max:255'],
            'address_district' => ['required', 'max:2'],
            'address_city' => ['required'],
            'address_postal_code' => ['required', 'numeric', 'max:99999999'],
            'address_longitude' => ['required', 'numeric', 'between:-90,90'],
            'address_latitude' => ['required', 'numeric', 'between:-90,90'],
        ];
        foreach ($addressFields as $fieldName => $rules) {
            if (null === $this->{$fieldName}) {
                $failedFields[$fieldName] = $rules;
            }
        }

        return 0 === count($failedFields) ? true : $failedFields;
    }

    public static function generateJwt(int $userId): string
    {
        $userSelected = DB::selectOne(
            'SELECT 
                users.id,
                users.name,
                users.email,
                users.address_street_name,
                users.address_neighborhood,
                users.address_number,
                users.address_district,
                users.address_city,
                users.address_postal_code,
                users.address_longitude,
                users.address_latitude,
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
