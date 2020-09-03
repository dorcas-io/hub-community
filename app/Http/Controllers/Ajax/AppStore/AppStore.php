<?php

namespace App\Http\Controllers\Ajax\AppStore;

use App\Exceptions\RecordNotFoundException;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AppStore extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data = [];
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request, Sdk $sdk)
    {
        $search = $request->query('search');
        $sort = $request->query('sort', '');
        $order = $request->query('order', 'asc');
        $offset = (int) $request->query('offset', 0);
        $page = (int) $request->query('page', 0);
        $limit = (int) $request->query('limit', 10);
        # get the request parameters
        $resource = $sdk->createAppStoreResource();
        $resource = $resource->addQueryArgument('limit', $limit)
                                ->addQueryArgument('page', $page);
        if (!empty($search)) {
            $resource->addQueryArgument('search', $search);
        }
        if ($request->has('filter')) {
            $resource->addQueryArgument('filter', $request->input('filter'));
        }
        if ($request->has('category_slug')) {
            $resource->addQueryArgument('category_slug', $request->input('category_slug'));
        }
        $response = $resource->send('get');
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Could not find any matching applications in the app store.');
        }
        return response()->json(['data' => $response->getData(), 'meta' => $response->meta]);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function installApp(Request $request, Sdk $sdk, string $id)
    {
        $response = $sdk->createAppStoreResource($id)->send('post');
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Installation failed!');
        }
        return response()->json($response->getData());
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function uninstallApp(Request $request, Sdk $sdk, string $id)
    {
        $response = $sdk->createAppStoreResource($id)->send('delete');
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Uninstallation failed!');
        }
        return response()->json($response->getData());
    }
}
