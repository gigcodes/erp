<?php

namespace Modules\BookStack\Http\Controllers;

use Views;
use Activity;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\BookStack\Auth\UserRepo;
use Modules\BookStack\Entities\ExportService;
use Modules\BookStack\Entities\Repos\EntityRepo;

class ChapterController extends Controller
{
    protected $userRepo;

    protected $entityRepo;

    protected $exportService;

    /**
     * ChapterController constructor.
     *
     * @param \BookStack\Entities\ExportService $exportService
     */
    public function __construct(EntityRepo $entityRepo, UserRepo $userRepo, ExportService $exportService)
    {
        $this->entityRepo    = $entityRepo;
        $this->userRepo      = $userRepo;
        $this->exportService = $exportService;
        parent::__construct();
    }

    /**
     * Show the form for creating a new chapter.
     *
     * @param mixed $bookSlug
     *
     * @return Response
     */
    public function create($bookSlug)
    {
        $book = $this->entityRepo->getBySlug('book', $bookSlug);
        $this->checkOwnablePermission('chapter-create', $book);
        $this->setPageTitle(trans('bookstack::entities.chapters_create'));

        return view('bookstack::chapters.create', ['book' => $book, 'current' => $book]);
    }

    /**
     * Store a newly created chapter in storage.
     *
     * @param mixed $bookSlug
     *
     * @return Response
     */
    public function store($bookSlug, Request $request)
    {
        $this->validate($request, [
            'name'        => 'required|string|max:255',
            'description' => 'string|max:1000',
        ]);

        $book = $this->entityRepo->getBySlug('book', $bookSlug);
        $this->checkOwnablePermission('chapter-create', $book);

        $input             = $request->all();
        $input['priority'] = $this->entityRepo->getNewBookPriority($book);
        $chapter           = $this->entityRepo->createFromInput('chapter', $input, $book);
        Activity::add($chapter, 'chapter_create', $book->id);

        return redirect($chapter->getUrl());
    }

    /**
     * Display the specified chapter.
     *
     * @param mixed $bookSlug
     * @param mixed $chapterSlug
     *
     * @return Response
     */
    public function show($bookSlug, $chapterSlug)
    {
        $chapter = $this->entityRepo->getBySlug('chapter', $chapterSlug, $bookSlug);
        $this->checkOwnablePermission('chapter-view', $chapter);
        $sidebarTree = $this->entityRepo->getBookChildren($chapter->book);
        Views::add($chapter);
        $this->setPageTitle($chapter->getShortName());
        $pages = $this->entityRepo->getChapterChildren($chapter);

        return view('bookstack::chapters.show', [
            'book'        => $chapter->book,
            'chapter'     => $chapter,
            'current'     => $chapter,
            'sidebarTree' => $sidebarTree,
            'pages'       => $pages,
        ]);
    }

    /**
     * Show the form for editing the specified chapter.
     *
     * @param mixed $bookSlug
     * @param mixed $chapterSlug
     *
     * @return Response
     */
    public function edit($bookSlug, $chapterSlug)
    {
        $chapter = $this->entityRepo->getBySlug('chapter', $chapterSlug, $bookSlug);
        $this->checkOwnablePermission('chapter-update', $chapter);
        $this->setPageTitle(trans('bookstack::entities.chapters_edit_named', ['chapterName' => $chapter->getShortName()]));

        return view('bookstack::chapters.edit', ['book' => $chapter->book, 'chapter' => $chapter, 'current' => $chapter]);
    }

    /**
     * Update the specified chapter in storage.
     *
     *
     * @param mixed $bookSlug
     * @param mixed $chapterSlug
     *
     * @return Response
     *
     * @throws \BookStack\Exceptions\NotFoundException
     */
    public function update(Request $request, $bookSlug, $chapterSlug)
    {
        $chapter = $this->entityRepo->getBySlug('chapter', $chapterSlug, $bookSlug);
        $this->checkOwnablePermission('chapter-update', $chapter);

        $this->entityRepo->updateFromInput('chapter', $chapter, $request->all());
        Activity::add($chapter, 'chapter_update', $chapter->book->id);

        return redirect($chapter->getUrl());
    }

    /**
     * Shows the page to confirm deletion of this chapter.
     *
     * @param mixed $bookSlug
     * @param mixed $chapterSlug
     *
     * @return \Illuminate\View\View
     */
    public function showDelete($bookSlug, $chapterSlug)
    {
        $chapter = $this->entityRepo->getBySlug('chapter', $chapterSlug, $bookSlug);
        $this->checkOwnablePermission('chapter-delete', $chapter);
        $this->setPageTitle(trans('bookstack::entities.chapters_delete_named', ['chapterName' => $chapter->getShortName()]));

        return view('bookstack::chapters.delete', ['book' => $chapter->book, 'chapter' => $chapter, 'current' => $chapter]);
    }

    /**
     * Remove the specified chapter from storage.
     *
     * @param mixed $bookSlug
     * @param mixed $chapterSlug
     *
     * @return Response
     */
    public function destroy($bookSlug, $chapterSlug)
    {
        $chapter = $this->entityRepo->getBySlug('chapter', $chapterSlug, $bookSlug);
        $book    = $chapter->book;
        $this->checkOwnablePermission('chapter-delete', $chapter);
        Activity::addMessage('chapter_delete', $book->id, $chapter->name);
        $this->entityRepo->destroyChapter($chapter);

        return redirect($book->getUrl());
    }

