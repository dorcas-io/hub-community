<?php

namespace App\Dorcas\Hub\Utilities\UiResponse;


interface UiResponseInterface
{
    /**
     * Sets the response type.
     *
     * @param string $type
     *
     * @return UiResponseInterface
     */
    public function setType(string $type): UiResponseInterface;

    /**
     * Sets the messages to be presented.
     *
     * @param array $messages
     *
     * @return UiResponseInterface
     */
    public function setMessages(array $messages = []): UiResponseInterface;

    /**
     * Converts the response to it's html representation.
     *
     * @return string
     */
    public function toHtml(): string;
}