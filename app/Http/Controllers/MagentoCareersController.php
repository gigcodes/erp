<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Exception;
use App\Models\MagentoModuleCareers as Career;
use App\StoreWebsite;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MagentoCareersController extends Controller
{
    /**
     * @param Request $request
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $careers = Career::orderBy('created_at', 'desc')->get();
        $storeWebsites = StoreWebsite::all();
        return view('magento_careers.index', [
            'careers' => $careers,
            'storeWebsites' => $storeWebsites
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createOrEdit(Request $request)
    {
        $data = $request->all();
        $isCreate = true;

        try {
            $validator = Validator::make($data, [
                'description' => 'required|max:10000',
                'title' => 'max:255',
                'type' => 'max:255',
                'location' => 'max:255',
                'is_active' => 'max:255',
            ]);

            if ($validator->fails())
            {
                throw new ValidationException($validator, 'Validation failed.');
            }

            if (isset($data['id']) && Career::find($data['id'])) {
                $career = Career::find($data['id']);
                $isCreate = false;
            } else {
                $career = new Career();
            }
            $career->setDescription($data[Career::DESCRIPTION] ?? '');
            $career->setTitle($data[Career::TITLE] ?? '');
            $career->setType($data[Career::TYPE] ?? '');
            $career->setLocation($data[Career::LOCATION] ?? '');
            $career->setIsActive(isset($data[Career::IS_ACTIVE]) ? (bool)$data[Career::IS_ACTIVE] : false);
            $career->save();

            $career->removeAllOrgnization();
            $career->addStoreWebsites($data['store_websites'] ?? []);

            return response()->json([
                'code' => 200,
                'message' => sprintf('Career with id: %s was %s.', $career->getId(), $isCreate ? 'created' : 'edited'),
                'career' => [
                    Career::ID => $career->getId(),
                    Career::TYPE => $career->getType(),
                    Career::DESCRIPTION => $career->getDescription(),
                    Career::LOCATION => $career->getLocation(),
                    Career::IS_ACTIVE => $career->getIsActive(),
                    Career::CREATED_AT => $career->getCreatedAt(),
                    Career::TITLE => $career->getTitle(),
                    Career::STORE_WEBSITE_ID => array_map(fn ($item) => $item->title, $career->getStoreWebsites())
                ],
                'career_json' => (string)json_encode($career)
            ]);
        }
        catch (ValidationException $validationException) {
            return response()->json([
                'code' => 400,
                'message' => $validationException->getMessage()
            ], 400);
        }
        catch (Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'error'
            ], 500);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listingApi(Request $request)
    {
        $data = $request->all();

        /** @var Career $career */
        $career = Career::where(Career::IS_ACTIVE, true);

        if (isset($data['website_id']) && $data['website_id']) {
            $career->whereHas('storeWebsites', fn ($query) => $query->where('website_id', (int)$data['website_id']));
        }

        if (isset($data['title'])) {
            $career->where('title', 'like', "%{$data['title']}%");
        }

        if (isset($data['order_by']) && $data['order_by'] == 1) {
            $career->orderBy('created_at', 'asc');
        } else {
            $career->orderBy('created_at', 'desc');
        }

        /** @var Career $career */
        $career = $career->get();

        return response()->json([
            'code' => 200,
            'careers' => array_map(fn ($career) => $career->toArrayCareer(), (array)$career->getIterator())
        ]);
    }
}