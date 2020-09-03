<?php

namespace App\Http\Controllers\Businesses\Teams;

use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class Teams extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Teams';
        $this->data['page']['header'] = ['title' => 'Teams'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'Business', 'href' => route('business')],
                ['text' => 'Departments', 'href' => route('business.teams'), 'isActive' => true],
            ]
        ];
        $this->data['currentPage'] = 'hr';
        $this->data['selectedSubMenu'] = 'teams';
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, Sdk $sdk)
    {
        $this->setViewUiResponse($request);
        $this->data['teams'] = $this->getTeams($sdk);
        return view('business.teams.teams', $this->data);
    }
}
