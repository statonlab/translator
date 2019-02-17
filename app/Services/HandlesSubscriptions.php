<?php

namespace App\Services;

use App\NotificationType;
use App\User;
use Illuminate\Notifications\Notification;

trait HandlesSubscriptions
{
    /**
     * @param \App\User $user
     * @param $notification
     * @return bool
     */
    public function isSubscribed(User $user, $notification): bool
    {
        if (is_string($notification)) {
            $machine_name = $notification;
        } elseif ($notification instanceof Notification) {
            $machine_name = get_class($notification);
        }

        if (strpos($machine_name, '\\') !== false) {
            $machine_name = explode('\\', $machine_name);
            $machine_name = $machine_name[count($machine_name) - 1];
        }

        $notification = NotificationType::where('machine_name', $machine_name)->first();
        if (! $notification) {
            return true;
        }

        if ($notification->is_private) {
            return true;
        }

        return $notification->users()->where('users.id', $user->id)->exists();
    }
}
