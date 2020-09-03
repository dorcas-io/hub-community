<?php

namespace App\Http\Controllers\Ajax\Inventory;

use App\Exceptions\RecordNotFoundException;
use Carbon\Carbon;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Orders extends Controller
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
        $sort = $request->query('sort', '');
        $order = $request->query('order', 'asc');
        $offset = (int) $request->query('offset', 0);
        $limit = (int) $request->query('limit', 10);
        $product = $request->query('product');
        # get the request parameters
        if (!empty($product)) {
            $query = $sdk->createProductResource($product)->addQueryArgument('include', 'orders:limit(10000|0)')
                                                            ->send('get');
            if (!$query->isSuccessful()) {
                // do something here
                throw new RecordNotFoundException($query->errors[0]['title'] ?? 'Could not find any matching orders.');
            }
            $this->data['rows'] = $data = $query->getData(true)->orders['data'];
            # set the data
            $this->data['total'] = count($data);
            # set the total
        } else {
            $query = $sdk->createOrderResource()->addQueryArgument('limit', $limit)
                                                ->addQueryArgument('page', get_page_number($offset, $limit));
            if (!empty($search)) {
                $query = $query->addQueryArgument('search', $search);
            }
            $response = $query->send('get');
            if (!$response->isSuccessful()) {
                // do something here
                throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Could not find any matching orders.');
            }
            $this->data['total'] = $response->meta['pagination']['total'] ?? 0;
            # set the total
            $this->data['rows'] = $response->data;
            # set the data
        }
        return response()->json($this->data);
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, Sdk $sdk, string $id)
    {
        $model = $sdk->createOrderResource($id);
        $response = $model->send('delete');
        if (!$response->isSuccessful()) {
            // do something here
            throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while deleting the order.');
        }
        $this->data = $response->getData();
        return response()->json($this->data);
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Sdk $sdk, string $id)
    {
        $dueDate = !empty($request->due_at) ? Carbon::parse($request->due_at) : null;
        $model = $sdk->createOrderResource($id)->addBodyParam('title', $request->title)
                                                ->addBodyParam('description', $request->description)
                                                ->addBodyParam('enable_reminder', (int) $request->input('reminders_on'));
        if (!empty($dueDate)) {
            $model = $model->addBodyParam('due_at', $dueDate->format('Y-m-d'));
        }
        $response = $model->send('put');
        if (!$response->isSuccessful()) {
            // do something here
            throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while updating the order.');
        }
        $this->data = $response->getData();
        return response()->json($this->data);
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteCustomer(Request $request, Sdk $sdk, string $id)
    {
        $model = $sdk->createOrderResource($id)->addBodyParam('id', $request->input('id'));
        $response = $model->send('delete',  ['customers']);
        if (!$response->isSuccessful()) {
            // do something here
            throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while deleting the order.');
        }
        $this->data = $response->getData();
        return response()->json($this->data);
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCustomerOrder(Request $request, Sdk $sdk, string $id)
    {
        $model = $sdk->createOrderResource($id)->addBodyParam('id', $request->input('id'))
                                                ->addBodyParam('is_paid', $request->input('is_paid'));
        $response = $model->send('put',  ['customers']);
        if (!$response->isSuccessful()) {
            // do something here
            throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while updating the customer order information.');
        }
        $this->data = $response->getData();
        return response()->json($this->data);
    }
}
