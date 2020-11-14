<?php

namespace App\Http\Controllers\Ajax\Business;

use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Businesses extends Controller
{
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
        $limit = (int) $request->query('limit', 20);
        # get the request parameters
        $resource = $sdk->createCompanyResource()->addQueryArgument('page', get_page_number($offset, $limit));
        if (!empty($search)) {
            $resource->addQueryArgument('search', $search);
        }
        $response = $resource->send('get');
        if (!$response->isSuccessful()) {
            $message = $response->getErrors()[0]['title'] ?? 'Failed while trying to fetching the businesses';
            throw new \RuntimeException($message);
        }
        # next up - we need to update the company information
        $this->data['total'] = $response->meta['pagination']['total'] ?? 0;
        # set the total
        $this->data['rows'] = $response->data;
        # set the data
        return response()->json($this->data);
    }
}
