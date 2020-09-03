<?php

namespace App\Http\Controllers\Ajax\Integrations;

use App\Exceptions\RecordNotFoundException;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class Integrations extends Controller
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
    public function install(Request $request, Sdk $sdk)
    {
        $this->validate($request, [
            'type' => 'required|string|max:30',
            'name' => 'required|string|max:50',
            'configurations' => 'nullable|array',
        ]);
        # validate the request
        $configurations = $request->has('configurations') ? $request->configurations : [];
        # set the configurations
        $query = $sdk->createIntegrationResource()->addBodyParam('type', $request->input('type'))
                                                    ->addBodyParam('name', $request->input('name'))
                                                    ->addBodyParam('configuration', $configurations)
                                                    ->send('POST');
        # send request
        if (!$query->isSuccessful()) {
            $message = $query->getErrors()[0]['title'] ?? 'Failed while trying to install the app integration.';
            throw new \RuntimeException($message);
        }
        $company = $request->user()->company(true, true);
        Cache::forget('integrations.'.$company->id);
        return response()->json($query->getData());
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function uninstall(Request $request, Sdk $sdk, string $id)
    {
        $model = $sdk->createIntegrationResource($id);
        $response = $model->send('delete');
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            $message = $response->getErrors()[0]['title'] ?? 'Failed while uninstalling the integration.';
            throw new RecordNotFoundException($message);
        }
        $company = $request->user()->company(true, true);
        Cache::forget('integrations.'.$company->id);
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
        $this->validate($request, [
            'type' => 'required|string|max:30',
            'name' => 'required|string|max:50',
            'configurations' => 'nullable|array',
        ]);
        # validate the request
        $configurations = $request->has('configurations') ? $request->configurations : [];
        # set the configurations
        $query = $sdk->createIntegrationResource($id)->addBodyParam('type', $request->input('type'))
                                                        ->addBodyParam('name', $request->input('name'))
                                                        ->addBodyParam('configuration', $configurations)
                                                        ->send('put');
        # send request
        if (!$query->isSuccessful()) {
            $message = $query->getErrors()[0]['title'] ?? 'Failed while trying to update the app integration.';
            throw new \RuntimeException($message);
        }
        $company = $request->user()->company(true, true);
        Cache::forget('integrations.'.$company->id);
        return response()->json($query->getData());
    }
}
