<?php

namespace App\Dorcas\Support\Tabler;


use Carbon\Carbon;

class TablerNotification
{
    /** @var string  */
    public $actor;
    
    /** @var null|string  */
    public $photo;
    
    /** @var string  */
    public $activity;
    
    /** @var array  */
    public $action;
    
    /** @var Carbon|null  */
    public $timestamp;
    
    /**
     * TablerNotification constructor.
     *
     * @param string                    $actor
     * @param string                    $activity
     * @param string|array              $action
     * @param string|\DateTime|Carbon   $timestamp
     * @param string|null $photo
     */
    public function __construct(string $actor, string $activity, $action, $timestamp, string $photo = null)
    {
        $this->actor = $actor;
        $this->activity = $activity;
        $this->photo = !empty($photo) ? $photo : null;
        $this->timestamp = $this->parseTimestamp($timestamp);
        $this->action = $this->parseAction($action);
        
    }
    
    /**
     * Parses the timestamp, converting it to an instance of Carbon.
     *
     * @param string|\DateTime|Carbon   $timestamp
     *
     * @return Carbon|null
     */
    protected function parseTimestamp($timestamp): ?Carbon
    {
        if ($timestamp instanceof Carbon) {
            return $timestamp;
        } elseif ($timestamp instanceof \DateTime) {
            return Carbon::instance($timestamp);
        }
        return Carbon::parse($timestamp);
    }
    
    /**
     * Parses the supplied action to be passed to the views.
     *
     * @param $action
     *
     * @return array
     */
    protected function parseAction($action): array
    {
        if (! (is_array($action) || is_string($action))) {
            return ['data-action' => 'ignore'];
        }
        if (is_string($action)) {
            return ['data-action' => 'views', 'data-url' => $action];
        }
        $attributes = [];
        foreach ($action as $key => $value) {
            if (is_numeric($key)) {
                continue;
            }
            $attributes[$key] = $value;
        }
        return $attributes;
    }
    
    /**
     * Converts this notification instance to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'actor' => $this->actor,
            'actor_photo' => $this->photo,
            'activity' => $this->activity,
            'action' => $this->action,
            'timestamp' => $this->timestamp->format(Carbon::ISO8601)
        ];
    }
}
