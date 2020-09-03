<?php

namespace App\Http\Controllers\Ajax;

use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Settings extends Controller
{
    /**
     * Settings constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->data = [];
    }
    
    /**
     * @param Request $request
     * @param Sdk $sdk
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function update(Request $request, Sdk $sdk)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'enabled' => 'required'
        ]);
        # validate the request
        $response = null;
        # our request query
        switch ($request->input('name')) {
            case 'set_professional_status':
                $response = $sdk->createProfileService()->addBodyParam('is_professional', (int) $request->enabled)
                                                        ->send('PUT');
                break;
            case 'set_vendor_status':
                $response = $sdk->createProfileService()->addBodyParam('is_vendor', (int) $request->enabled)
                                                        ->send('PUT');
                break;
            default:
                break;
        }
        if (empty($response)) {
            throw new \RuntimeException('The request could not be sent, please try again.');
        }
        if (!$response->isSuccessful()) {
            throw new \RuntimeException($response->getErrors()[0]['title']);
        }
        return response()->json($response->getData());
    }
}
