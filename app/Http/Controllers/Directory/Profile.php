<?php

namespace App\Http\Controllers\Directory;

use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Profile extends Controller
{
    /**
     * Profile constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Professional Profile';
        $this->data['page']['header'] = ['title' => 'Professional Profile'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'Profile', 'href' => route('directory.profile'), 'isActive' => true]
            ]
        ];
        $this->data['currentPage'] = 'professional_profile';
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
        $this->data['categories'] = $this->getProfessionalServiceCategories($sdk);
        $this->data['profile'] = $profile = $this->getProfessionalProfile($sdk);
        return view('directory.profile', $this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function vendorsIndex(Request $request, Sdk $sdk)
    {
        $this->data['page']['title'] = 'Vendors Directory';
        $this->data['page']['header']['title'] = 'Vendors Directory';
        $this->data['currentPage'] = 'vendor_profile';
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'Profile', 'href' => route('directory.vendors.profile'), 'isActive' => true]
            ]
        ];
        $this->data['vendorMode'] = true;
        return $this->index($request, $sdk);
    }
}
