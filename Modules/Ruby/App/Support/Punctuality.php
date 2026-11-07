<?php

namespace Modules\Ruby\App\Support;

use Modules\Ruby\App\Models\Punch;

class Punctuality
{
    /**
     * Settings key holding the tenant's punctuality threshold (minutes).
     */
    public const KEY = 'ruby.attendance.punctuality_threshold';

    /**
     * Threshold default (minutes) used when the tenant never configured one.
     */
    public const DEFAULT = 15;

    /**
     * Maximum accepted threshold value (minutes).
     */
    public const MAX = 240;

    /**
     * Resolves the tenant's punctuality threshold in whole minutes, clamped to
     * the valid range (zero to MAX) regardless of what is stored.
     */
    public static function threshold(): int
    {
        return max(0, min(static::MAX, (int) \Settings::get(static::KEY, static::DEFAULT)));
    }

    /**
     * Derives the punctuality reading (text + color class) for a punch from its
     * recorded latency and type.
     *
     * Color matrix (FR-002): late ins and early outs are adverse (red beyond the
     * threshold, blue at or below it); early ins and late outs are favorable
     * (bold green); exactly on time is green.
     *
     * @return array{text: string, class: string}|null Null for punches without a
     *                                                 matched shift (no latency).
     */
    public static function reading(Punch $punch): ?array
    {
        if ($punch->latency === null) {
            return null;
        }

        if ($punch->latency === 0) {
            return ['text' => trans('ruby::attendance.on_time'), 'class' => 'text-success'];
        }

        $late = $punch->latency > 0;
        $minutes = abs($punch->latency);
        $text = trans_choice(
            $late ? 'ruby::attendance.minutes_late' : 'ruby::attendance.minutes_early',
            $minutes,
            ['m' => $minutes]
        );

        // A punch is adverse when lateness and type point the same way: a late
        // "in" (arrived after the shift start) or an early "out" (left before
        // the shift end).
        $adverse = ($punch->type === 'in') === $late;

        return [
            'text' => $text,
            'class' => $adverse
                ? ($minutes > static::threshold() ? 'text-danger' : 'text-primary')
                : 'text-success font-weight-bold',
        ];
    }
}
