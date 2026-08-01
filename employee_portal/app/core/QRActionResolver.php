<?php

class QRActionResolver
{
    public static function resolveAction($todayRecord = null, $preferredAction = null)
    {
        $action = strtoupper(trim((string) ($preferredAction ?? '')));

        if ($action === 'TIME_IN' || $action === 'TIME_OUT') {
            return $action;
        }

        if ($todayRecord && !empty($todayRecord['time_in']) && empty($todayRecord['time_out'])) {
            return 'TIME_OUT';
        }

        if ($todayRecord && !empty($todayRecord['time_in']) && !empty($todayRecord['time_out'])) {
            return 'COMPLETED';
        }

        return 'TIME_IN';
    }

    public static function isValidAction($action)
    {
        return in_array(strtoupper(trim((string) $action)), ['TIME_IN', 'TIME_OUT'], true);
    }
}
