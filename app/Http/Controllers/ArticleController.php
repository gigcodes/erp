<?php

namespace App\Http\Controllers;

use App\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $articles = Article::paginate(100);
        return View('articles.index',
        compact('articles')
        );
    }

    /**
     * Get Broken Links Details
     * Function for display
     * 
     * @return json response
     */
    public function updateDomain(Request $request) {
        $checker = BackLinkChecker::findOrFail($request['id']);
        $checker->domains = $request['domain_name'];
        $checker->save();
        return response()->json([
            'type' => 'success',
            'message' => 'Domain Updated'
        ]);
    }

    /**
     * Updated Title
     * Function for display
     * 
     * @return json response
     */
    public function updateTitle(Request $request) {
        $checker = BackLinkChecker::findOrFail($request['id']);
        $checker->title = $request['title'];
        $checker->save();
        return response()->json([
            'type' => 'success',
            'message' => 'Title Updated'
        ]);
    }

}
