<?php

namespace App\Http\Controllers\Ajax\ECommerce;

use App\Http\Controllers\ECommerce\Website;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Emails extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data = [];
    }
    
    /**
     * @param Request $request
     * @param string  $username
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \League\Flysystem\FileNotFoundException
     * @throws \League\Flysystem\UnreadableFileException
     */
    public function delete(Request $request, string $username)
    {
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
        $deleted = $whm->deleteEmail($username, $hosting['domain'], 'remove', $hosting['username']);
        if (empty($deleted)) {
            throw new \RuntimeException('Could not remove the email account.');
        }
        return response()->json(['data' => ['Successfully removed the email account']]);
    }
}
