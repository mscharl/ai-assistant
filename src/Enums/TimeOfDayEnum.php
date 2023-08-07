<?php

namespace App\Enums;

enum TimeOfDayEnum: string
{
    case earlyMorning = 'binary_sensor.tod_early_morning';
    case evening = 'binary_sensor.tod_evening';
    case midday = 'binary_sensor.tod_midday';
    case morning = 'binary_sensor.tod_morning';
    case night = 'binary_sensor.tod_night';
    case noon = 'binary_sensor.tod_noon';

    public function name(): string {
        return match ($this) {
            self::earlyMorning => 'Früh am Morgen',
            self::morning => 'Vormittag',
            self::midday => 'Mittag',
            self::noon => 'Nachmittag',
            self::evening => 'Abend',
            self::night => 'Nacht'
        };
    }
}
