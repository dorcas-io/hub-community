<?php

namespace App\Http\Controllers\ECommerce;

use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class Adverts extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Adverts Manager';
        $this->data['page']['header'] = ['title' => 'Adverts Manager'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'ECommerce', 'href' => route('apps.ecommerce')],
                ['text' => 'Ads Manager', 'href' => route('apps.ecommerce.adverts'), 'isActive' => true],
            ]
        ];
        $this->data['currentPage'] = 'ecommerce';
        $this->data['selectedSubMenu'] = 'adverts';
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
        $this->data['adverts'] = $this->getAdverts($sdk);
        return view('ecommerce.adverts', $this->data);
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
            'title' => 'required|string|max:80',
            'type' => 'required|string',
            'redirect_url' => 'nullable|string',
            'is_default' => 'required|string|in:0,1',
            'image' => 'required_without:advert_id|image'
        ]);
        # validate the request
        try {
            if (!$request->has('redirect_url')) {
                $redirectUrl = $request->input('redirect_url');
                $redirectUrl = starts_with($redirectUrl, ['http', 'https']) ? $redirectUrl : 'http://' . $redirectUrl;
                $request->request->set('redirect_url', $redirectUrl);
            }
            $advertId = $request->has('advert_id') ? $request->input('advert_id') : null;
            $resource = $sdk->createAdvertResource($advertId);
            $payload = $request->only(['title', 'type', 'redirect_url', 'is_default']);
            foreach ($payload as $key => $value) {
                $resource->addBodyParam($key, $value);
            }
            if ($request->has('image')) {
                $file = $request->file('image');
                $resource->addMultipartParam('image', file_get_contents($file->getRealPath(), false), $file->getClientOriginalName());
            }
            $response = $resource->send('post');
            # send the request
            if (!$response->isSuccessful()) {
                # it failed
                $message = $response->errors[0]['title'] ?? '';
                throw new \RuntimeException('Failed while '. (empty($advertId) ? 'adding' : 'updating') .' the advert. '.$message);
            }
            $company = $this->getCompany();
            Cache::forget('adverts.'.$company->id);
            $response = (material_ui_html_response(['Successfully '. (empty($advertId) ? 'added' : 'updated the') .' advert.']))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (material_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }
}
