<?php

namespace App\Dorcas\Support;


use Illuminate\Auth\GenericUser;
use Illuminate\Notifications\Notifiable;

class EmailNotifiable extends GenericUser
{
    use Notifiable;
}