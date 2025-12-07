<?php

namespace App;

enum JenisKelamin: int
{
    case LAKI = 1;
    case PEREMPUAN = 0;

    public function label(): string
    {
        return match ($this) {
            self::LAKI => 'Laki-laki',
            self::PEREMPUAN => 'Perempuan',
        };
    }
}
