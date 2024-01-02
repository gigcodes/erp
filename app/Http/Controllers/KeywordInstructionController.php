<?php

namespace App\Http\Controllers;

use App\KeywordInstruction;
use App\InstructionCategory;
use Illuminate\Http\Request;

class KeywordInstructionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $keywordInstructions = KeywordInstruction::paginate(25);

        $instructions = InstructionCategory::all();

        return view('keyword_instructions.index', compact('keywordInstructions', 'instructions'));
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
            'keywords' => 'required|array',
            'instruction_category' => 'required',
        ]);

        $keywordInstruction = new KeywordInstruction();
        $keywordInstruction->keywords = $request->get('keywords');
        $keywordInstruction->instruction_category_id = $request->get('instruction_category');
        $keywordInstruction->remark = $request->get('remark') ?? 'N/A';
        $keywordInstruction->save();

        return redirect()->action([\App\Http\Controllers\KeywordInstructionController::class, 'index'])->with('message', 'Keyword-instruction reference added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(KeywordInstruction $keywordInstruction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(KeywordInstruction $keywordInstruction)
    {
        if (! $keywordInstruction) {
            abort(404);
        }

        $instructions = InstructionCategory::all();

        return view('keyword_instructions.edit', compact('keywordInstruction', 'instructions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, KeywordInstruction $keywordInstruction)
    {
        $this->validate($request, [
            'keywords' => 'required|array',
            'instruction_category' => 'required',
        ]);

        $keywordInstruction->keywords = $request->get('keywords');
        $keywordInstruction->instruction_category_id = $request->get('instruction_category');
        $keywordInstruction->remark = $request->get('remark') ?? 'N/A';
        $keywordInstruction->save();

        return redirect()->back()->with('message', 'Keyword-instruction reference added successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Exception
     */
    public function destroy(KeywordInstruction $keywordInstruction)
    {
        if ($keywordInstruction) {
            $keywordInstruction->delete();
        }

        return redirect()->back()->with('message', 'Keyword-Instruction deleted successfully!');
    }
}
