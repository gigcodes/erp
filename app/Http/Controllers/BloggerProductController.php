<?php

namespace App\Http\Controllers;

use App\Blogger;
use App\BloggerProduct;
use App\Brand;
use App\Helpers;
use App\Http\Requests\CreateBloggerProductRequest;
use App\ReplyCategory;
use App\User;
use Illuminate\Http\Request;

class BloggerProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:blogger-all');
        $this->middleware(function ($request, $next) {
            session()->forget('active_tab');
            return $next($request);
        });
    }

    public function store(BloggerProduct $bloggerProduct, CreateBloggerProductRequest $request)
    {
        $bloggerProduct->create($request->all());
        return redirect()->route('blogger.index')->withSuccess('You have successfully saved a blogger product record');
    }

    public function update(BloggerProduct $bloggerProduct, CreateBloggerProductRequest $request)
    {
        $blogger = $bloggerProduct->blogger;
        if($request->has('default_phone')){
            $blogger->default_phone = $request->get('default_phone');
        }
        if($request->has('whatsapp_number')){
            $blogger->default_phone = $request->get('whatsapp_number');
        }
        $blogger->save();
        $bloggerProduct->fill($request->all())->save();
        return redirect()->route('blogger.index')->withSuccess('You have successfully updated a blogger product record');
    }

    public function show(BloggerProduct $blogger_product, Blogger $blogger, Brand $brand)
    {
        $this->data['bloggers'] = $blogger->pluck('name','id');
        $this->data['brands'] = $brand->pluck('name','id');
        $this->data['blogger_product'] = $blogger_product;
        $this->data['reply_categories'] = ReplyCategory::all();
        $this->data['users_array'] = Helpers::getUserArray(User::all());

        return view('blogger.show', $this->data);
    }

    public function uploadImages(Request $request)
    {
        $this->validate($request, [
            'image' => 'image'
        ]);
        dd($request->file('image'));

    }
}
