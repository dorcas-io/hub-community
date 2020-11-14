<?php

namespace App\Http\Controllers\Ajax\Directory;

use App\Exceptions\RecordNotFoundException;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Directory extends Controller
{
    /**
     * Directory constructor.
     */
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
        $search = $request->query('search', '');
        $sort = $request->query('sort', '');
        $order = $request->query('order', 'asc');
        $offset = (int) $request->query('offset', 0);
        $limit = (int) $request->query('limit', 10);
        $mode = strtolower($request->query('mode', 'professional'));
        # get the request parameters
        $query = $sdk->createDirectoryResource();
        $query = $query->addQueryArgument('limit', $limit)
                        ->addQueryArgument('page', get_page_number($offset, $limit))
                        ->addQueryArgument('mode', $mode);
        if ($request->query->has('category_id')) {
            $query = $query->addQueryArgument('category_id', $request->query('category_id'));
        }
        if (!empty($search)) {
            $query = $query->addQueryArgument('search', $search);
        }
        $response = $query->send('get', ['services']);
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Could not find any matching professionals in the directory.');
        }
        $this->data['total'] = $response->meta['pagination']['total'] ?? 0;
        # set the total
        $this->data['rows'] = $response->data;
        # set the data
        return response()->json($this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function vendorContacts(Request $request, Sdk $sdk)
    {
        $search = $request->query('search', '');
        $limit = (int) $request->query('limit', 10);
        $page = (int) $request->query('page', 1);
        $query = $sdk->createCompanyService()->addQueryArgument('page', $page)
                                            ->addQueryArgument('limit', $limit);
        if (!empty($search)) {
            $query = $query->addQueryArgument('search', $search);
        }
        $response = $query->send('GET', ['contacts']);
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Could not find any matching contacts in the directory.');
        }
        $this->data['meta'] = $response->meta;
        $this->data['total'] = $response->meta['pagination']['total'] ?? 0;
        # set the total
        $this->data['rows'] = $response->data;
        # set the data
        return response()->json($this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function removeContact(Request $request, Sdk $sdk, string $id)
    {
        $response = $sdk->createCompanyService()->send('DELETE', ['contacts', $id]);
        # send the delete request
        if (!$response->isSuccessful()) {
            throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Could not remove the contact.');
        }
        return response()->json($response->getData());
    }
}