    /**
     * Show the page for moving a chapter.
     *
     *
     * @param mixed $bookSlug
     * @param mixed $chapterSlug
     *
     * @return mixed
     *
     * @throws \BookStack\Exceptions\NotFoundException
     */
    public function showMove($bookSlug, $chapterSlug)
    {
        $chapter = $this->entityRepo->getBySlug('chapter', $chapterSlug, $bookSlug);
        $this->setPageTitle(trans('bookstack::entities.chapters_move_named', ['chapterName' => $chapter->getShortName()]));
        $this->checkOwnablePermission('chapter-update', $chapter);
        $this->checkOwnablePermission('chapter-delete', $chapter);

        return view('bookstack::chapters.move', [
            'chapter' => $chapter,
            'book'    => $chapter->book,
        ]);
    }

    /**
     * Perform the move action for a chapter.
     *
     *
     * @param mixed $bookSlug
     * @param mixed $chapterSlug
     *
     * @return mixed
     *
     * @throws \BookStack\Exceptions\NotFoundException
     */
    public function move($bookSlug, $chapterSlug, Request $request)
    {
        $chapter = $this->entityRepo->getBySlug('chapter', $chapterSlug, $bookSlug);
        $this->checkOwnablePermission('chapter-update', $chapter);
        $this->checkOwnablePermission('chapter-delete', $chapter);

        $entitySelection = $request->get('entity_selection', null);
        if ($entitySelection === null || $entitySelection === '') {
            return redirect($chapter->getUrl());
        }

        $stringExploded = explode(':', $entitySelection);
        $entityType     = $stringExploded[0];
        $entityId       = intval($stringExploded[1]);

        $parent = false;

        if ($entityType == 'book') {
            $parent = $this->entityRepo->getById('book', $entityId);
        }

        if ($parent === false || $parent === null) {
            session()->flash('error', trans('bookstack::errors.selected_book_not_found'));

            return redirect()->back();
        }

        $this->entityRepo->changeBook('chapter', $parent->id, $chapter, true);
        Activity::add($chapter, 'chapter_move', $chapter->book->id);
        session()->flash('success', trans('bookstack::entities.chapter_move_success', ['bookName' => $parent->name]));

        return redirect($chapter->getUrl());
    }

    /**
     * Show the Restrictions view.
     *
     *
     * @param mixed $bookSlug
     * @param mixed $chapterSlug
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * @throws \BookStack\Exceptions\NotFoundException
     */
    public function showPermissions($bookSlug, $chapterSlug)
    {
        $chapter = $this->entityRepo->getBySlug('chapter', $chapterSlug, $bookSlug);
        $this->checkOwnablePermission('restrictions-manage', $chapter);
        $roles = $this->userRepo->getRestrictableRoles();

        return view('bookstack::chapters.permissions', [
            'chapter' => $chapter,
            'roles'   => $roles,
        ]);
    }

    /**
     * Set the restrictions for this chapter.
     *
     *
     * @param mixed $bookSlug
     * @param mixed $chapterSlug
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     *
     * @throws \BookStack\Exceptions\NotFoundException
     * @throws \Throwable
     */
    public function permissions($bookSlug, $chapterSlug, Request $request)
    {
        $chapter = $this->entityRepo->getBySlug('chapter', $chapterSlug, $bookSlug);
        $this->checkOwnablePermission('restrictions-manage', $chapter);
        $this->entityRepo->updateEntityPermissionsFromRequest($request, $chapter);
        session()->flash('success', trans('bookstack::entities.chapters_permissions_success'));

        return redirect($chapter->getUrl());
    }

    /**
     * Exports a chapter to pdf .
     *
     * @param string $bookSlug
     * @param string $chapterSlug
     *
     * @return \Illuminate\Http\Response
     */
    public function exportPdf($bookSlug, $chapterSlug)
    {
        $chapter    = $this->entityRepo->getBySlug('chapter', $chapterSlug, $bookSlug);
        $pdfContent = $this->exportService->chapterToPdf($chapter);

        return $this->downloadResponse($pdfContent, $chapterSlug . '.pdf');
    }

    /**
     * Export a chapter to a self-contained HTML file.
     *
     * @param string $bookSlug
     * @param string $chapterSlug
     *
     * @return \Illuminate\Http\Response
     */
    public function exportHtml($bookSlug, $chapterSlug)
    {
        $chapter       = $this->entityRepo->getBySlug('chapter', $chapterSlug, $bookSlug);
        $containedHtml = $this->exportService->chapterToContainedHtml($chapter);

        return $this->downloadResponse($containedHtml, $chapterSlug . '.html');
    }

    /**
     * Export a chapter to a simple plaintext .txt file.
     *
     * @param string $bookSlug
     * @param string $chapterSlug
     *
     * @return \Illuminate\Http\Response
     */
    public function exportPlainText($bookSlug, $chapterSlug)
    {
        $chapter     = $this->entityRepo->getBySlug('chapter', $chapterSlug, $bookSlug);
        $chapterText = $this->exportService->chapterToPlainText($chapter);

        return $this->downloadResponse($chapterText, $chapterSlug . '.txt');
    }
}
