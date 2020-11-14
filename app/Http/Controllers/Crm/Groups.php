<?php

namespace App\Http\Controllers\Crm;

use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class Groups extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Customer Groups';
        $this->data['page']['header'] = ['title' => 'Customer Groups'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'CRM Basic', 'href' => route('apps.crm')],
                ['text' => 'Customers', 'href' => route('apps.crm.groups'), 'isActive' => true]
            ]
        ];
        $this->data['currentPage'] = 'crm';
        $this->data['selectedSubMenu'] = 'groups';
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, Sdk $sdk)
    {
        $this->setViewUiResponse($request);
        $this->data['groups'] = $this->getGroups($sdk);
        return view('crm.groups', $this->data);
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
        $this->validate($request,[
            'name' => 'required|string|max:80',
            'description' => 'nullable|string'
        ]);
        # validate the request
        try {
            $groupId = $request->has('group_id') ? $request->input('group_id') : null;
            $resource = $sdk->createGroupResource($groupId);
            $payload = $request->only(['name', 'description']);
            foreach ($payload as $key => $value) {
                $resource->addBodyParam($key, $value);
            }
            $response = $resource->send(empty($groupId) ? 'post' : 'put');
            # send the request
            if (!$response->isSuccessful()) {
                # it failed
                $message = $response->errors[0]['title'] ?? '';
                throw new \RuntimeException('Failed while '. (empty($groupId) ? 'adding' : 'updating') .' the group. '.$message);
            }
            $company = $this->getCompany();
            Cache::forget('crm.groups.'.$company->id);
            $response = (material_ui_html_response(['Successfully '. (empty($groupId) ? 'added' : 'updated the') .' group.']))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (material_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }
}
