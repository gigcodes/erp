<?php

namespace App\Http\Controllers\Marketing;

use App\Customer;
use App\Mailinglist;
use App\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class MailinglistController extends Controller
{
    /**
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $services = Service::all();
        $list = Mailinglist::all();

        return view('marketing.mailinglist.index', compact('services', 'list'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $curl = curl_init();
        $data = [
            "folderId" => 1,
            "name" => $request->name
        ];
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.sendinblue.com/v3/contacts/lists",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                "api-key: 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",
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $res = json_decode($response);
        Mailinglist::create([
            'name' => $request->name,
            'service_id' => $request->service_id,
            'remote_id' => $res->id,
        ]);

        return response()->json(true);
    }

    /**
     * @param $id
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $customers = Customer::whereNotNull('email')->select('email', 'id', 'name')->paginate(20);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.sendinblue.com/v3/contacts",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "api-key: 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",
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $res = json_decode($response);
//        dd($res);
        $contacts = collect($res->contacts)->pluck('email')->toArray();

        return view('marketing.mailinglist.show', compact('customers', 'id', 'contacts'));
    }

    /**
     * @param $id
     * @param $email
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addToList($id, $email)
    {

        $curl = curl_init();
        $data = [
            "email" => $email,
            "listIds" => [intval($id)]
        ];

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.sendinblue.com/v3/contacts",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                "api-key: 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",
                "Content-Type: application/json"
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $res = json_decode($response);
        if (isset($res->message)) {
            return redirect()->back()->withErrors($res->message);
        } else {
            return redirect()->back()->with('message', 'Added successfully.');
        }
    }

    /**
     * @param $email
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($email)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.sendinblue.com/v3/contacts/".$email,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_HTTPHEADER => array(
                "api-key: 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",
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $res = json_decode($response);

        if (isset($res->message)) {
            return redirect()->back()->withErrors($res->message);
        } else {
            return redirect()->back()->with('message', 'Removed successfully.');
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteList($id)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.sendinblue.com/v3/contacts/lists/".$id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_HTTPHEADER => array(
                "api-key: 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",
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $res = json_decode($response);

        if (isset($res->message)) {
            return redirect()->back()->withErrors($res->message);
        } else {
            Mailinglist::where('remote_id', $id)->delete();
            return redirect()->back()->with('message', 'Removed successfully.');
        }
    }
}
