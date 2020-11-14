<?php

namespace App\Http\Controllers\Directory;

use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Directory extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Professionals Directory';
        $this->data['page']['header'] = ['title' => 'Professionals Directory'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'Directory', 'href' => route('directory'), 'isActive' => true]
            ]
        ];
        $this->data['currentPage'] = 'professional_directory';
        $this->data['viewMode'] = 'professional';
    }
    
    /**
     * @param Request     $request
     * @param Sdk         $sdk
     *
     * @param string|null $viewTemplate
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function search(Request $request, Sdk $sdk, string $viewTemplate = null)
    {
        $this->setViewUiResponse($request);
        $view = strtolower($request->query('views', 'listing'));
        $this->data['categories'] = $categories = $this->getProfessionalServiceCategories($sdk);
        $this->data['views'] = $view = empty($categories) ? 'listing' : $view;
        $this->data['query'] = $request->query->all();
        $this->data['query']['mode'] = $this->data['viewMode'];
        if ($view !== 'categories') {
            $categoryId = $request->query('category_id', null);
            if (!empty($categories)) {
                $category = $categories->where('id', $categoryId)->first();
                if (!empty($category)) {
                    $this->data['page']['header']['title'] .= ' (' . title_case($category->name) . ')';
                    $entry = ['text' => 'By Category', 'href' => route('directory') .'?views=categories'];
                    array_splice($this->data['breadCrumbs']['crumbs'], 0, 0, [$entry]);
                }
            } else {
                unset($this->data['query']['category_id']);
                # unset it, if it already exists
            }
        }
        $viewTemplate = $viewTemplate ?: 'directory.directory';
        return view($viewTemplate, $this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function searchVendors(Request $request, Sdk $sdk)
    {
        $this->data['page']['title'] = 'Vendors Directory';
        $this->data['page']['header']['title'] = 'Vendors Directory';
        $this->data['currentPage'] = 'vendor_directory';
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'Directory', 'href' => route('directory.vendors'), 'isActive' => true]
            ]
        ];
        $request->query->set('mode', 'vendor');
        $this->data['vendorMode'] = true;
        $this->data['viewMode'] = 'vendor';
        return $this->search($request, $sdk, 'directory.vendors-directory');
    }
}
