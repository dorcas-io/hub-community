<?php

namespace App\Http\Controllers\Finance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Transtrak extends Controller
{
    const MODE_INTRO = 'intro';
    const MODE_SETUP = 'setup';
    
    protected $notifiers = [
        'GTB' => [
            'sender' => 'gens@gtbank.com',
            'title' => 'GeNS Transaction Alert'
        ]
    ];
    
    /**
     * Transtrak constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Finance: Transtrak';
        $this->data['page']['header'] = ['title' => 'Transtrak: Get Started'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'Finance', 'href' => route('apps.finance')],
                ['text' => 'Transtrak', 'href' => route('apps.finance.transtrak'), 'isActive' => true]
            ]
        ];
        $this->data['currentPage'] = 'finance';
        $this->data['selectedSubMenu'] = 'transtrak';
    }
    
    /**
     * @param Request     $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $this->setViewUiResponse($request);
        $modes = [self::MODE_INTRO, self::MODE_SETUP];
        $mode = in_array($request->query('mode', null), $modes) ? $request->query('mode') : self::MODE_INTRO;
        # get the mode
        if ($mode === self::MODE_SETUP) {
            # we're in setup mode
            
        }
        $configurations = (array) $request->user()->extra_configurations;
        $transtrakConfig = $configurations['transtrak'] ?? [];
        $this->data['transtrakConfig'] = !empty($transtrakConfig['default_config']) ? $transtrakConfig[$transtrakConfig['default_config']] : [];
        # get the configuration
        if (empty($transtrakConfig['provider'])) {
            $this->data['providerSetup'] = [
                'provider' => 'gmail',
                'bank' => '',
                'account_no' => '',
                'username' => $request->user()->email,
                'password' => '',
                'sender_email' => '',
                'sender_subject' => '',
                'show_email_instructions' => true,
                'from_date' => '',
                'hide_subject_line' => true,
                'auto_processing' => false,
                'imap_port' => 993
            ];
        } else {
            $this->data['providerSetup'] = $transtrakConfig['provider'];
        }
        $this->data['transtrakEnabled'] = !empty($configurations['transtrak_enabled']);
        $this->data['transtrakAutoEnabled'] = !empty($configurations['transtrak_auto_enabled']);
        $this->data['bankConfig'] = $this->notifiers;
        $this->data['mode'] = $mode;
        
        return view('finance.transtrak', $this->data);
    }
}
