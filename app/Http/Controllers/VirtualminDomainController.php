<?php

namespace App\Http\Controllers;

use Auth;
use Exception;
use App\ZabbixStatus;
use Illuminate\Http\Request;
use App\Models\ZabbixWebhookData;
use App\VirtualminDomain;
use App\VirtualminHelper;
use App\ZabbixWebhookDataRemarkHistory;
use App\ZabbixWebhookDataStatusHistory;
use Illuminate\Support\Facades\Validator;

class VirtualminDomainController extends Controller
{
    private $virtualminHelper;

    public function __construct(VirtualminHelper $virtualminHelper)
    {
        $this->virtualminHelper = $virtualminHelper;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $domains = VirtualminDomain::latest()->paginate(10);
        return view('virtualmin-domain.index', ['domains' => $domains]);
    }

    public function syncDomains()
    {
        $this->virtualminHelper->syncDomains();

        return redirect()->back()->with('status', 'Domains synced successfully.');
    }

}
