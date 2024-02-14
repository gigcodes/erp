<?php

namespace App\Http\Controllers;

use App\ColorReference;
use App\ColorNamesReference;
use Illuminate\Http\Request;

class ColorReferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $colors = ColorNamesReference::query();

        if ($request->keyword != null) {
            $colors = $colors->where(function ($q) use ($request) {
                $q->orWhere('color_name', 'like', '%' . $request->keyword . '%')->orWhere('erp_name', 'like', '%' . $request->keyword . '%');
            });
        }

        if ($request->no_ref == 1) {
            $colors = $colors->where(function ($q) {
                $q->orWhere('erp_name', '')->orWhereNull('erp_name');
            });
        }

        $colors = $colors->get();

        return view('color_references.index', compact('colors'));
    }

    public function groupColor(Request $request)
    {
        $listcolors = (new \App\Colors())->all();

        return view('color_references.listing', compact('listcolors'));
    }

    public function colorGroupBy(Request $request, $name, $threshold)
    {
        $colors = ColorNamesReference::get()->filter(function ($color) use ($name, $threshold) {
            similar_text(strtolower($color->color_name), strtolower($name), $percentage);

            return $percentage >= $threshold * 100;
        });
        $listcolors = (new \App\Colors())->all();

        return view('color_references.update', compact('colors', 'listcolors'));
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'colors' => 'required|array',
        ]);

        $colors = $request->get('colors');
        foreach ($colors as $key => $color) {
            if (! $color) {
                continue;
            }
            $c = ColorNamesReference::find($key);
            $c->erp_name = $color;
            $c->save();
        }

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(ColorReference $colorReference)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(ColorReference $colorReference)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ColorReference $colorReference)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(ColorReference $colorReference)
    {
        //
    }

    public function usedProducts(Request $request)
    {
        $q = $request->q;

        if ($q) {
            // check the type and then
            $q = '"color":"' . $q . '"';
            $products = \App\ScrapedProducts::where('properties', 'like', '%' . $q . '%')->latest()->limit(5)->get();

            $view = (string) view('compositions.preview-products', compact('products'));

            return response()->json(['code' => 200, 'html' => $view]);
        }

        return response()->json(['code' => 200, 'html' => '']);
    }

    public function affectedProduct(Request $request)
    {
        $from = $request->from;
        $to = $request->to;

        if (! empty($from) && ! empty($to)) {
            // check the type and then
            $q = '"color":"' . $from . '"';
            $total = \App\ScrapedProducts::where('properties', 'like', '%' . $q . '%')
                ->join('products as p', 'p.sku', 'scraped_products.sku')
                ->where('p.color', '')
                ->groupBy('p.id')
                ->get()->count();

            $view = (string) view('color_references.partials.affected-products', compact('total', 'from', 'to'));

            return response()->json(['code' => 200, 'html' => $view]);
        }
    }

    public function updateColorMultiple(Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        foreach ($from as $fromname) {
            $updateWithProduct = $request->with_product;
            if ($updateWithProduct == 'yes') {
                \App\Jobs\UpdateProductColorFromErp::dispatch([
                    'from' => $fromname,
                    'to' => $to,
                    'user_id' => \Auth::user()->id,
                ])->onQueue('supplier_products');
            }

            $c = ColorNamesReference::where('color_name', $fromname)->first();
            if ($c) {
                $c->erp_name = $to;
                $c->save();
            }
        }

        return response()->json(['code' => 200, 'message' => 'Your request has been pushed successfully']);
    }

    public function updateColor(Request $request)
    {
        $from = $request->from;
        $to = $request->to;

        $updateWithProduct = $request->with_product;
        if ($updateWithProduct == 'yes') {
            \App\Jobs\UpdateProductColorFromErp::dispatch([
                'from' => $from,
                'to' => $to,
                'user_id' => \Auth::user()->id,
            ])->onQueue('supplier_products');
        }

        $c = ColorNamesReference::where('color_name', $from)->first();
        if ($c) {
            $c->erp_name = $to;
            $c->save();
        }

        return response()->json(['code' => 200, 'message' => 'Your request has been pushed successfully']);
    }

    public function cmdcallcolorfix(Request $request)
    {
        \Artisan::call('fix-erp-color-issue');

        return redirect()->back();
    }
}
