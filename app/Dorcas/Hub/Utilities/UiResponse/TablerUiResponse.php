<?php

namespace App\Dorcas\Hub\Utilities\UiResponse;


class TablerUiResponse extends UiResponse
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
                $data = ['type' => 'danger', 'icon' => 'fe-alert-triangle', 'title' => 'Oops', 'dismissable' => $this->dismissable];
                break;
            case self::TYPE_SUCCESS:
                $data = ['type' => 'success', 'icon' => 'fe-check', 'title' => 'Awesome!', 'dismissable' => $this->dismissable];
                break;
            case self::TYPE_WARNING:
                $data = ['type' => 'warning', 'icon' => 'fe-alert-octagon', 'title' => 'Sorry', 'dismissable' => $this->dismissable];
                break;
            default:
                $data = ['type' => 'primary', 'dismissable' => $this->dismissable];
        }
        $data['slot'] = implode('<br>', $this->messages);
        # set the data
        try {
            return view('layouts.components.tabler.alerts.standard-alert', $data)->render();
        } catch (\Throwable $e) {}
        return '';
    }
}