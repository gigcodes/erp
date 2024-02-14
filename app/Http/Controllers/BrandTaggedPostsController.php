<?php

namespace App\Http\Controllers;

use App\Account;
use App\BrandTaggedPosts;
use Illuminate\Http\Request;

class BrandTaggedPostsController extends Controller
{
    public function index()
    {
        $posts = BrandTaggedPosts::all();

        return view('instagram.bt.index', compact('posts'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'account_id' => 'required',
            'receipts' => 'required|array',
        ]);

        $account = Account::find($request->get('account_id'));

        $message = $request->get('message');

        $usernames = $request->get('receipts');

        foreach ($usernames as $username) {
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $photo = new InstagramPhoto($file);
            }
        }

        return redirect()->back()->with('message', 'Message sent successfully!');
    }
}
