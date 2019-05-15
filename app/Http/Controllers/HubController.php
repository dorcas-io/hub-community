<?php

namespace App\Http\Controllers;


use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use Dorcas\ModulesLibrary\Models\ModulesLibraryResources;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class HubController extends Controller
{
    

    /**
     * Create a new Dorcas Hub controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * @param Sdk|null $sdk
     *
     * @return Collection|null
     */
    public function getLibraryResources($resource_type): ?Collection
    {
        $partner_id = !empty($partner->name) ? $partner->id : 0;
        //$company = $this->getCompany();
        //$company->id = 999999999;
        $company_id = 100006;
        # get the user
        $resources = Cache::remember('mli_resources.'.$company_id, 30, function () use ($resource_type) {
            $response = ModulesLibraryResources::where([
            	['partner_id', '=', '0'],
            	['resource_type', '=', $resource_type]
            ])->get()->toArray();
            //if (!$response->isSuccessful()) {
            //    return null;
            //}
            return collect($response)->map(function ($resource) {
                return (object) $resource;
            });
        });
        return $resources;
    }

}
