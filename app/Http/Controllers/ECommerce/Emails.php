<?php

namespace App\Http\Controllers\ECommerce;

use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Emails extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Email Accounts';
        $this->data['page']['header'] = ['title' => 'Email Accounts'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'eCommerce', 'href' => route('apps.ecommerce')],
                ['text' => 'Domains', 'href' => route('apps.ecommerce.domains'), 'isActive' => true]
            ]
        ];
        $this->data['currentPage'] = 'ecommerce';
        $this->data['selectedSubMenu'] = 'email';
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, Sdk $sdk)
    {
        $config = $this->getCompany()->extra_data;
        # get the company configuration data
        $emails = [];
        # the emails collection
        if (!empty($config) && !empty($config['hosting'])) {
            # we actually have some hosting data
            try {
                $hosting = $config['hosting'][0];
                $whm = Website::getWhmClient($hosting['hosting_box_id']);
                # get the API client
                $emails = $whm->listEmails($hosting['domain'], $hosting['username'], 400);
                # list the email addresses on this domain
            } catch (\Exception $e) {
                $response = material_ui_html_response([$e->getMessage()])->setType(UiResponse::TYPE_ERROR);
                $request->session()->flash('UiResponse', $response);
            }
        }
        $this->setViewUiResponse($request);
        $this->data['emails'] = collect($emails)->map(function ($email) {
            return (object) $email;
        });
        # list the email addresses on this domain
        $this->data['domains'] = $domains = $this->getDomains($sdk);
        $this->data['isHostingSetup'] = !empty($config['hosting']) && !empty($domains) && $domains->count() > 0;
        return view('ecommerce.emails', $this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \League\Flysystem\FileNotFoundException
     * @throws \League\Flysystem\UnreadableFileException
     */
    public function post(Request $request, Sdk $sdk)
    {
        $this->validate($request, [
            'action' => 'required|string|in:setup_hosting,create_email',
            'username' => 'required_if:action,create_email|string',
            'domain' => 'required_if:action,create_email|string',
            'password' => 'required_if:action,create_email|string|min:8',
            'quota' => 'required_if:action,create_email|numeric',
        ]);
        # validate the request
        $action = $request->input('action');
        try {
            switch ($action) {
                case 'setup_hosting':
                    return (new Website)->post($request, $sdk);
                    break;
                default:
                    $config = $this->getCompany()->extra_data;
                    # get the company configuration data
                    if (empty($config) || empty($config['hosting'])) {
                        throw new \RuntimeException(
                            'You need to first setup hosting on your domain before you can create email accounts.'
                        );
                    }
                    $hosting = $config['hosting'][0];
                    $whm = Website::getWhmClient($hosting['hosting_box_id']);
                    # get the API client
                    $email = $whm->createEmail(
                        $request->input('username'),
                        $request->input('password'),
                        $request->input('domain'),
                        (int) $request->input('quota'),
                        0,
                        0,
                        $hosting['username']
                    );
                    if (empty($email)) {
                        throw new \RuntimeException('Could not create the email account.');
                    }
                    $response = material_ui_html_response(['Successfully created email account ' . $email])->setType(UiResponse::TYPE_SUCCESS);
            }
            
        } catch (\RuntimeException $e) {
            $response = material_ui_html_response([$e->getMessage()])->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }
}
