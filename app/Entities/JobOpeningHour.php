<?php

namespace App\Entities;

use App\Exceptions\ApiError;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOpeningHour extends Model
{
    use HasFactory;

    public const DAY_NAMES = [
        'Segunda',
        'Terça',
        'Quarta',
        'Quinta',
        'Sexta',
        'Sábado',
        'Domingo',
        'Feriados',
    ];

    protected $table = 'jobs_opening_hours';

    protected $guarded = ['id'];

    public static function boot()
    {
        parent::boot();

        self::creating(function (JobOpeningHour $openingHour) {
            if (!\in_array($openingHour->day_name, self::DAY_NAMES)) {
                throw new ApiError(['day_name' => ['Nome inválido']], 422);
            }

            $willOpenIn = \DateTimeImmutable::createFromFormat('H:i', $openingHour->will_open_in);
            $willCloseIn = \DateTimeImmutable::createFromFormat('H:i', $openingHour->will_close_in);
            if ($willOpenIn > $willCloseIn) {
                throw new ApiError(['will_open_in' => ['A data de abertura deve ser menor que a de fechamento'], 'will_close_in' => ['A data de fechamento deve ser maior que a de abertura']], 422);
            }
        });
    }
}
