<?php

namespace Modules\BookStack\Http\Controllers;

use Views;
use Activity;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\BookStack\Auth\UserRepo;
use Modules\BookStack\Uploads\ImageRepo;
use Modules\BookStack\Entities\Bookshelf;
use Modules\BookStack\Entities\Repos\EntityRepo;
use Modules\BookStack\Entities\EntityContextManager;

class BookshelfController extends Controller
{
    protected $entityRepo;

    protected $userRepo;

    protected $entityContextManager;

    protected $imageRepo;

    /**
     * BookController constructor.
     */
    public function __construct(EntityRepo $entityRepo, UserRepo $userRepo, EntityContextManager $entityContextManager, ImageRepo $imageRepo)
    {
        $this->entityRepo           = $entityRepo;
        $this->userRepo             = $userRepo;
        $this->entityContextManager = $entityContextManager;
        $this->imageRepo            = $imageRepo;
        parent::__construct();
    }

    /**
     * Display a listing of the book.
     *
     * @return Response
     */
    public function index()
    {
        return redirect()->route('searchGrid');
        $view        = setting()->getUser($this->currentUser, 'bookshelves_view_type', config('app.views.bookshelves', 'grid'));
        $sort        = setting()->getUser($this->currentUser, 'bookshelves_sort', 'name');
        $order       = setting()->getUser($this->currentUser, 'bookshelves_sort_order', 'asc');
        $sortOptions = [
            'name'       => trans('bookstack::common.sort_name'),
            'created_at' => trans('bookstack::common.sort_created_at'),
            'updated_at' => trans('bookstack::common.sort_updated_at'),
        ];

        $shelves = $this->entityRepo->getAllPaginated('bookshelf', 18, $sort, $order);
        foreach ($shelves as $shelf) {
            $shelf->books = $this->entityRepo->getBookshelfChildren($shelf);
        }

        $recents = $this->signedIn ? $this->entityRepo->getRecentlyViewed('bookshelf', 4, 0) : false;
        $popular = $this->entityRepo->getPopular('bookshelf', 4, 0);
        $new     = $this->entityRepo->getRecentlyCreated('bookshelf', 4, 0);

        $this->entityContextManager->clearShelfContext();
        $this->setPageTitle(trans('bookstack::entities.shelves'));

        return view('bookstack::shelves.index', [
            'shelves'     => $shelves,
            'recents'     => $recents,
            'popular'     => $popular,
            'new'         => $new,
            'view'        => $view,
            'sort'        => $sort,
            'order'       => $order,
            'sortOptions' => $sortOptions,
        ]);
    }

    /**
     * Show the form for creating a new bookshelf.
     *
     * @return Response
     */
    public function create()
    {
        $this->checkPermission('bookshelf-create-all');
        $books = $this->entityRepo->getAll('book', false, 'update');
        $this->setPageTitle(trans('bookstack::entities.shelves_create'));

        return view('bookstack::shelves.create', ['books' => $books]);
    }

    /**
     * Store a newly created bookshelf in storage.
     *
     * @return Response
     *
     * @throws \BookStack\Exceptions\ImageUploadException
     */
    public function store(Request $request)
    {
        $this->checkPermission('bookshelf-create-all');
        $this->validate($request, [
            'name'        => 'required|string|max:255',
            'description' => 'string|max:1000',
            'image'       => $this->imageRepo->getImageValidationRules(),
        ]);

        $shelf = $this->entityRepo->createFromInput('bookshelf', $request->all());
        $this->shelfUpdateActions($shelf, $request);

        Activity::add($shelf, 'bookshelf_create');

        return redirect($shelf->getUrl());
    }

