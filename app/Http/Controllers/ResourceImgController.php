<?php

namespace App\Http\Controllers;

use Auth;
use Exception;
use App\ResourceImage;
use App\ResourceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ResourceImgController extends Controller
{
    public function index(Request $request)
    {
        $old = $request->old('parent_id');
        $Categories = ResourceCategory::attr(['name' => 'parent_id', 'class' => 'form-control'])
            ->selected($old ? $old : 1)
            ->renderAsDropdown();
        $categories = ResourceCategory::where('parent_id', '=', 0)->get();
        $sub_categories = ResourceCategory::where('parent_id', '!=', 0)->get();

        $query = ResourceImage::where('is_pending', '=', 0);
        if ($request->id) {
            $query = $query->where('id', $request->id);
        }
        \DB::enableQueryLog();

        // Log::info("termmmm" . $request->term);
        $query->where(function ($query) use ($request) {
            if ($request->term) {
                $query = $query->where('url', 'LIKE', '%' . $request->term . '%')
                ->orWhere('created_at', 'LIKE', '%' . $request->term . '%')
                ->orWhere('updated_at', 'LIKE', '%' . $request->term . '%');
            }
            if ($request->category) {
                $query = $query->orwhereIn('cat_id', $request->category);
            }
            if ($request->sub_category) {
                $query = $query->orwhereIn('sub_cat_id', $request->sub_category);
            }

            return $query;
        });

        $allresources = $query->orderBy('id', 'desc')->paginate(15)->appends(request()->except(['page']));

        if ($request->ajax()) {
            Log::info('enter in ajax');
            LOG::info(\DB::getQueryLog());

            return response()->json([
                'tbody' => view('resourceimg.partial_index', compact('allresources', 'Categories', 'categories', 'sub_categories'))->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string) $allresources->render(),
                'count' => $allresources->total(),
            ], 200);
        } else {
            return view('resourceimg.index', compact('Categories', 'categories', 'allresources', 'sub_categories'))
                ->with('i', ($request->input('page', 1) - 1) * 5);
        }
    }

    public function searchResourceimg(Request $request)
    {
        $query = ResourceImage::where('is_pending', '=', 0)->select();

        $query->where(function ($query) use ($request) {
            if ($request->term) {
                $query = $query->where('url', 'LIKE', '%' . $request->term . '%')
                ->orWhere('created_at', 'LIKE', '%' . $request->term . '%')
                ->orWhere('updated_at', 'LIKE', '%' . $request->term . '%');
            }
            if ($request->category) {
                $query = $query->orwhereIn('cat_id', $request->category);
            }
            if ($request->sub_category) {
                $query = $query->orwhereIn('sub_cat_id', $request->sub_category);
            }
        });

        $allresources = $query->orderBy('id', 'desc')->get();

        return view('resourceimg.partial_index', compact('allresources'));
    }

    public function addResourceCat(Request $request)
    {
        $this->validate($request, ['title' => 'required']);
        $input = $request->all();
        if (! ResourceCategory::create($input)) {
            return back()->with('danger', 'Something went wrong, Please try again.');
        } else {
            return back()->with('success', 'New resource category added successfully.');
        }
    }

    public function editResourceCat(ResourceCategory $category, Request $request)
    {
        $input = $request->all();
        if ($input['type'] == 'edit') {
            if ($request->method() === 'POST') {
                if ($request->input('title')) {
                    $this->validate($request, ['title' => 'required']);
                    $category = $category->find($request->input('parent_id'));
                    $category->title = $request->input('title');
                    if (! $category->save()) {
                        return redirect()->route('resourceimg.index')->with('danger', 'Something went wrong, Please try again.');
                    } else {
                        return redirect()->route('resourceimg.index')->with('success', 'Resource category updated successfully.');
                    }
                } else {
                    $old = $request->input('parent_id');
                    $Categories = ResourceCategory::attr(['name' => 'parent_id', 'class' => 'form-control'])
                        ->selected($old ? $old : 1)
                        ->renderAsDropdown();
                    $category = $category->find($request->input('parent_id'));
                    $title = $category->title;

                    return view('resourceimg.editCategory', compact('Categories', 'title'));
                }
            }
        }

        if ($input['type'] == 'delete') {
            $category_instance = new ResourceCategory();
            $category = $category_instance->find($request->input('parent_id'));

            if (ResourceCategory::isParent($category->id)) {
                return back()->with('danger', 'Can\'t delete Parent category. Please delete all the childs first');
            }

            if ($category->id == 1) {
                return back()->with('danger', 'Can\'t be delete');
            }

            $title = $category->title;
            $category->delete();

            return back()->with('success', 'deleted successfully.');
        }

        return back()->with('danger', 'Method not allowed, Please try again.');
    }

    public function addResource(Request $request)
    {
        $input = $request->all();
        // dd($request->all());
        $request->validate([
            'cat_id' => 'required',
            'sub_cat_id' => 'required',
            'url' => 'sometimes',
            'description' => 'required',
            'image' => 'sometimes',
            'image2' => 'sometimes',
        ]);
        // if ($request->input('cat_id') == 1) {
        //     return back()->with('danger', 'Please Select Category.');
        // }
        try {
            if ($request->hasFile('image')) {
                $images = $request->file('image');
                foreach ($images as $image) {
                    $name = uniqid() . time() . '.' . $image->getClientOriginalExtension();
                    $destinationPath = public_path('/category_images');
                    $image->move($destinationPath, $name);
                    $input['images'][] = $name;
                }
            }

            if ($request->image2) {
                $image = $request->image2;  // your base64 encoded
                $image = str_replace('data:image/png;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $imageName = uniqid() . time() . '.' . 'png';
                $destinationPath = public_path('/category_images');
                // dd();
                \File::put($destinationPath . '/' . $imageName, base64_decode($image));
                $input['images'][] = $imageName;
            }
            $input['images'] = ($request->hasFile('image') || $request->image2) ? json_encode($input['images']) : '';

            if (ResourceImage::create($input)) {
                return back()->with('success', 'New Resource image added successfully.');
            } else {
                return back()->with('danger', 'Something went wrong,Please try again.');
            }
        } catch (Exception $e) {
            dd($e);

            return back()->with('danger', 'Error while uploading file.');
        }
    }

    public function deleteResource(Request $request)
    {
        $input = $request->all();
        if ($request->input('button_type') == 'delete') {
            if ($request->input('id')) {
                $ResourceImage = new ResourceImage();
                $Image = $ResourceImage->find($request->input('id'));
                $file_path = public_path() . '/category_images/' . $Image->image1;
                if (isset($Image->image1) && file_exists(@$file_path)) {
                    unlink($file_path);
                }
                $file_path = public_path() . '/category_images/' . @$Image->image2;
                if (isset($Image->image2) && file_exists(@$file_path)) {
                    unlink($file_path);
                }
                $Image->delete();

                return back()->with('success', 'Resource removed successfully.');
            } else {
                return back()->with('danger', 'Requested id not found.');
            }
        } elseif ($request->input('button_type') == 'edit') {
            $allresources = ResourceImage::getData();
            // $Image = $ResourceImage->find($request->input('id'));
            $old = $request->old('cat_id');
            $allCategoriesDropdown = Category::attr(['name' => 'cat_id', 'class' => 'form-control'])->selected($old ? $old : 1)->renderAsDropdown();

            return view('resourceimg.edit', compact('allCategoriesDropdown', 'allresources'));
        }

        return back()->with('danger', 'Requested params not found.');
    }

    public function imagesResource($id)
    {
        $ResourceImage = new ResourceImage();
        $allresources = $ResourceImage->find($id);
        $title = '';
        if ($allresources) {
            $categories = ResourceCategory::where('id', '=', $allresources->cat_id)->get()->first();
            $parent_id = $categories->parent_id;
            $id = $categories->id;
            if ($parent_id == 0) {
                $title = $categories->title;
            } else {
                $titlestr = [];
                while ($parent_id != 0) {
                    $categories = ResourceCategory::where('id', '=', $id)->get()->first();
                    $titlestr[] = $categories->title;
                    $id = $parent_id = $categories->parent_id;
                }
                krsort($titlestr);
                $title = implode(' >> ', $titlestr);
            }
            $url = $allresources->url;
            $description = $allresources->description;

            return view('resourceimg.images', compact('allresources', 'title', 'url', 'description'));
        } else {
            return redirect()->route('resourceimg.index');
        }
    }

    /**
     * This method used for show image in resourceimg page
     */
    public function showImagesResource(Request $request)
    {
        $ResourceImage = new ResourceImage();
        $allresources = $ResourceImage->find($request->id);
        $title = '';
        if ($allresources) {
            if ($request->ajax()) {
                return response()->json([
                    'html' => view('resourceimg.partials.modal-show-images', compact('allresources'))->render(),
                ], 200);
            }
        } else {
            return redirect()->route('resourceimg.index');
        }
    }

    /**
     * @SWG\Post(
     *   path="/values-as-per-category",
     *   tags={"Documents"},
     *   summary="post Documents values as per category",
     *   operationId="get-document-per-category",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="mytest",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     * )
     */
    public function getSubCategoryByCategory(Request $request)
    {
        $sub = ResourceCategory::where('parent_id', $request->selected)->get();

        $output = '';

        foreach ($sub as $sub_cat) {
            $output .= '<option value="' . $sub_cat['id'] . '">' . $sub_cat['title'] . '</option>';
        }
        echo $output;
    }

    public function pending(Request $request)
    {
        $old = $request->old('parent_id');
        $Categories = ResourceCategory::attr(['name' => 'parent_id', 'class' => 'form-control'])
            ->selected($old ? $old : 1)
            ->renderAsDropdown();
        $categories = ResourceCategory::where('parent_id', '=', 0)->get();
        $query = ResourceImage::where('is_pending', '=', 1);
        if ($request->id) {
            $query = $query->where('id', $request->id);
        }
        if ($request->term) {
            $query = $query->where('url', 'LIKE', '%' . $request->term . '%')
                ->orWhere('created_at', 'LIKE', '%' . $request->term . '%')
                ->orWhere('updated_at', 'LIKE', '%' . $request->term . '%');
        }
        $allresources = $query->orderby('id', 'desc')->paginate(15)->appends(request()->except(['page']));
        if ($request->ajax()) {
            Log::info('enter in ajax');

            return response()->json([
                'tbody' => view('resourceimg.partial_pending', compact('allresources', 'Categories', 'categories'))->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string) $allresources->render(),
                'count' => $allresources->total(),
            ], 200);
        } else {
            return view('resourceimg.pending', compact('Categories', 'categories', 'allresources'))
                ->with('i', ($request->input('page', 1) - 1) * 5);
        }
    }

    public function activateResourceCat(Request $request)
    {
        $ids = $request->id;
        foreach ($ids as $id) {
            $resourceImage = ResourceImage::findorfail($id);
            $resourceImage->is_pending = 0;
            $resourceImage->created_by = Auth::user()->name;
            $resourceImage->update();
        }

        return back()->with('success', 'New Resource image added successfully.');
    }
}
