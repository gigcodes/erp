<?php

namespace Modules\BookStack\Http\Controllers;

use Illuminate\Http\Request;
use Modules\BookStack\Uploads\Attachment;
use Modules\BookStack\Entities\Repos\EntityRepo;
use Modules\BookStack\Uploads\AttachmentService;
use Modules\BookStack\Exceptions\NotFoundException;
use Modules\BookStack\Exceptions\FileUploadException;

class AttachmentController extends Controller
{
    protected $attachmentService;

    protected $attachment;

    protected $entityRepo;

    /**
     * AttachmentController constructor.
     *
     * @param \BookStack\Uploads\AttachmentService $attachmentService
     */
    public function __construct(AttachmentService $attachmentService, Attachment $attachment, EntityRepo $entityRepo)
    {
        $this->attachmentService = $attachmentService;
        $this->attachment        = $attachment;
        $this->entityRepo        = $entityRepo;
        parent::__construct();
    }

    /**
     * Endpoint at which attachments are uploaded to.
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function upload(Request $request)
    {
        $this->validate($request, [
            'uploaded_to' => 'required|integer|exists:pages,id',
            'file'        => 'required|file',
        ]);

        $pageId = $request->get('uploaded_to');
        $page   = $this->entityRepo->getById('page', $pageId, true);

        $this->checkPermission('attachment-create-all');
        $this->checkOwnablePermission('page-update', $page);

        $uploadedFile = $request->file('file');

        try {
            $attachment = $this->attachmentService->saveNewUpload($uploadedFile, $pageId);
        } catch (FileUploadException $e) {
            return response($e->getMessage(), 500);
        }

        return response()->json($attachment);
    }

    /**
     * Update an uploaded attachment.
     *
     * @param int $attachmentId
     *
     * @return mixed
     */
    public function uploadUpdate($attachmentId, Request $request)
    {
        $this->validate($request, [
            'uploaded_to' => 'required|integer|exists:pages,id',
            'file'        => 'required|file',
        ]);

        $pageId     = $request->get('uploaded_to');
        $page       = $this->entityRepo->getById('page', $pageId, true);
        $attachment = $this->attachment->findOrFail($attachmentId);

        $this->checkOwnablePermission('page-update', $page);
        $this->checkOwnablePermission('attachment-create', $attachment);

        if (intval($pageId) !== intval($attachment->uploaded_to)) {
            return $this->jsonError(trans('errors.attachment_page_mismatch'));
        }

        $uploadedFile = $request->file('file');

        try {
            $attachment = $this->attachmentService->saveUpdatedUpload($uploadedFile, $attachment);
        } catch (FileUploadException $e) {
            return response($e->getMessage(), 500);
        }

        return response()->json($attachment);
    }

    /**
     * Update the details of an existing file.
     *
     * @param mixed $attachmentId
     *
     * @return Attachment|mixed
     */
    public function update($attachmentId, Request $request)
    {
        $this->validate($request, [
            'uploaded_to' => 'required|integer|exists:pages,id',
            'name'        => 'required|string|min:1|max:255',
            'link'        => 'string|min:1|max:255',
        ]);

        $pageId     = $request->get('uploaded_to');
        $page       = $this->entityRepo->getById('page', $pageId, true);
        $attachment = $this->attachment->findOrFail($attachmentId);

        $this->checkOwnablePermission('page-update', $page);
        $this->checkOwnablePermission('attachment-create', $attachment);

        if (intval($pageId) !== intval($attachment->uploaded_to)) {
            return $this->jsonError(trans('errors.attachment_page_mismatch'));
        }

        $attachment = $this->attachmentService->updateFile($attachment, $request->all());

        return response()->json($attachment);
    }

    /**
     * Attach a link to a page.
     *
     * @return mixed
     */
    public function attachLink(Request $request)
    {
        $this->validate($request, [
            'uploaded_to' => 'required|integer|exists:pages,id',
            'name'        => 'required|string|min:1|max:255',
            'link'        => 'required|string|min:1|max:255',
        ]);

        $pageId = $request->get('uploaded_to');
        $page   = $this->entityRepo->getById('page', $pageId, true);

        $this->checkPermission('attachment-create-all');
        $this->checkOwnablePermission('page-update', $page);

        $attachmentName = $request->get('name');
        $link           = $request->get('link');
        $attachment     = $this->attachmentService->saveNewFromLink($attachmentName, $link, $pageId);

        return response()->json($attachment);
    }

    /**
     * Get the attachments for a specific page.
     *
     * @param mixed $pageId
     *
     * @return mixed
     */
    public function listForPage($pageId)
    {
        $page = $this->entityRepo->getById('page', $pageId, true);
        $this->checkOwnablePermission('page-view', $page);

        return response()->json($page->attachments);
    }

    /**
     * Update the attachment sorting.
     *
     * @param mixed $pageId
     *
     * @return mixed
     */
    public function sortForPage($pageId, Request $request)
    {
        $this->validate($request, [
            'files'      => 'required|array',
            'files.*.id' => 'required|integer',
        ]);
        $page = $this->entityRepo->getById('page', $pageId);
        $this->checkOwnablePermission('page-update', $page);

        $attachments = $request->get('files');
        $this->attachmentService->updateFileOrderWithinPage($attachments, $pageId);

        return response()->json(['message' => trans('bookstack::entities.attachments_order_updated')]);
    }

    /**
     * Get an attachment from storage.
     *
     *
     * @param mixed $attachmentId
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws NotFoundException
     */
    public function get($attachmentId)
    {
        $attachment = $this->attachment->findOrFail($attachmentId);
        $page       = $this->entityRepo->getById('page', $attachment->uploaded_to);
        if ($page === null) {
            throw new NotFoundException(trans('errors.attachment_not_found'));
        }

        $this->checkOwnablePermission('page-view', $page);

        if ($attachment->external) {
            return redirect($attachment->path);
        }

        $attachmentContents = $this->attachmentService->getAttachmentFromStorage($attachment);

        return $this->downloadResponse($attachmentContents, $attachment->getFileName());
    }

    /**
     * Delete a specific attachment in the system.
     *
     *
     * @param mixed $attachmentId
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function delete($attachmentId)
    {
        $attachment = $this->attachment->findOrFail($attachmentId);
        $this->checkOwnablePermission('attachment-delete', $attachment);
        $this->attachmentService->deleteFile($attachment);

        return response()->json(['message' => trans('bookstack::entities.attachments_deleted')]);
    }
}
