<?php

namespace App\Dorcas\Hub\Utilities\UiResponse;


class BootstrapUiResponse extends UiResponse
{
    /**
     * Converts the response to it's html representation.
     *
     * @return string
     */
    public function toHtml(): string
    {
        switch ($this->type) {
            case self::TYPE_ERROR:
                $data = ['type' => 'alert-danger', 'icon' => 'icon-remove-sign', 'title' => 'Oops'];
                break;
            case self::TYPE_SUCCESS:
                $data = ['type' => 'alert-success', 'icon' => 'icon-gift', 'title' => 'Awesome!'];
                break;
            case self::TYPE_WARNING:
                $data = ['type' => 'alert-warning', 'icon' => 'icon-warning-sign', 'title' => 'Sorry'];
                break;
            default:
                $data = [];
        }
        $data['slot'] = implode('<br>', $this->messages);
        # set the data
        try {
            return view('layouts.slots.bootstrap-alert', $data)->render();
        } catch (\Throwable $e) {}
        return '';
    }
}