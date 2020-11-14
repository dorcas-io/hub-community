<?php

namespace App\Dorcas\Hub\Utilities;


class Avatar
{
    /**
     * Converts an avatar name to the appropriate URL.
     *
     * @param string $name
     *
     * @return string
     */
    public static function getUrl(string $name): string
    {
        $name = ends_with($name, '.png') ? $name : $name.'.png';
        # we first fix the name
        $directory = public_path('images/avatar');
        # path to the avatar directory
        if (!file_exists($directory . DIRECTORY_SEPARATOR . $name)) {
            # the image file does not exist
            return cdn('images/avatar/avatar-9.png');
        }
        return cdn('images/avatar/'.$name);
    }
}