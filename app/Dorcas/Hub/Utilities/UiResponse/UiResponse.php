<?php

namespace App\Dorcas\Hub\Utilities\UiResponse;


abstract class UiResponse implements UiResponseInterface
{
    const TYPE_ERROR = 'error';
    const TYPE_WARNING = 'warning';
    const TYPE_INFORMATION = 'info';
    const TYPE_SUCCESS = 'success';
    
    /** @var string */
    public $type;
    
    /** @var array */
    public $messages;
    
    /** @var null|string */
    public $title;
    
    /** @var bool  */
    public $dismissable;
    
    /**
     * UiResponse constructor.
     *
     * @param array       $messages
     * @param string      $type
     * @param string|null $title
     * @param bool        $dismissable
     */
    public function __construct(
        array $messages = [],
        string $type = self::TYPE_INFORMATION,
        string $title = null,
        bool $dismissable = true
    ) {
        $this->title = $title;
        $this->dismissable = $dismissable;
        $this->setType($type)
            ->setMessages($messages);
    }
    
    /**
     * @inheritdoc
     */
    public function setType(string $type = self::TYPE_INFORMATION): UiResponseInterface
    {
        $types = [self::TYPE_ERROR, self::TYPE_INFORMATION, self::TYPE_SUCCESS, self::TYPE_WARNING];
        if (!in_array($type, $types)) {
            throw new \UnexpectedValueException('The type your specified is not one of the allowed type.');
        }
        $this->type = $type;
        return $this;
    }
    
    /**
     * @inheritdoc
     */
    public function setMessages(array $messages = []): UiResponseInterface
    {
        $this->messages = collect($messages)->map(function ($message) {
            return (string) $message;
        })->all();
        return $this;
    }
}