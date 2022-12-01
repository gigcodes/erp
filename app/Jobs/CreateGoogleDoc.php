<?php

namespace App\Jobs;

use App\GoogleDoc;
use Exception;
use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CreateGoogleDoc
{
    use Dispatchable, SerializesModels;

    private $googleDoc;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(GoogleDoc $googleDoc)
    {
        $this->googleDoc = $googleDoc;
    }

    /**
     * Execute the job.
     * Sample doc link
     * https://docs.google.com/document/d/1O2nIeK9SOjn6ZKujfHdTkHacHnscjRKOG9G2OOiGaPU/edit
     *
     * @return void
     */
    public function handle()
    {
        $client = new Client();
        $client->useApplicationDefaultCredentials();
        $client->addScope(Drive::DRIVE);
        try {
            $createFile = $this->createDriveFile(env('GOOGLE_SHARED_FOLDER'));
            $spreadsheetId = $createFile->id;

            $this->googleDoc->docId = $spreadsheetId;
            $this->googleDoc->save();
        } catch (Exception $e) {
            echo 'Message: '.$e->getMessage();
            dd($e);
        }
    }

    public function moveFileToFolder($fileId, $folderId)
    {
        try {
            $client = new Client();
            $client->useApplicationDefaultCredentials();
            $client->addScope(Drive::DRIVE);
            $driveService = new Drive($client);
            $emptyFileMetadata = new DriveFile();
            // Retrieve the existing parents to remove
            $file = $driveService->files->get($fileId, ['fields' => 'parents']);

            $previousParents = implode(',', $file->parents);

            // Move the file to the new folder
            $file = $driveService->files->update($fileId, $emptyFileMetadata, [
                'addParents' => $folderId,
                'removeParents' => $previousParents,
                'fields' => 'id, parents', ]);

            return $file->parents;
        } catch (Exception $e) {
            echo 'Error Message: '.$e;
        }
    }

    public function createDriveFile($folderId)
    {
        try {
            $client = new Client();
            $client->useApplicationDefaultCredentials();
            $client->addScope(Drive::DRIVE);
            $driveService = new Drive($client);
            $fileMetadata = new Drive\DriveFile([
                'name' => $this->googleDoc->name,
                'parents' => [$folderId],
                'mimeType' => 'application/vnd.google-apps.document',
            ]);

            $file = $driveService->files->create($fileMetadata, [
                'fields' => 'id,parents,mimeType',
            ]);

            return $file;
        } catch (Exception $e) {
            echo 'Error Message: '.$e;
            dd($e);
        }
    }
}
