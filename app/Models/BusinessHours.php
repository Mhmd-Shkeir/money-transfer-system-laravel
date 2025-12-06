<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessHours extends Model
{
    protected $fillable = [
        'agent_profile_id',
        'day_of_week',
        'opening_time',
        'closing_time',
        'is_closed',
    ];

    protected $casts = [
        'is_closed' => 'boolean',
    ];

    public function agentProfile()
    {
        return $this->belongsTo(AgentProfile::class);
    }

   
    public function getDayName()
    {
        $days = [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];
        return $days[$this->day_of_week] ?? 'Unknown';
    }

  
    public static function isOpenNow($agentProfileId)
    {
        $now = now();
        $businessHours = self::where('agent_profile_id', $agentProfileId)
            ->where('day_of_week', $now->dayOfWeek)
            ->first();

        if (!$businessHours || $businessHours->is_closed) {
            return false;
        }

        $currentTime = $now->format('H:i');
        return $currentTime >= $businessHours->opening_time && $currentTime <= $businessHours->closing_time;
    }

   
    public static function getNextOpening($agentProfileId)
    {
        $businessHours = self::where('agent_profile_id', $agentProfileId)
            ->where('is_closed', false)
            ->orderBy('day_of_week')
            ->first();

        if (!$businessHours) {
            return null;
        }

        return "{$businessHours->getDayName()} at {$businessHours->opening_time}";
    }
}
