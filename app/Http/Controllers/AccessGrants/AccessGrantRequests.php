<?php

namespace App\Http\Controllers\AccessGrants;

use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use App\Http\Controllers\HomeController;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class AccessGrantRequests extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data = [
            'page' => ['title' => 'Access Requests'],
            'header' => ['title' => 'Access Requests'],
            'selectedMenu' => 'access-grants'
        ];
    }
    
    /**
     * @param Request     $request
     * @param string|null $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, string $id = null)
    {
        $this->setViewUiResponse($request);
        $this->setViewUiNotifications($request);
        $this->data['arguments'] = [];
        if (!empty($id)) {
            $this->data['requestId'] = $id;
        }
        $this->data['availableModules'] = HomeController::SETUP_UI_COMPONENTS;
        return view('access-grants.listing', $this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post(Request $request, Sdk $sdk)
    {
        $this->validate($request, [
            'status' => 'required_if:action,update_request_status|string|in:accepted,rejected',
            'grant_id' => 'required_if:action,update_request_status|string',
            'modules' => 'required_if:action,update_request_status|array',
            'modules.*' => 'required_with:modules|string'
        ]);
        # validate the request
        $response = null;
        $action = $request->input('action');
        try {
            switch ($action) {
                case 'update_request_status':
                default:
                    $grantId = $request->input('grant_id');
                    $service = $sdk->createCompanyService();
                    $data = $request->only(['status', 'modules']);
                    foreach ($data as $key => $value) {
                        $service->addBodyParam($key, $value);
                    }
                    $response = $service->send('put', ['access-grant-requests/' . $grantId]);
                    if (!$response->isSuccessful()) {
                        $message = $response->errors[0]['title'] ?? '';
                        throw new \RuntimeException('Failed while updating the request status. '.$message);
                    }
                    $response = (tabler_ui_html_response(['Successfully updated the request.']))->settype(UiResponse::TYPE_SUCCESS);
                    break;
            }
        
        } catch (\Exception $e) {
            $response = (tabler_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        } catch (\Throwable $e) {
            Log::error($e->getMessage(), ['exception' => $e]);
            $response = (tabler_ui_html_response(['Something went wrong while processing your request.']))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }
}
