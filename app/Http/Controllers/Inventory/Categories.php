<?php

namespace App\Http\Controllers\Inventory;

use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Categories extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Inventory Categories';
        $this->data['page']['header'] = ['title' => 'Inventory'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'Inventory', 'href' => route('apps.inventory')],
                ['text' => 'Categories', 'href' => route('apps.inventory.categories'), 'isActive' => true],
            ]
        ];
        $this->data['currentPage'] = 'invoicing';
        $this->data['selectedSubMenu'] = 'categories';
    }
    
    public function index(Request $request, Sdk $sdk)
    {
        $this->setViewUiResponse($request);
        $this->data['categories'] = $this->getProductCategories($sdk);
        return view('inventory.categories', $this->data);
    }
}
