<?php

namespace App\Http\Controllers;

use App\QuickReply;
use App\Reply;
use App\ReplyCategory;
use App\StoreWebsite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Zend\Diactoros\Response\JsonResponse;

class QuickReplyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $replies = QuickReply::all();

        return view('quick_reply.index', compact('replies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'text' => 'required',
        ]);

        $r = new QuickReply();
        $r->text = $request->get('text');
        $r->save();

        return redirect()->back()->with('message', 'Quick reply added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\QuickReply  $quickReply
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $reply = QuickReply::findOrFail($id);

        $reply->delete();

        return redirect()->back()->with('message', 'Deleted successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\QuickReply  $quickReply
     * @return \Illuminate\Http\Response
     */
    public function edit(QuickReply $quickReply)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\QuickReply  $quickReply
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, QuickReply $quickReply)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\QuickReply  $quickReply
     * @return \Illuminate\Http\Response
     */
    public function destroy(QuickReply $quickReply)
    {
        //
    }

    public function quickReplies(Request $request)
    {

        try {
            $subcat = '';
            $all_categories = ReplyCategory::where('parent_id', 0);
            if ($request->sub_category) {
                $subcat = $request->sub_category;
                $parent_id = ReplyCategory::find($request->sub_category);
                $all_categories->where('id', $parent_id->parent_id);
            }
            $all_categories = $all_categories->get();
            $store_websites = StoreWebsite::all();
            $sub_categories = ['' => 'Select Sub Category'] + ReplyCategory::where('parent_id', '!=', 0)->pluck('name', 'id')->toArray();
            $website_length = count($store_websites);

            //all categories replies related to store website id
//            $all_replies = DB::select("SELECT * from replies");
            $all_replies = Reply::all();
            $category_wise_reply = [];
            foreach ($all_replies as $replies) {
                $category_wise_reply[$replies->category_id][$replies->store_website_id][$replies->id] = $replies;
            }
            if ($all_categories) {
                foreach ($all_categories as $k => $_cat) {
                    $childs = ReplyCategory::where('parent_id', $_cat->id);
                    if ($request->sub_category) {
                        $childs->where('id', $request->sub_category);
                    }
                    $childs = $childs->get();
                    $all_categories[$k]['childs'] = $childs;
                    if ($childs) {
                        foreach ($all_categories[$k]['childs'] as $c => $_child) {
                            $subchilds = ReplyCategory::where('parent_id', $_child->id);
                            $subchilds = $subchilds->get();
                            $all_categories[$k]['childs'][$c]['subchilds'] = $subchilds;
                        }
                    }
                }
            }

            return view('quick_reply.quick_replies', compact('all_categories', 'store_websites', 'website_length', 'category_wise_reply','sub_categories','subcat'));
        } catch (\Exception $e) {
            return redirect()->back();
        }

    }

    public function getStoreWiseReplies($category_id, $store_website_id = null)
    {
        try {

            $replies = ($store_website_id)
            ? Reply::where(['category_id' => $category_id, 'store_website_id' => $store_website_id])->get()
            : Reply::where(['category_id' => $category_id])->get();
            return new JsonResponse(['status' => 1, 'data' => $replies]);
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 0, 'message' => 'Try again']);
        }
    }

    public function saveStoreWiseReply(Request $request)
    {
        try {
            if (isset($request->reply_id)) {
                //update reply
                Reply::where('id', '=', $request->reply_id)->update([
                    'reply' => $request->reply,
					'pushed_to_watson'=>0
                ]);
                return new JsonResponse(['status' => 1, 'data' => $request->reply, 'message' => 'Reply updated successfully']);
            } else {
                Reply::create([
                    'category_id' => $request->category_id,
                    'store_website_id' => $request->store_website_id,
                    'reply' => $request->reply,
                    'model' => 'Store Website',
					'pushed_to_watson'=>0
                ]);
                return new JsonResponse(['status' => 1, 'data' => $request->reply, 'message' => 'Reply added successfully']);
            }
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 0, 'message' => 'Try again']);
        }
    }

    public function saveSubCat(Request $request)
    {

        try {
            if (isset($request->sub_id)) {
                //update name
				$replyCategory = ReplyCategory::find($request->sub_id);
				if($replyCategory != null and $replyCategory['name'] != $request->name) {
					ReplyCategory::where('id', '=', $request->sub_id)->update([
						'name' => $request->name,
						'intent_id' => 0,
						'dialog_id' => 0,
						'pushed_to_watson' => 0,
					]);
				} 
                return new JsonResponse(['status' => 1, 'data' => $request->name, 'message' => 'Category updated successfully']);
            } else {
                ReplyCategory::create([
                    'name' => $request->reply,
                    'parent_id' => $request->category_id,
                ]);
                return new JsonResponse(['status' => 1, 'data' => $request->reply, 'message' => 'Category added successfully']);
            }
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 0, 'message' => 'Try again']);
        }
    }

}