    /**
     * Display the specified bookshelf.
     *
     * @return Response
     *
     * @throws \BookStack\Exceptions\NotFoundException
     */
    public function show(string $slug)
    {
        /** @var Bookshelf $shelf */
        $shelf = $this->entityRepo->getBySlug('bookshelf', $slug);
        $this->checkOwnablePermission('book-view', $shelf);

        $books = $this->entityRepo->getBookshelfChildren($shelf);
        Views::add($shelf);
        $this->entityContextManager->setShelfContext($shelf->id);

        $this->setPageTitle($shelf->getShortName());

        return view('bookstack::shelves.show', [
            'shelf'    => $shelf,
            'books'    => $books,
            'activity' => Activity::entityActivity($shelf, 20, 1),
        ]);
    }

    public function showShelf($sortByView, $sortByDate)
    {
        $view        = setting()->getUser($this->currentUser, 'bookshelves_view_type', config('app.views.bookshelves', 'grid'));
        $sort        = setting()->getUser($this->currentUser, 'bookshelves_sort', 'name');
        $order       = setting()->getUser($this->currentUser, 'bookshelves_sort_order', 'asc');
        $sortOptions = [
            'name'       => trans('bookstack::common.sort_name'),
            'created_at' => trans('bookstack::common.sort_created_at'),
            'updated_at' => trans('bookstack::common.sort_updated_at'),
        ];

        $shelves = $this->entityRepo->getAllPaginated('bookshelf', 18, $sort, $order);
        foreach ($shelves as $shelf) {
            $shelf->books = $this->entityRepo->getBookshelfChildren($shelf);
        }

        $recents = $this->signedIn ? $this->entityRepo->getRecentlyViewed('bookshelf', 4, 0) : false;
        $popular = $this->entityRepo->getPopular('bookshelf', 4, 0);
        $new     = $this->entityRepo->getRecentlyCreated('bookshelf', 4, 0);

        $this->entityContextManager->clearShelfContext();

        return response()->json([
            'shelves'     => $shelves,
            'recents'     => $recents,
            'popular'     => $popular,
            'new'         => $new,
            'view'        => $view,
            'sort'        => $sort,
            'order'       => $order,
            'sortOptions' => $sortOptions,
        ]);
    }

    /**
     * Show the form for editing the specified bookshelf.
     *
     * @return Response
     *
     * @throws \BookStack\Exceptions\NotFoundException
     */
    public function edit(string $slug)
    {
        $shelf = $this->entityRepo->getBySlug('bookshelf', $slug); /** @var $shelf Bookshelf */
        $this->checkOwnablePermission('bookshelf-update', $shelf);

        $shelfBooks   = $this->entityRepo->getBookshelfChildren($shelf);
        $shelfBookIds = $shelfBooks->pluck('id');
        $books        = $this->entityRepo->getAll('book', false, 'update');
        $books        = $books->filter(function ($book) use ($shelfBookIds) {
            return ! $shelfBookIds->contains($book->id);
        });

        $this->setPageTitle(trans('bookstack::entities.shelves_edit_named', ['name' => $shelf->getShortName()]));

        return view('bookstack::shelves.edit', [
            'shelf'      => $shelf,
            'books'      => $books,
            'shelfBooks' => $shelfBooks,
        ]);
    }

    /**
     * Update the specified bookshelf in storage.
     *
     * @return Response
     *
     * @throws \BookStack\Exceptions\NotFoundException
     * @throws \BookStack\Exceptions\ImageUploadException
     */
    public function update(Request $request, string $slug)
    {
        $shelf = $this->entityRepo->getBySlug('bookshelf', $slug); /** @var $bookshelf Bookshelf */
        $this->checkOwnablePermission('bookshelf-update', $shelf);
        $this->validate($request, [
            'name'        => 'required|string|max:255',
            'description' => 'string|max:1000',
            'image'       => $this->imageRepo->getImageValidationRules(),
        ]);

        $shelf = $this->entityRepo->updateFromInput('bookshelf', $shelf, $request->all());
        $this->shelfUpdateActions($shelf, $request);

        Activity::add($shelf, 'bookshelf_update');

        return redirect($shelf->getUrl());
    }

