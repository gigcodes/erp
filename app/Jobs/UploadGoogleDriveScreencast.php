<?php

namespace App\Jobs;

use App\GoogleScreencast;
use Exception;
use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UploadGoogleDriveScreencast
{
    use Dispatchable, SerializesModels;

    private $googleScreencast;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(GoogleScreencast $googleScreencast, $uploadedFile)
    {
        $this->googleScreencast = $googleScreencast;
        $this->uploadedFile = $uploadedFile;
    }

    /**
     * Execute the job.
     * Sample file link
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
            $createFile = $this->uploadScreencast(env('GOOGLE_SCREENCAST_FOLDER'));
            $screencastId = $createFile->id;

            $this->googleScreencast->google_drive_file_id = $screencastId;
            $this->googleScreencast->save();
        } catch (Exception $e) {
            echo 'Message: '.$e->getMessage();
            dd($e);
        }
    }

    public function uploadScreencast($folderId)
    {
        try {
            $client = new Client();
            $client->useApplicationDefaultCredentials();
            $client->addScope(Drive::DRIVE);
            $driveService = new Drive($client);
            $fileMetadata = new Drive\DriveFile(array(
            'name' => $this->uploadedFile->getClientOriginalName(),
            'parents' => [$folderId],
            ));
            $content = file_get_contents($this->uploadedFile->getRealPath());
            $file = $driveService->files->create($fileMetadata, array(
                'data' => $content,
                'mimeType' => $this->uploadedFile->getClientMimeType(),
                'uploadType' => 'multipart',
                'fields' => 'id,parents,mimeType'));
            return $file;
        } catch(Exception $e) {
            echo "Error Message: ".$e;
        } 

    }
    
}
