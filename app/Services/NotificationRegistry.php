<?php

namespace App\Services;

use App\Helpers\FileHelper;
use App\NotificationType;

class NotificationRegistry
{
    /**
     * @var string
     */
    protected $path;

    /**
     * NotificationRegistry constructor.
     */
    public function __construct()
    {
        $this->setPath(app_path('Notifications'));
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     *
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return array
     */
    public function find()
    {
        $notifications = [];

        foreach (glob($this->path.'/*.php') as $file) {
            $notifications[] = $this->machineName($file);
        }

        $notifications = $this->addMetaData($notifications);

        return $notifications;
    }

    /**
     * @param string $file
     * @return string
     */
    public function machineName(string $file): string
    {
        $helper = new FileHelper($file);
        $name = explode('.', $helper->name());
        array_pop($name);

        return implode('.', $name);
    }

    /**
     * @param array $notifications
     * @return array
     */
    public function addMetaData(array $notifications): array
    {
        $notification_types = NotificationType::whereIn('machine_name', $notifications)
            ->get();

        $data = [];

        foreach ($notifications as $notification) {
            if (($item = $notification_types->where('machine_name', $notification)
                ->first())) {
                $data[$notification] = [
                    'id' => $item->id,
                    'machine_name' => $item->machine_name,
                    'title' => $item->title,
                    'is_private' => $item->is_private,
                ];
            } else {
                $data[$notification] = [
                    'id' => null,
                    'machine_name' => $notification,
                    'title' => null,
                    'is_private' => true,
                ];
            }
        }

        return $data;
    }
}
