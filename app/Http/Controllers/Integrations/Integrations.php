<?php

namespace App\Http\Controllers\Integrations;

use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Integrations extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Integrations';
        $this->data['page']['header'] = ['title' => 'Integrations'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'Integrations', 'href' => route('integrations'), 'isActive' => true]
            ]
        ];
        $this->data['currentPage'] = 'integrations';
    }

    public function index(Request $request, Sdk $sdk)
    {
        $availableIntegrations = config('dorcas.integrations');
        # get all the available integrations
        $integrations = collect([]);
        # the installed integrations
        $installed = $this->getIntegrations($sdk);
        $installedNames = [];
        if (!empty($installed)) {
            $installedNames = $installed->pluck('name')->all();
        }
        foreach ($availableIntegrations as $index => $integration) {
            if (($installedIndex = array_search($integration['name'], $installedNames, true)) === false) {
                continue;
            }
            $installedIntegration = $installed->get($installedIndex);
            $integration['id'] = $installedIntegration->id;
            $integration['configurations'] = $installedIntegration->configuration;
            # update the values
            $integrations->push($integration);
            # add the integration
        }
        $this->data['integrations'] = $integrations;
        return view('integrations.integrations', $this->data);
    }
}