    /**
     * Shows the page to confirm deletion
     *
     * @return \Illuminate\View\View
     *
     * @throws \BookStack\Exceptions\NotFoundException
     */
    public function showDelete(string $slug)
    {
        $shelf = $this->entityRepo->getBySlug('bookshelf', $slug); /** @var $shelf Bookshelf */
        $this->checkOwnablePermission('bookshelf-delete', $shelf);

        $this->setPageTitle(trans('bookstack::entities.shelves_delete_named', ['name' => $shelf->getShortName()]));

        return view('bookstack::shelves.delete', ['shelf' => $shelf]);
    }

    /**
     * Remove the specified bookshelf from storage.
     *
     * @return Response
     *
     * @throws \BookStack\Exceptions\NotFoundException
     * @throws \Throwable
     */
    public function destroy(string $slug)
    {
        $shelf = $this->entityRepo->getBySlug('bookshelf', $slug); /** @var $shelf Bookshelf */
        $this->checkOwnablePermission('bookshelf-delete', $shelf);
        Activity::addMessage('bookshelf_delete', 0, $shelf->name);

        if ($shelf->cover) {
            $this->imageRepo->destroyImage($shelf->cover);
        }
        $this->entityRepo->destroyBookshelf($shelf);

        return redirect('/kb/shelves');
    }

    /**
     * Show the permissions view.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * @throws \BookStack\Exceptions\NotFoundException
     */
    public function showPermissions(string $slug)
    {
        $shelf = $this->entityRepo->getBySlug('bookshelf', $slug);
        $this->checkOwnablePermission('restrictions-manage', $shelf);

        $roles = $this->userRepo->getRestrictableRoles();

        return view('bookstack::shelves.permissions', [
            'shelf' => $shelf,
            'roles' => $roles,
        ]);
    }

    /**
     * Set the permissions for this bookshelf.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     *
     * @throws \BookStack\Exceptions\NotFoundException
     * @throws \Throwable
     */
    public function permissions(string $slug, Request $request)
    {
        $shelf = $this->entityRepo->getBySlug('bookshelf', $slug);
        $this->checkOwnablePermission('restrictions-manage', $shelf);

        $this->entityRepo->updateEntityPermissionsFromRequest($request, $shelf);
        session()->flash('success', trans('bookstack::entities.shelves_permissions_updated'));

        return redirect($shelf->getUrl());
    }

    /**
     * Copy the permissions of a bookshelf to the child books.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     *
     * @throws \BookStack\Exceptions\NotFoundException
     */
    public function copyPermissions(string $slug)
    {
        $shelf = $this->entityRepo->getBySlug('bookshelf', $slug);
        $this->checkOwnablePermission('restrictions-manage', $shelf);

        $updateCount = $this->entityRepo->copyBookshelfPermissions($shelf);
        session()->flash('success', trans('bookstack::entities.shelves_copy_permission_success', ['count' => $updateCount]));

        return redirect($shelf->getUrl());
    }

    /**
     * Common actions to run on bookshelf update.
     *
     *
     * @throws \BookStack\Exceptions\ImageUploadException
     */
    protected function shelfUpdateActions(Bookshelf $shelf, Request $request)
    {
        // Update the books that the shelf references
        $this->entityRepo->updateShelfBooks($shelf, ($request->get('books', '')) ? $request->get('books', '') : '');

        // Update the cover image if in request
        if ($request->has('image')) {
            $newImage = $request->file('image');
            $this->imageRepo->destroyImage($shelf->cover);
            $image           = $this->imageRepo->saveNew($newImage, 'cover_shelf', $shelf->id, 512, 512, true);
            $shelf->image_id = $image->id;
            $shelf->save();
        }

        if ($request->has('image_reset')) {
            $this->imageRepo->destroyImage($shelf->cover);
            $shelf->image_id = 0;
            $shelf->save();
        }
    }
}
