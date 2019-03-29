<?php

namespace App\Http\Controllers\Directory;

use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class Service extends Controller
{
    /**
     * Service constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Service Details';
        $this->data['page']['header'] = ['title' => 'Service Details'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'Directory', 'href' => route('directory')],
                ['text' => 'Service Information', 'href' => '#', 'isActive' => true],
            ]
        ];
        $this->data['currentPage'] = 'professional_directory';
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
        $this->data['service'] = $service = Cache::remember('directory.service.'.$id, 30, function () use ($id, $sdk) {
            $query = $sdk->createDirectoryResource()
                            ->addQueryArgument('include', 'user.professional_credentials,user.professional_experiences')
                            ->send('GET', ['services', $id]);
            if (!$query->isSuccessful()) {
                return null;
            }
            return $query->getData(true);
        });
        $isContact = false;
        $contactQuery = $sdk->createCompanyService()->addQueryArgument('user_id', $service->user['data']['id'])
                                                    ->send('GET', ['contacts']);
        if ($contactQuery->isSuccessful()) {
            # it was successful
            $isContact = !empty($contactQuery->getData());
        }
        if ($request->query->has('add_contact') && !$isContact) {
            $query = $sdk->createCompanyService()->addBodyParam('user_id', $service->user['data']['id'])
                                                ->send('POST', ['contacts']);
            # try to add the contact
            if ($query->isSuccessful()) {
                # it was successful
                $isContact = true;
            }
        }
        $this->data['is_contact'] = $isContact;
        $this->data['page']['title'] .= ' | ' . $service->title;
        return view('directory.service-details', $this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function request(Request $request, Sdk $sdk, string $id)
    {
        $this->validate($request, [
            'message' => 'required|string',
            'attachment' => 'nullable|file|max:6144',
        ], [
            'attachment.max' => 'The attachment should not be greater than 6Mb, you can compress the file into an archive.'
        ]);
        # validate the request
        try {
            $attachment = $request->file('attachment', null);
            # get the attachment, if any
            $query = $sdk->createDirectoryResource()->addBodyParam('message', $request->message);
            if (!empty($attachment)) {
                $query = $query->addMultipartParam(
                    'attachment',
                    file_get_contents($attachment->getRealPath()),
                    $attachment->getClientOriginalName()
                );
            }
            $response = $query->send('post', ['services', $id, 'requests']);
            # send the request
            if (!$response->isSuccessful()) {
                throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while sending the request. Please try again.');
            }
            $message = ['Successfully sent the service request, expect to hear back from them soon.'];
            $response = (material_ui_html_response($message))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (material_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }
}
