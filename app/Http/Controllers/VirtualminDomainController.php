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

        return redirect()->back()->with('success', 'Domains synced successfully.');
    }

    public function enableDomain(Request $request, $id)
    {
        try {
            // Find the domain in the local database
            $domain = VirtualminDomain::findOrFail($id);

            // Enable the domain using Virtualmin API
            $response = $this->virtualminHelper->enableDomain($domain->name);

            // Maintain Log depends on the response in new Table

            // Update the domain status in your local database if needed
            $domain->update(['is_enabled' => true]);

            return redirect()->back()->with('success', 'Domain enabled successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }
    }

    public function disableDomain(Request $request, $id)
    {
        try {
            // Find the domain in the local database
            $domain = VirtualminDomain::findOrFail($id);

            // Disable the domain using Virtualmin API
            $response = $this->virtualminHelper->disableDomain($domain->name);

            // Maintain Log depends on the response in new Table

            // Update the domain status in your local database if needed
            $domain->update(['is_enabled' => false]);

            return redirect()->back()->with('success', 'Domain disabled successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }
    }

    public function deleteDomain(Request $request, $id)
    {
        try {
            // Find the domain in the local database
            $domain = VirtualminDomain::findOrFail($id);

            // Disable the domain using Virtualmin API
            $response = $this->virtualminHelper->deleteDomain($domain->name);

            // Maintain Log depends on the response in new Table

            // Update the domain status in your local database if needed
            $domain->delete();

            return redirect()->back()->with('success', 'Domain deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }
    }

}
