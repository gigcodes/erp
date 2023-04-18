<?php

namespace App\Http\Controllers;

use Auth;
use App\Tag;
use App\User;
use DataTables;
use App\Models\Blog;
use App\Models\BlogTag;
use App\Models\BlogHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // \DB::enableQueryLog();
            $blogs = Blog::query();
           
            if($request->get('user_id')){
                $blogs->where('user_id', $request->get('user_id'));
            }
            if(!empty($request->get('date'))){
               
                $blogs->whereDate('created_at', $request->get('date'));
            }

            $blogs->with('user', 'blogsTag');
            $blogs = $blogs->orderBy('id', 'desc')->get();
            \Log::info($blogs->toArray());
            // dd(\DB::getQueryLog()); 

            return Datatables::of($blogs)
                ->addIndexColumn()
                ->addColumn('userName', function ($row) {
                    $user = $row->user ? $row->user->name : "N/A";
                    return $user;
                })
                ->addColumn('no_index', function ($row) {
                    if($row->no_index === 1) {
                        return 'Yes';
                    }else if($row->no_index === 0){
                        return 'No';
                    }else{
                        return '';
                    }
                  
                })
                ->addColumn('no_follow', function ($row) {
                    if($row->no_follow === 1) {
                        return 'Yes';
                    }else if($row->no_follow === 0){
                        return 'No';
                    }else{
                        return '';
                    }
                })

                ->addColumn('created_at', function ($row) {
                    $createdDate = $row->created_at ? Carbon::parse($row->created_at)->format('Y-m-d H:i:s') : "N/A";
                    return $createdDate;
                })
                ->addColumn('publish_blog_date', function ($row) {
                    $publishDate = $row->publish_blog_date ? Carbon::parse($row->publish_blog_date)->format('Y-m-d') : "N/A";
                    return $publishDate;
                    
                })
                
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a href="edit/' . $row->id . '" data-id="' . $row->id . '" data-product-id="' . $row->id . '" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i> Edit</a>&nbsp; | 
                    <a href="edit/' . $row->id . '"  data-id="' . $row->id . '" data-blog-id="' . $row->id . '" class="btn delete-blog btn-danger  btn-sm"><i class="fa fa-trash"></i> Delete</a>&nbsp; |
                    <a href="view/' . $row->id . '"  data-id="' . $row->id . '" data-blog-id="' . $row->id . '" class="btn btn-info btn-sm"><i class="fa fa-eye" aria-hidden="true"></i> View</a>&nbsp;';
                    return $actionBtn;
                })
                ->rawColumns(['action', 'userName'])
                ->make(true);
        }
        
        $users = User::get();
        return view('blogs.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $users = User::get();
        $allTag = Tag::get()->toArray();
        $tagName = array_column($allTag, 'tag');

        $tagName = implode(",", $tagName);
        $tagName = "['" . str_replace(",", "','", $tagName) . "']";
        return view('blogs.create', compact('users', 'tagName'));
    }


    public function viewAllHistory(Request $request)
    {
        if ($request->ajax()) {
            
            $blogsHistory = BlogHistory::query();
            if($request->get('user_id')){
                $blogsHistory->where('user_id', $request->get('user_id'));
            }
            $blogsHistory->with('user');
            return Datatables::of($blogsHistory)
                ->addIndexColumn()
                ->addColumn('no_index', function ($row) {
                    if($row->no_index === 1) {
                        return 'Yes';
                    }else if($row->no_index === 0){
                        return 'No';
                    }else{
                        return '';
                    }
                  
                })
                ->addColumn('no_follow', function ($row) {
                    if($row->no_follow === 1) {
                        return 'Yes';
                    }else if($row->no_follow === 0){
                        return 'No';
                    }else{
                        return '';
                    }
                })
                
                ->addColumn('created_at', function ($row) {
                    $createdDate = $row->created_at ? Carbon::parse($row->created_at)->format('Y-m-d H:i:s') : "N/A";
                    return $createdDate;
                })
                ->addColumn('userName', function ($row) {
                    $user = $row->user ? $row->user->name : "N/A";
                    return $user;
                })
                ->rawColumns(['userName'])
                ->make(true);
        }

        $users = User::get();
        return view('blogs.view-all-history', compact('users'));
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
            'user_id' => 'required',
            'idea' => 'nullable|max:524',
            'content' => 'nullable',
            'plaglarism' => 'nullable|max:8',
            'internal_link' => 'nullable|max:524',
            'external_link' => 'nullable|max:524',
            'meta_desc' => 'nullable|max:524',
            'url_structure' => 'nullable|max:524',
            'facebook' => 'nullable|max:256',
            'instagram' => 'nullable|max:524',
            'twitter' => 'nullable|max:256',
            'google' => 'nullable|max:256',
            'bing' => 'nullable|max:256',
            'publish_blog_date' => 'date_format:Y-m-d|after:' . Carbon::now()->format('Y-m-d')
        ]);
      
        $blog = Blog::create($request->all());
        if (!empty($blog)) {
            $blogId = $blog->id;
            // $tags = Tag::get();

            if (!empty($request->title_tag)) {
                $titleTags = explode(",", str_replace(' ', '', $request->title_tag));
               

                if (!empty($titleTags)) {
                    $this->titleTag($titleTags, $blogId);
                }
            }

            if (!empty($request->header_tag)) {
                $headerTags = explode(",", str_replace(' ','', $request->header_tag));

                if (!empty($headerTags)) {

                    $this->headerTag($headerTags, $blogId);
                }
            }

            if (!empty($request->italic_tag)) {
                $italicTags = explode(",", str_replace(' ','', $request->italic_tag));

                if (!empty($italicTags)) {

                    $this->italicTag($italicTags, $blogId);
                }
            }

            if (!empty($request->strong_tag)) {
                $strongTags = explode(",", str_replace(' ','', $request->strong_tag));

                if (!empty($strongTags)) {

                    $this->strongTag($strongTags, $blogId);
                }
            }
            $blogHistory = BlogHistory::create([
                'blog_id' => $blog->id,
                'plaglarism' => $blog->plaglarism,
                'internal_link' => $blog->internal_link,
                'external_link' => $blog->external_link,
                'create_time' => Carbon::now()->format('Y-m-d'),
                'no_index' => $blog->no_index,
                'no_follow' => $blog->no_follow

            ]);

            return redirect()->route('blog.index')->with('message', 'Blog has been create successfully!');
        } else {
            return redirect()->back()->with('error', 'Something Went Wrong!');
        }
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    public function titleTag($titleTags, $blogId)
    {
        foreach ($titleTags as $titleTag) {

            $checkTagExistsOrNot = Tag::where('tag', $titleTag)->first();

            if (empty($checkTagExistsOrNot)) {
                $tagAdd = Tag::create([
                    'tag' => $titleTag
                ]);

                $blogTag = BlogTag::create([
                    'blog_id' => $blogId,
                    'type' => 'title_tag',
                    'tag_id' => $tagAdd->id,
                ]);
            } else {
                $blogTag = BlogTag::create([
                    'blog_id' => $blogId,
                    'type' => 'title_tag',
                    'tag_id' => $checkTagExistsOrNot->id,
                ]);
            }
        }

        return true;
    }

    public function headerTag($headerTags, $blogId)
    {
        foreach ($headerTags as $headerTag) {

            $checkTagExistsOrNot = Tag::where('tag', $headerTag)->first();

            if (empty($checkTagExistsOrNot)) {
                $tagAdd = Tag::create([
                    'tag' => $headerTag
                ]);

                $blogTag = BlogTag::create([
                    'blog_id' => $blogId,
                    'type' => 'header_tag',
                    'tag_id' => $tagAdd->id,
                ]);
            } else {
                $blogTag = BlogTag::create([
                    'blog_id' => $blogId,
                    'type' => 'header_tag',
                    'tag_id' => $checkTagExistsOrNot->id,
                ]);
            }
        }

        return true;
    }


    public function italicTag($italicTags, $blogId)
    {
        foreach ($italicTags as $italicTag) {

            $checkTagExistsOrNot = Tag::where('tag', $italicTag)->first();

            if (empty($checkTagExistsOrNot)) {
                $tagAdd = Tag::create([
                    'tag' => $italicTag
                ]);

                $blogTag = BlogTag::create([
                    'blog_id' => $blogId,
                    'type' => 'italic_tag',
                    'tag_id' => $tagAdd->id,
                ]);
            } else {
                $blogTag = BlogTag::create([
                    'blog_id' => $blogId,
                    'type' => 'italic_tag',
                    'tag_id' => $checkTagExistsOrNot->id,
                ]);
            }
        }
        return true;
    }

    public function strongTag($strongTags, $blogId)
    {
        foreach ($strongTags as $strongTag) {

            $checkTagExistsOrNot = Tag::where('tag', $strongTag)->first();

            if (empty($checkTagExistsOrNot)) {
                $tagAdd = Tag::create([
                    'tag' => $strongTag
                ]);

                $blogTag = BlogTag::create([
                    'blog_id' => $blogId,
                    'type' => 'strong_tag',
                    'tag_id' => $tagAdd->id,
                ]);
            } else {
                $blogTag = BlogTag::create([
                    'blog_id' => $blogId,
                    'type' => 'strong_tag',
                    'tag_id' => $checkTagExistsOrNot->id,
                ]);
            }
        }
        return true;
    }

    public function show($id)
    {
        $blog = Blog::with('user', 'blogsTag')->where('id', $id)->first();

        if(!empty($blog)){
            $users = User::get();
            $headerTags = $this->headerTagGetWhenEdit($id);
            $headerTagEditValue = implode(",", $headerTags);
            $titleTags = $this->titleTagGetWhenEdit($id);
            $titleTagEditValue = implode(",", $titleTags);
            $italicTags = $this->italicTagGetWhenEdit($id);
            $italicTagEditValue = implode(",", $italicTags);
            $strongTags = $this->strongTagGetWhenEdit($id);
            $strongTagEditValue = implode(",", $strongTags);
            // $titleTags = $this->titleTagGetWhenEdit($id);
            // $headerTags = $this->headerTagGetWhenEdit($id);
            // $italicTags = $this->italicTagGetWhenEdit($id);
            // $strongTags = $this->strongTagGetWhenEdit($id);
            // $userName = !empty($blog->user->name) ? $blog->user->name : '';
            return view('blogs.show', compact('blog', 'headerTagEditValue', 'titleTagEditValue', 'italicTagEditValue', 'strongTagEditValue', 'users'));
    
        }
        return abort(404);
       
        
    }   

  

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $blog = Blog::where('id', $id)->first();
        if (!empty($blog)) {
            $users = User::get();
            
            $headerTags = $this->headerTagGetWhenEdit($id);
            $headerTagEditValue = implode(",", $headerTags);
            $titleTags = $this->titleTagGetWhenEdit($id);
            $titleTagEditValue = implode(",", $titleTags);
            $italicTags = $this->italicTagGetWhenEdit($id);
            $italicTagEditValue = implode(",", $italicTags);
            $strongTags = $this->strongTagGetWhenEdit($id);
            $strongTagEditValue = implode(",", $strongTags);

            // $headerTagAll = $this->allTagsByTagType('header_tag');
            // $headerTagAll = implode(",", $headerTagAll);
            // $headerTagAll = "['" . str_replace(",", "','", $headerTagAll) . "']";

            // $titleTagAll = $this->allTagsByTagType('title_tag');
            // $titleTagAll = implode(",", $titleTagAll);
            // $titleTagAll = "['" . str_replace(",", "','", $titleTagAll) . "']";

            // $italicTagAll = $this->allTagsByTagType('italic_tag');
            // $italicTagAll = implode(",", $italicTagAll);
            // $italicTagAll = "['" . str_replace(",", "','", $italicTagAll) . "']";

            // $strongTagAll = $this->allTagsByTagType('strong_tag');
            // $strongTagAll = implode(",", $strongTagAll);
            // $strongTagAll = "['" . str_replace(",", "','", $strongTagAll) . "']";


            return view('blogs.edit', compact('blog', 'headerTagEditValue', 'titleTagEditValue', 'italicTagEditValue', 'strongTagEditValue', 'users'));
        } else {
            return abort(404);
        }
    }

    public function allTagsByTagType($type)
    {
        $tags = [];
        $allTagsType = BlogTag::where('type', $type)->get();
        if (!empty($allTagsType)) {
            foreach ($allTagsType as $value) {

                if (!empty($value->tag->tag)) {
                    $tags[] = $value->tag->tag;
                }
            }
        }
        return $tags;
    }

    public function headerTagGetWhenEdit($blogId)
    {
        $headerTag = BlogTag::query();
        $headerTag = $headerTag->with('tag')->where('type', 'header_tag')->where('blog_id', $blogId)->get();

        $headerTags = [];
        foreach ($headerTag as $value) {

            if (!empty($value->tag->tag)) {
                $headerTags[] = $value->tag->tag;
            }
        }
        return $headerTags;
    }

    public function titleTagGetWhenEdit($blogId)
    {
        $titleTag = BlogTag::query();
        $titleTag = $titleTag->with('tag')->where('type', 'title_tag')->where('blog_id', $blogId)->get();

        $titleTags = [];
        foreach ($titleTag as $value) {

            if (!empty($value->tag->tag)) {
                $titleTags[] = $value->tag->tag;
            }
        }
        return $titleTags;
    }


    public function italicTagGetWhenEdit($blogId)
    {
        $italicTag = BlogTag::query();
        $italicTag = $italicTag->with('tag')->where('type', 'italic_tag')->where('blog_id', $blogId)->get();

        $italicTags = [];
        foreach ($italicTag as $value) {

            if (!empty($value->tag->tag)) {
                $italicTags[] = $value->tag->tag;
            }
        }
        return $italicTags;
    }


    public function strongTagGetWhenEdit($blogId)
    {
        $strongTag = BlogTag::query();
        $italicTag = $strongTag->with('tag')->where('type', 'strong_tag')->where('blog_id', $blogId)->get();

        $italicTags = [];
        foreach ($italicTag as $value) {

            if (!empty($value->tag->tag)) {
                $italicTags[] = $value->tag->tag;
            }
        }
        return $italicTags;
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $blog = Blog::where('id', $id)->first();
        if (empty($blog)) {
            return redirect()->route('blog.index')->with('error', 'Blog Not Found!');
        }

        $this->validate($request, [
            'user_id' => 'required',
            'idea' => 'nullable|max:524',
            'content' => 'nullable',
            'plaglarism' => 'nullable|max:8',
            'internal_link' => 'nullable|max:524',
            'external_link' => 'nullable|max:524',
            'meta_desc' => 'nullable|max:524',
            'url_structure' => 'nullable|max:524',
            'facebook' => 'nullable|max:256',
            'instagram' => 'nullable|max:524',
            'twitter' => 'nullable|max:256',
            'google' => 'nullable|max:256',
            'bing' => 'nullable|max:256',
            'publish_blog_date' => 'date_format:Y-m-d|after:' . Carbon::now()->format('Y-m-d')
        ]);

        $dataUpdate = [
            'user_id' => $request->user_id,
            'idea' => $request->idea,
            'keyword' => $request->keyword,
            'content' => $request->content,
            'plaglarism' => $request->plaglarism,
            'internal_link' => $request->internal_link,
            'external_link' => $request->external_link,
            'meta_desc' => $request->meta_desc,
            'url_structure' => $request->url_structure,
            'url_xml' => $request->url_xml,
            'no_follow' => $request->no_follow,
            'publish_blog_date' => $request->publish_blog_date,
            'no_index' => $request->no_index,
            'date' => $request->date,
            'facebook' => $request->facebook,
            'facebook_date' => $request->facebook_date,
            'instagram' => $request->instagram,
            'instagram_date' => $request->instagram_date,
            'twitter' => $request->twitter,
            'twitter_date' => $request->twitter_date,
            'google' => $request->google,
            'google_date' => $request->google_date,
            'bing' => $request->bing,
            'bing_date' => $request->bing_date,

        ];
        $blogUpdate = Blog::where('id', $id)->update($dataUpdate);


        if ($blogUpdate) {

            BlogHistory::create([
                'blog_id' => $id,
                'plaglarism' => $request->plaglarism,
                'user_id' => !empty(Auth::user()->id) ? Auth::user()->id: null,
                'internal_link' => $request->internal_link,
                'external_link' => $request->external_link,
                'create_time' => Carbon::now()->format('Y-m-d'),
                'no_index' => $request->no_index,
                'no_follow' => $request->no_follow

            ]);
            if (!empty($request->title_tag)) {

                $this->blogTagDeleteByType($id, 'title_tag');
                $titleTags = explode(",", str_replace(' ', '', $request->title_tag));

                if (!empty($titleTags)) {
                    $this->titleTag($titleTags, $id);
                }
            }

            if (!empty($request->header_tag)) {

                $this->blogTagDeleteByType($id, 'header_tag');
                $headerTags = explode(",", str_replace(' ', '', $request->header_tag));

                if (!empty($headerTags)) {
                    $this->headerTag($headerTags, $id);
                }
            }

            if (!empty($request->strong_tag)) {

                $this->blogTagDeleteByType($id, 'strong_tag');
                $strongTags = explode(",", str_replace(' ', '', $request->strong_tag));

                if (!empty($strongTags)) {
                    $this->strongTag($strongTags, $id);
                }
            }

            if (!empty($request->italic_tag)) {

                $this->blogTagDeleteByType($id, 'italic_tag');
                $italicTags = explode(",", str_replace(' ', '', $request->italic_tag));

                if (!empty($italicTags)) {
                    $this->italicTag($italicTags, $id);
                }
            }

            return redirect()->route('blog.index')->with('message', 'Blog has been successfully update!');
        }
    }

    public function blogTagDeleteByType($blogId, $type)
    {
        return BlogTag::where('blog_id', $blogId)->where('type', $type)->delete();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $blog = Blog::where('id', $id)->first();
        if(empty($blog)){
            return response()->json(['message' => 'Blog Not Found.']);
        }
        BlogTag::where('blog_id', $id)->delete();
        BlogHistory::where('blog_id', $id)->delete();
        $blog = Blog::where('id', $id)->delete();
        if($blog){
            return response()->json(['status'=>200,'message' => 'Blog has been successfully deleted!']);
        }else{
            return response()->json(['status'=>200,'message' => 'Something Went Wrong!']);
        }


    }
}
