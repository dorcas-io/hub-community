<?php

namespace App\Http\Controllers\Ajax\vPanel;

use App\Exceptions\DeletingFailedException;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Businesses extends Controller
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
        $search = $request->query('search', '');
        $offset = (int) $request->query('offset', 0);
        $limit = (int) $request->query('limit', 10);
        $filters = $request->query('filters');
        # get the request parameters
        $resource = $sdk->createPartnerResource()->addQueryArgument('page', get_page_number($offset, $limit));
        if (!empty($search)) {
            $resource->addQueryArgument('search', $search);
        }
        if (!empty($filters)) {
            $resource->addQueryArgument('filters', $filters);
        }
        $response = $resource->send('get', ['companies']);
        if (!$response->isSuccessful()) {
            $message = $response->getErrors()[0]['title'] ?? 'Failed while trying to fetch the companies.';
            throw new \RuntimeException($message);
        }
        # next up - we need to update the company information
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
    public function delete(Request $request, Sdk $sdk, string $id)
    {
        $resource = $sdk->createPartnerResource();
        if ($request->has('purge')) {
            $resource->addBodyParam('purge', 1);
        }
        $response = $resource->send('delete', ['companies', $id]);
        if (!$response->isSuccessful()) {
            $message = $response->getErrors()[0]['title'] ?? 'Failed while trying to delete the company.';
            throw new DeletingFailedException($message);
        }
        return response()->json($response->getData());
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchInvites(Request $request, Sdk $sdk)
    {
        $search = $request->query('search', '');
        $offset = (int) $request->query('offset', 0);
        $limit = (int) $request->query('limit', 10);
        $filters = $request->query('filters');
        # get the request parameters
        $resource = $sdk->createPartnerResource()->addQueryArgument('page', get_page_number($offset, $limit))
                                                ->addQueryArgument('include', 'inviter,inviting_user');
        if (!empty($search)) {
            $resource->addQueryArgument('search', $search);
        }
        if (!empty($filters)) {
            $resource->addQueryArgument('filters', $filters);
        }
        $response = $resource->send('get', ['invites']);
        if (!$response->isSuccessful()) {
            $message = $response->getErrors()[0]['title'] ?? 'Failed while trying to fetch the invites.';
            throw new \RuntimeException($message);
        }
        # next up - we need to update the company information
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
    public function deleteInvite(Request $request, Sdk $sdk, string $id)
    {
        $resource = $sdk->createPartnerResource();
        $response = $resource->send('delete', ['invites', $id]);
        if (!$response->isSuccessful()) {
            $message = $response->getErrors()[0]['title'] ?? 'Failed while trying to delete the invites.';
            throw new DeletingFailedException($message);
        }
        return response()->json($response->getData());
    }
}
