<?php

namespace Modules\BookStack\Http\Controllers;

use Illuminate\Http\Request;
use Modules\BookStack\Actions\TagRepo;

class TagController extends Controller
{
    protected $tagRepo;

    /**
     * TagController constructor.
     */
    public function __construct(TagRepo $tagRepo)
    {
        $this->tagRepo = $tagRepo;
        parent::__construct();
    }

    /**
     * Get all the Tags for a particular entity
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getForEntity($entityType, $entityId)
    {
        $tags = $this->tagRepo->getForEntity($entityType, $entityId);

        return response()->json($tags);
    }

    /**
     * Get tag name suggestions from a given search term.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNameSuggestions(Request $request)
    {
        $searchTerm = $request->get('search', false);
        $suggestions = $this->tagRepo->getNameSuggestions($searchTerm);

        return response()->json($suggestions);
    }

    /**
     * Get tag value suggestions from a given search term.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getValueSuggestions(Request $request)
    {
        $searchTerm = $request->get('search', false);
        $tagName = $request->get('name', false);
        $suggestions = $this->tagRepo->getValueSuggestions($searchTerm, $tagName);

        return response()->json($suggestions);
    }
}
