<?php

namespace App\Dorcas\Support\Tabler;


class TablerNotificationCollection
{
    /** @var array  */
    protected $notifications = [];
    
    /**
     * TablerNotificationCollection constructor.
     *
     * @param array $notifications
     */
    public function __construct(array $notifications = [])
    {
        $this->setNotifications($notifications);
    }
    
    /**
     * @param array $notifications
     *
     * @return TablerNotificationCollection
     */
    public function setNotifications(array $notifications): TablerNotificationCollection
    {
        if (empty($notifications)) {
            return $this;
        }
        $notifications = array_filter($notifications, function ($n) {
           return $n instanceof TablerNotification;
        });
        $this->notifications = $notifications;
        return $this;
    }
    
    /**
     * @param TablerNotification $notification
     *
     * @return $this
     */
    public function add(TablerNotification $notification): TablerNotificationCollection
    {
        $this->notifications[] = $notification;
        return $this;
    }
    
    /**
     * @return array
     */
    public function getNotifications(): array
    {
        return $this->notifications;
    }
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        $collection = array_map(function ($n) {
            return $n->toArray();
        }, $this->notifications);
        return $collection;
    }
}
