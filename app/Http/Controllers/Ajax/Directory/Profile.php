<?php

namespace App\Http\Controllers\Ajax\Directory;

use App\Exceptions\DeletingFailedException;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class Profile extends Controller
{
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteSocialConnection(Request $request, Sdk $sdk)
    {
        $this->validate($request, [
            'index' => 'required|numeric'
        ]);
        # validate the request
        $model = $sdk->createDirectoryResource()->addBodyParam('index', $request->index);
        # set the model
        $response = $model->send('DELETE', ['social-connections']);
        if (!$response->isSuccessful()) {
            // do something here
            throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while deleting the social connection from your profile.');
        }
        Cache::forget('professional.profile.'.$request->user()->id);
        $this->data = $response->getData();
        return response()->json($this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function addSocialConnection(Request $request, Sdk $sdk)
    {
        if ($request->has('url') && !starts_with($request->url, ['http://', 'https://'])) {
            # we add the scheme to the URL
            $request->request->set('url', 'http://'.$request->url);
        }
        $model = $sdk->createDirectoryResource()->addBodyParam('channel', $request->channel)
                                                ->addBodyParam('id', $request->all()['id'] ?? $request->channel)
                                                ->addBodyParam('url', $request->url);
        # set the model
        $response = $model->send('POST', ['social-connections']);
        if (!$response->isSuccessful()) {
            // do something here
            throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while adding the social connection to your profile.');
        }
        Cache::forget('professional.profile.'.$request->user()->id);
        $this->data = $response->getData();
        return response()->json($this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function addCredential(Request $request, Sdk $sdk)
    {
        $data = $request->all();
        $model = $sdk->createDirectoryResource();
        # create the resource
        foreach ($data as $key => $value) {
            $model = $model->addBodyParam($key, $value);
        }
        $response = $model->send('POST', ['credentials']);
        if (!$response->isSuccessful()) {
            // do something here
            throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while adding the credential to your profile.');
        }
        Cache::forget('professional.profile.'.$request->user()->id);
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
    public function deleteCredential(Request $request, Sdk $sdk, string $id)
    {
        $model = $sdk->createDirectoryResource();
        # create the resource
        $response = $model->send('DELETE', ['credentials', $id]);
        if (!$response->isSuccessful()) {
            // do something here
            throw new DeletingFailedException($response->errors[0]['title'] ?? 'Failed while deleting the credential from your profile.');
        }
        Cache::forget('professional.profile.'.$request->user()->id);
        $this->data = $response->getData();
        return response()->json($this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function addExperience(Request $request, Sdk $sdk)
    {
        $data = $request->all();
        $model = $sdk->createDirectoryResource();
        # create the resource
        foreach ($data as $key => $value) {
            if (empty($value)) {
                continue;
            }
            $model = $model->addBodyParam($key, $value);
        }
        $response = $model->send('POST', ['experiences']);
        if (!$response->isSuccessful()) {
            // do something here
            throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while adding the experience to your profile.');
        }
        Cache::forget('professional.profile.'.$request->user()->id);
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
    public function deleteExperience(Request $request, Sdk $sdk, string $id)
    {
        $model = $sdk->createDirectoryResource();
        # create the resource
        $response = $model->send('DELETE', ['experiences', $id]);
        if (!$response->isSuccessful()) {
            // do something here
            throw new DeletingFailedException($response->errors[0]['title'] ?? 'Failed while deleting the experience from your profile.');
        }
        Cache::forget('professional.profile.'.$request->user()->id);
        $this->data = $response->getData();
        return response()->json($this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function manageServices(Request $request, Sdk $sdk)
    {
        $data = $request->all();
        $model = $sdk->createDirectoryResource();
        # create the resource
        $skipKeys = ['id', 'extra_category', 'service_type'];
        $prefixedKeys = ['type', 'frequency', 'currency', 'amount'];
        foreach ($data as $key => $value) {
            if (empty($value) || in_array($key, $skipKeys)) {
                continue;
            }
            if (in_array($key, $prefixedKeys)) {
                $key = 'cost_' . $key;
            }
            $model = $model->addBodyParam($key, $value);
        }
        if ($request->has('service_type')) {
            $model = $model->addBodyParam('type', $request->service_type);
        }
        if ($request->has('extra_category') && !empty($request->extra_category)) {
            $extras = array_map('trim', explode(',', $request->extra_category));
            $model = $model->addBodyParam('extra_categories', $extras);
            Cache::forget('professional.service_categories');
        }
        $method = !empty($data['id']) ? 'PUT' : 'POST';
        $path = ['services'];
        if (!empty($data['id'])) {
            $path[] = $data['id'];
        }
        $response = $model->send($method, $path);
        if (!$response->isSuccessful()) {
            // do something here
            throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while adding the new service to your profile.');
        }
        Cache::forget('professional.profile.'.$request->user()->id);
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
    public function deleteService(Request $request, Sdk $sdk, string $id)
    {
        $model = $sdk->createDirectoryResource();
        # create the resource
        $response = $model->send('DELETE', ['services', $id]);
        if (!$response->isSuccessful()) {
            // do something here
            throw new DeletingFailedException($response->errors[0]['title'] ?? 'Failed while deleting the service offering from your profile.');
        }
        Cache::forget('professional.profile.'.$request->user()->id);
        $this->data = $response->getData();
        return response()->json($this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getServiceRequests(Request $request, Sdk $sdk)
    {
        $limit = $request->query('limit', 12);
        $pageNumber = $request->query('page', 1);
        # get the request data
        $viewMode = $request->session()->get('viewMode');
        # get the view mode
        $query = $sdk->createDirectoryResource()->addQueryArgument('limit', $limit)
                                                ->addQueryArgument('page', $pageNumber);
        if ($viewMode !== 'professional') {
            $query->addQueryArgument('mode', $viewMode);
        }
        $query = $query->send('GET', ['service-requests']);
        if (!$query->isSuccessful()) {
            throw new \RuntimeException($response->errors[0]['title'] ??
                'Errors occurred while fetching service requests for your account.');
        }
        $json = json_decode($query->getRawResponse(), true);
        return response()->json($json);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateServiceRequest(Request $request, Sdk $sdk, string $id)
    {
        $this->validate($request, [
            'status' => 'required|in:accepted,rejected'
        ]);
        # validate the request
        $query = $sdk->createDirectoryResource()->addBodyParam('status', $request->input('status'))
                                                ->send('PUT', ['service-requests', $id]);
        if (!$query->isSuccessful()) {
            throw new \RuntimeException($query->errors[0]['title'] ??
                'Errors occurred while marking the service request. Please try again.');
        }
        return response()->json($query->getData());
    }
}
