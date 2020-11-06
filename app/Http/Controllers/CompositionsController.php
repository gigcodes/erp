<?php

namespace App\Http\Controllers;

use App\Compositions;
use Illuminate\Http\Request;

class CompositionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $compositions = Compositions::query();
        if($request->keyword != null) {
            $compositions = $compositions->where(function($q) use ($request) {
                $q->orWhere('name','like','%'.$request->keyword.'%')->orWhere('replace_with','like','%'.$request->keyword.'%');
            });
        }

        if($request->no_ref == 1) {
            $compositions = $compositions->where(function($q) use ($request) {
                $q->orWhere('replace_with','')->orWhereNull('replace_with');
            });
        }

        $compositions = $compositions->orderBy('id','desc')->paginate(12);

        return view('compositions.index', compact('compositions'));
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
        //
        $this->validate($request, [
            'name'         => 'required',
            'replace_with' => 'required',
        ]);

        Compositions::create($request->all());

        return redirect()->back();

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Compositions  $compositions
     * @return \Illuminate\Http\Response
     */
    public function show(Compositions $compositions)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Compositions  $compositions
     * @return \Illuminate\Http\Response
     */
    public function edit(Compositions $compositions)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Compositions  $compositions
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Compositions $compositions, $id)
    {
        //
        $c = $compositions->find($id);
        if ($c) {
            $c->fill($request->all());
            $c->save();
        }
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Compositions  $compositions
     * @return \Illuminate\Http\Response
     */
    public function destroy(Compositions $compositions, $id)
    {
        //
        $compositions->find($id)->delete();

        return redirect()->back();
    }
}
