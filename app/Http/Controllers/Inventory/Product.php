<?php

namespace App\Http\Controllers\Inventory;

use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class Product extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Product';
        $this->data['page']['header'] = ['title' => 'Product'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'Inventory', 'href' => route('apps.inventory')],
                ['text' => 'Product', 'href' => route('apps.inventory'), 'isActive' => true],
            ]
        ];
        $this->data['currentPage'] = 'invoicing';
        $this->data['selectedSubMenu'] = 'products';
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, Sdk $sdk, string $id)
    {
        $this->setViewUiResponse($request);
        $response = $sdk->createProductResource($id)->addQueryArgument('include', 'stocks:limit(1|0),orders:limit(1|0)')
                                                    ->send('get');
        $subdomain = get_dorcas_subdomain();
        if (!empty($subdomain)) {
            $this->data['page']['header']['title'] .= ' (Store: '.$subdomain.'/store)';
        }
        $this->data['subdomains'] = $this->getSubDomains($sdk);
        # get the subdomains issued to this customer
        if (!$response->isSuccessful()) {
            abort(404, 'Could not find the product at this URL.');
        }
        $this->data['categories'] = $this->getProductCategories($sdk);
        $this->data['product'] = $product = $response->getData(true);
        $this->data['page']['title'] .= ' - ' . $product->name;
        return view('inventory.product', $this->data);
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Sdk $sdk, string $id)
    {
        try {
            $prices = [];
            if ($request->has('prices')) {
                foreach ($request->currencies as $index => $currency) {
                    $price = (float) $request->prices[$index] ?? 0;
                    $prices[] = ['currency' => $currency, 'price' => $price];
                }
            }
            $query = $sdk->createProductResource($id)->addBodyParam('name', $request->name)
                                                    ->addBodyParam('description', $request->description)
                                                    ->addBodyParam('default_price', $request->default_price)
                                                    ->addBodyParam('prices', $prices)
                                                    ->send('post');
            # send the request
            if (!$query->isSuccessful()) {
                # it failed
                $message = $query->errors[0]['title'] ?? '';
                throw new \RuntimeException('Failed while updating the product. '.$message);
            }
            $response = (material_ui_html_response(['Successfully updated product information.']))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (material_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }

    /**
     * @param string $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirect(string $id)
    {
        return redirect()->route('apps.inventory.single', [$id]);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addCategories(Request $request, Sdk $sdk, string $id)
    {
        $this->validate($request, [
            'categories' => 'required|array',
        ]);
        # validate the request
        try {
            $query = $sdk->createProductResource($id)->addBodyParam('ids', $request->input('categories', []))
                                                    ->send('POST', ['categories']);
            # send the request
            if (!$query->isSuccessful()) {
                throw new \RuntimeException('Failed while adding the selected categories. Please try again.');
            }
            Cache::forget('business.product-categories.'.$this->getCompany()->id);
            $message = ['Successfully added the selected categories.'];
            $response = (material_ui_html_response($message))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (material_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect()->route('apps.inventory.single', [$id])->with('UiResponse', $response);
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addImage(Request $request, Sdk $sdk, string $id)
    {
        $this->validate($request, [
            'image' => 'required_if:action,add_product_image|image',
        ]);
        # validate the request
        try {
            if ($request->action === 'add_product_image') {
                # update the business information
                $file = $request->file('image');
                $query = $sdk->createProductResource($id)->addMultipartParam('image', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
                                                            ->send('post', ['images']);
                # send the request
                if (!$query->isSuccessful()) {
                    throw new \RuntimeException('Failed while uploading the product image. Please try again.');
                }
                $message = ['Successfully added new product image.'];
            }
            $response = (material_ui_html_response($message))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (material_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect()->route('apps.inventory.single', [$id])->with('UiResponse', $response);
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStocks(Request $request, Sdk $sdk, string $id)
    {
        try {
            $query = $sdk->createProductResource($id)->addBodyParam('action', $request->action)
                                                    ->addBodyParam('quantity', $request->quantity)
                                                    ->addBodyParam('comment', $request->description)
                                                    ->send('post', ['stocks']);
            # send the request
            if (!$query->isSuccessful()) {
                # it failed
                $message = $query->errors[0]['title'] ?? '';
                throw new \RuntimeException('Failed while updating product stocks. '.$message);
            }
            $response = (material_ui_html_response(['Successfully updated product stocks, and inventory.']))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (material_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect()->route('apps.inventory.single', [$id])->with('UiResponse', $response);
    }
}
