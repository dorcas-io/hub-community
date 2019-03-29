<?php

namespace App\Http\Controllers\Integrations;

use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Install extends Controller
{
    /**
     * Install constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Install Integrations';
        $this->data['page']['header'] = ['title' => 'Install Integrations'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'Integrations', 'href' => route('integrations')],
                ['text' => 'Install', 'href' => route('integrations.install'), 'isActive' => true],
            ]
        ];
        $this->data['currentPage'] = 'integrations';
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, Sdk $sdk)
    {
        $installed = $this->getIntegrations($sdk);
        $installedNames = !empty($installed) && $installed->count() > 0 ? $installed->pluck('name')->all() : [];
        $availableIntegrations = config('dorcas.integrations');
        foreach ($availableIntegrations as $index => $integration) {
            if (!in_array($integration['name'], $installedNames)) {
                continue;
            }
            unset($availableIntegrations[$index]);
        }
        $this->data['availableIntegrations'] = $availableIntegrations;
        return view('integrations.install', $this->data);
    }
}
