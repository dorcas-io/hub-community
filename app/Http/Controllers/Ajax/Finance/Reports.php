<?php

namespace App\Http\Controllers\Ajax\Finance;

use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Reports extends Controller
{
    /**
     * Reports constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->data = [];
    }
    
    public function createReport(Request $request, Sdk $sdk)
    {
    
    }
}