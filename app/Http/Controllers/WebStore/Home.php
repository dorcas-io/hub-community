<?php

namespace App\Http\Controllers\WebStore;

use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Dorcas\Support\CartManager;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ECommerce\OnlineStore;

class Home extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Store';
        $this->data['page']['header'] = ['title' => 'Store'];
    }
    
    /**
     * @param Request     $request
     * @param string|null $slug
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, string $slug = null)
    {
        $storeOwner = $this->getCompanyViaDomain();
        # get the store owner
        if (empty($storeOwner)) {
            abort(404, 'Could not find a store at this URL.');
        }
        $this->data['categorySlug'] = $slug;
        $this->data['storeSettings'] = OnlineStore::getStoreSettings((array) $storeOwner->extra_data);
        # our store settings container
        $this->data['defaultSearch'] = $request->get('q', '');
        $this->data['storeOwner'] = $storeOwner;
        $this->data['page']['title'] = $storeOwner->name . ' ' . $this->data['page']['title'];
        $this->data['page']['header']['title'] = $storeOwner->name . ' Store';
        $this->data['cart'] = self::getCartContent($request);
        return view('webstore.shop', $this->data);
    }
    
    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function categories()
    {
        return redirect()->route('webstore');
    }
    
    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function products()
    {
        return redirect()->route('webstore');
    }
    
    public function variant_type_get(Request $request, Sdk $sdk)
    {

        $company = $this->getCompanyViaDomain();
        # get the company information
        $salesConfig = !empty($company->extra_data['salesConfig']) ? $company->extra_data['salesConfig'] : [];
        $variantTypes = !empty($salesConfig) ? $salesConfig['variant_types'] : [];
        return $variantTypes;
        //return response()->json($variantTypes);
    }


    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function productDetails(Request $request, Sdk $sdk, string $id)
    {
        $storeOwner = $this->getCompanyViaDomain();
        # get the store owner
        $this->data['storeSettings'] = OnlineStore::getStoreSettings((array) $storeOwner->extra_data);
        # our store settings container
        if (empty($storeOwner)) {
            abort(404, 'Could not find a store at this URL.');
        }
        $query = $sdk->createStoreService()->addQueryArgument('id', $id)->send('GET', [$storeOwner->id, 'product']);
        if (!$query->isSuccessful()) {
            abort(500, $query->getErrors()[0]['title'] ?? 'Something went wrong while fetching the product.');
        }
        $this->data['product'] = $product = $query->getData(true);
        $this->data['storeOwner'] = $storeOwner;
        $this->data['cart'] = self::getCartContent($request);



        $this->data['variantTypes'] = $this->variant_type_get($request,$sdk);

        //check requests params
        $search = $request->query('search', '');
        $sort = $request->query('sort', '');
        $order = $request->query('order', 'asc');
        $offset = (int) $request->query('offset', 0);
        $limit = (int) $request->query('limit', 10);
        $type = $request->query('type', 'variant');
        $parent = $request->query('parent', $id);

        $this->data['productType'] = $product->product_type;

        $isParent = $product->product_type=="default" ? true : false;
        $isVariant = $product->product_type=="variant" ? true : false;

        if ($isParent) {
            /*$req = $sdk->createStoreService();
            $req = $req->addQueryArgument('limit', $limit)
                            ->addQueryArgument('page', get_page_number($offset, $limit));
            if (!empty($type)) {
                $req = $req->addQueryArgument('product_type', $type);
            }
            if (!empty($parent)) {
                $req = $req->addQueryArgument('product_parent', $parent);
            }
            $variants = $req->send('get');
            # make the request
            if (!$variants->isSuccessful()) {
                # it failed
                $ms = $variants->errors[0]['title'] ?? '';
                throw new \RuntimeException('Failed while adding the product. '.$ms);
            }*/
            $this->data['variantProducts'] = [];

        }  elseif ($isVariant) {
            //get variant parent
            $qparent = $sdk->createStoreService()->addQueryArgument('id', $product->product_parent)->send('GET', [$storeOwner->id, 'product']);
            if (!$qparent->isSuccessful()) {
                abort(500, $query->getErrors()[0]['title'] ?? 'Something went wrong while fetching the product.');
            }
            $this->data['variantParent'] = $qparent->getData(true);
        }



        $this->data['page']['title'] = 'Product Details | '.$product->name;
        $this->data['page']['header']['title'] = $product->name . ' | ' . $storeOwner->name . ' Store';
        return view('webstore.product-details', $this->data);
    }

    /*
     * @param Request $request
     * @param string  $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function quickView(Request $request, string $id)
    {
        $storeOwner = $this->getCompanyViaDomain();
        # get the store owner
        if (empty($storeOwner)) {
            abort(404, 'Could not find a store at this URL.');
        }
        $this->data['storeOwner'] = $storeOwner;
        $url = config('dorcas-api.url') . '/store/' . $storeOwner->id . '/product?id=' . $id;
        # compose the query URL
        $json = json_decode(file_get_contents($url));
        # request the data
        if (empty($json->data)) {
            # something went wrong
            abort(500, 'Something went wrong while getting the product.');
        }
        $this->data['product'] = $product = (object) $json->data;
        $this->data['price'] = collect($product->prices->data)->where('currency', 'NGN')->first();
        return view('webstore.quick-view', $this->data);
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public static function getCartContent(Request $request): array
    {
        return (new CartManager($request))->getCart() ?:
            ['items' => [], 'total' => ['raw' => 0, 'formatted' => 0], 'currency' => 'NGN'];
    }
}
