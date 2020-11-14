<?php

namespace App\Http\Controllers\vPanel\Businesses;

use GuzzleHttp\Exception\GuzzleException;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use stdClass;

class Business extends Controller
{
    /**
     * Business constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->data = [
            'page' => ['title' => 'Business Information'],
            'header' => ['title' => 'Business Information'],
            'selectedMenu' => 'businesses',
        ];
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function index(Request $request, Sdk $sdk, string $id)
    {
        $this->setViewUiResponse($request);
        $business = self::getBusiness($sdk, $id);
        if (empty($business)) {
            return redirect()->route('vpanel.businesses')->with('UiToast', toast('Could not load business information.'));
        }
        $this->data['businessProfile'] = $business;
        $this->data['header']['title'] = $business->name;
        return view('vpanel.businesses.profile', $this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function post(Request $request, Sdk $sdk, string $id)
    {
        $this->validate($request,[
            'name' => 'required|string|max:80',
            'registration' => 'nullable|string|max:30',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:30',
            'website' => 'nullable|string|max:30'
        ]);
        # validate the request
        $toast = null;
        try {
            if ($request->has('website') && !starts_with($request->input('website'), 'http')) {
                $website = $request->input('website');
                $request->request->set('website', 'http://' . $website);
            }
            $resource = $sdk->createCompanyResource($id);
            foreach ($request->except(['_token']) as $key => $value) {
                $resource->addBodyParam($key, $value);
            }
            $response = $resource->send('post');
            if (!$response->isSuccessful()) {
                throw new \RuntimeException(
                    $response->getErrors()[0]['title'] ?? 'Error while updating the business information.'
                );
            }
            $toast = toast('Successfully updated the business\' information.');
        
        } catch (GuzzleException $e) {
            $toast = toast('Network error.');
        } catch (\RuntimeException $e) {
            $toast = toast($e->getMessage());
        }
        return redirect(url()->current())->with('UiToast', $toast->json());
    }
    
    /**
     * @param Sdk    $sdk
     * @param string $id
     *
     * @return null|stdClass
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getBusiness(Sdk $sdk, string $id): ?stdClass
    {
        $query = $sdk->createCompanyResource($id)->send('get');
        if (!$query->isSuccessful()) {
            # it failed
            return null;
        }
        return $query->getData(true);
    }
}
