<?php

namespace App\Http\Controllers\Directory;

use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use App\Http\Controllers\HomeController;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AccessGrants extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Access Grants';
        $this->data['page']['header'] = ['title' => ''];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'Access Request', 'href' => route('directory.access-grant'), 'isActive' => true]
            ]
        ];
        $this->data['currentPage'] = 'access-grants';
    }
    
    public function index(Request $request)
    {
        $this->setViewUiResponse($request);
        $this->data['availableModules'] = HomeController::SETUP_UI_COMPONENTS;
        return view('directory.access-grant', $this->data);
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
            'business_id' => 'required|string',
            'modules' => 'required|array',
            'modules.*' => 'required|string'
        ]);
        # validate the request
        try {
            $query = $sdk->createCompanyResource($request->input('business_id'));
            $data = $request->only(['modules']);
            foreach ($data as $key => $value) {
                $query->addBodyParam($key, $value);
            }
            $query = $query->send('post', ['access-grant-requests']);
            # send the request
            if (!$query->isSuccessful()) {
                $message = $response->errors[0]['title'] ?? '';
                throw new \RuntimeException('Failed while sending the request. '.$message);
            }
            $response = (material_ui_html_response(['Successfully sent the request.']))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (material_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }
}
