<?php

namespace App\Dorcas\Support\UiResponse;


class ToastNotification
{
    /** @var string  */
    public $message;
    
    /** @var int  */
    public $durationMilliseconds = 4000;
    
    /** @var array  */
    public $config;
    
    /**
     * ToastNotification constructor.
     *
     * @param string $message
     * @param int    $durationMilli
     * @param array  $config
     */
    public function __construct(string $message, int $durationMilli = 10000, array $config = [])
    {
        $this->message = $message;
        $this->durationMilliseconds = $durationMilli;
        $this->config = (array) $config;
    }
    
    /**
     * @return array
     */
    public function json(): array
    {
        $toast = [
            'html' => $this->message,
            'displayLength' => $this->durationMilliseconds >= 1000 ? $this->durationMilliseconds : 4000
        ];
        $toast['inDuration'] = intval($this->config['inDuration'] ?? 300);
        $toast['outDuration'] = intval($this->config['outDuration'] ?? 375);
        if (!empty($this->config['classes'])) {
            $toast['classes'] = $this->config['classes'];
        }
        return $toast;
    }
}
