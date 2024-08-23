<?php

namespace App\Http\Services\StatusManagement;

class StatusMatcher
{
    public static function isMatchingStatus($status, $oldStatus, $validTransitions)
    {
        if ($status->status === $oldStatus->status) {
            return true;
        }

        if (array_key_exists($status->status, $validTransitions)) {
            return in_array($oldStatus->status, $validTransitions[$status->status]);
        }

        return false;
    }
}
