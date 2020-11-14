<?php

namespace App\Dorcas\Hub\Utilities\UiResponse;


class MaterialUiResponse extends UiResponse
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
                $data = ['type' => 'gradient-45deg-red-pink', 'icon' => '<i class="material-icons">error</i>'];
                break;
            case self::TYPE_SUCCESS:
                $data = ['type' => 'gradient-45deg-green-teal', 'icon' => '<i class="material-icons">check</i>'];
                break;
            case self::TYPE_WARNING:
                $data = ['type' => 'gradient-45deg-amber-amber', 'icon' => '<i class="material-icons">warning</i>'];
                break;
            default:
                $data = [];
        }
        $data['slot'] = implode('<br>', $this->messages);
        # set the data
        try {
            return view('layouts.slots.alert', $data)->render();
        } catch (\Throwable $e) {}
        return '';
    }
}