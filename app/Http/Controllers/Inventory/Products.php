<?php

namespace App\Http\Controllers\Inventory;

use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Products extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Inventory';
        $this->data['page']['header'] = ['title' => 'Inventory'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'Inventory', 'href' => route('apps.inventory'), 'isActive' => true]
            ]
        ];
        $this->data['currentPage'] = 'invoicing';
        $this->data['selectedSubMenu'] = 'products';
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
        $subdomain = get_dorcas_subdomain();
        if (!empty($subdomain)) {
            $this->data['page']['header']['title'] .= ' (Store: '.$subdomain.'/store)';
        }
        $productCount = 0;
        $query = $sdk->createProductResource()->addQueryArgument('limit', 1)->send('get');
        if ($query->isSuccessful()) {
            $productCount = $query->meta['pagination']['total'] ?? 0;
        }
        $this->data['categories'] = $this->getProductCategories($sdk);
        $this->data['subdomain'] = get_dorcas_subdomain($sdk);
        # set the subdomain
        $this->data['productsCount'] = $productCount;
        return view('inventory.products', $this->data);
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(Request $request, Sdk $sdk)
    {
        $this->validate($request, [
            'name' => 'required|string|max:80',
            'currency' => 'required|string|size:3',
            'price' => 'required|numeric',
            'description' => 'nullable'
        ]);
        # validate the request
        try {
            $price = ['currency' => $request->currency, 'price' => $request->price];
            # create the price payload
            $resource = $sdk->createProductResource();
            $resource = $resource->addBodyParam('name', $request->name)
                                    ->addBodyParam('description', $request->description)
                                    ->addBodyParam('prices', [$price]);
            # the resource
            $response = $resource->send('post');
            # send the request
            if (!$response->isSuccessful()) {
                # it failed
                $message = $response->errors[0]['title'] ?? '';
                throw new \RuntimeException('Failed while adding the product. '.$message);
            }
            $response = (material_ui_html_response(['Successfully added product.']))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (material_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }
}
