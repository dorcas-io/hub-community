<?php

namespace App\Http\Controllers\Settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Settings extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Settings';
        $this->data['page']['header'] = ['title' => 'Settings'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'Settings', 'href' => route('settings'), 'isActive' => true]
            ]
        ];
        $this->data['currentPage'] = 'settings';
    }

    public function index()
    {
        return view('settings.settings', $this->data);
    }
}
