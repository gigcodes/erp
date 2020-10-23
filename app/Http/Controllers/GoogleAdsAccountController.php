<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use Plank\Mediable\Mediable;
use stdClass;

class GoogleAdsAccountController extends Controller
{
    // show campaigns in main page
    public function index()
    {
        /* $oAuth2Credential = (new OAuth2TokenBuilder())
            ->fromFile(storage_path('adsapi_php.ini'))
            ->build();

        $session = (new AdWordsSessionBuilder())
            ->fromFile(storage_path('adsapi_php.ini'))
            ->withOAuth2Credential($oAuth2Credential)
            ->build();

        $adWordsServices = new AdWordsServices();

        $campInfo = $this->getCampaigns($adWordsServices, $session); */
        $googleadsaccount = \App\GoogleAdsAccount::paginate(15);
        $totalentries = $googleadsaccount->count();
        return view('googleadsaccounts.index', ['googleadsaccount' => $googleadsaccount, 'totalentries' => $totalentries]);
    }
    
    public function createGoogleAdsAccountPage()
    {
        return view('googleadsaccounts.create');
    }

    public function createGoogleAdsAccount(Request $request)
    {
        //create account
        $this->validate($request, [
            'account_name' => 'required',
            'store_websites' => 'required',
            'config_file_path' => 'required',
            'status' => 'required',
        ]);

        $accountArray = array(
            'account_name' => $request->account_name,
            'store_websites' => $request->store_websites,
            'notes' => $request->notes,
            'status' => $request->status,
        );
        $googleadsAc = \App\GoogleAdsAccount::create($accountArray);
        $account_id = $googleadsAc->id;
        if($request->file('config_file_path')){
            $uploadfile = MediaUploader::fromSource($request->file('config_file_path'))
                ->toDestination('adsapi', $account_id)
                ->upload();
            $getfilename = $uploadfile->filename . '.' . $uploadfile->extension;
            $googleadsAc->config_file_path = $getfilename;
            $googleadsAc->save();
        }
        return redirect()->back()->with('success', 'GoogleAdwords account details added successfully');
    }

    public function editeGoogleAdsAccountPage($id)
    {
        $googleAdsAc=\App\GoogleAdsAccount::find($id);
        return view('googleadsaccounts.update',['account'=>$googleAdsAc]);
    }

    public function updateGoogleAdsAccount(Request $request)
    {
        $account_id = $request->account_id;
        //update account
        $this->validate($request, [
            'account_name' => 'required',
            'store_websites' => 'required',
            'status' => 'required',
        ]);

        $accountArray = array(
            'account_name' => $request->account_name,
            'store_websites' => $request->store_websites,
            'notes' => $request->notes,
            'status' => $request->status,
        );
        $googleadsAcQuery = New \App\GoogleAdsAccount;
        $googleadsAc=$googleadsAcQuery->find($account_id);
        if($request->file('config_file_path')){
            //find old one
            if(isset($googleadsAc->config_file_path) && $googleadsAc->config_file_path!="" && \Storage::disk('adsapi')->exists($account_id.'/'.$googleadsAc->config_file_path)){
                \Storage::disk('adsapi')->delete($account_id.'/'.$googleadsAc->config_file_path);
            }
            $uploadfile = MediaUploader::fromSource($request->file('config_file_path'))
                ->toDestination('adsapi', $account_id)
                ->upload();
            $getfilename = $uploadfile->filename . '.' . $uploadfile->extension;
            $accountArray['config_file_path'] = $getfilename;
        }
        $googleadsAc->fill($accountArray);
        $googleadsAc->save();
        return redirect()->to('/googlecampaigns/adsaccount')->with('success', 'GoogleAdwords account details added successfully');
    }
}
