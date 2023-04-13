<?php

namespace App\Jobs;

use App\GoogleDriveBug;
use App\Models\GoogleDriveBugsUpload;
use Exception;
use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UploadGoogleDriveBugs
{
    use Dispatchable, SerializesModels;

    private $googleDriveBug;
    private $uploadedFile;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(GoogleDriveBugsUpload $googleDriveBug, $uploadedFile)
    {
        $this->googleDriveBug = $googleDriveBug;
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
            $createFile = $this->uploadScreencast(env('GOOGLE_SCREENCAST_FOLDER'), $this->googleDriveBug->read, $this->googleDriveBug->write);
            $screencastId = $createFile->id;

            $this->googleDriveBug->google_drive_file_id = $screencastId;
            $this->googleDriveBug->save();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function uploadScreencast($folderId, $googleFileUsersRead, $googleFileUsersWrite)
    {
        // dd($folderId, $googleFileUsersRead, $googleFileUsersWrite);
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
            $index = 1;
            $driveService->getClient()->setUseBatch(true);
            $batch = $driveService->createBatch();

            if(isset($googleFileUsersRead) && $googleFileUsersRead != "") {
                $googleFileUsersRead = explode(',', $googleFileUsersRead);
                foreach ($googleFileUsersRead as $email) {
                    $userPermission = new Drive\Permission([
                        'type' => 'user',
                        'role' => 'reader',
                        'emailAddress' => $email,
                    ]);
    
                    $request = $driveService->permissions->create($file->id, $userPermission, ['fields' => 'id']);
                    $batch->add($request, 'user'.$index);
                    $index++;
                }
                $results = $batch->execute();
            }


            if(isset($googleFileUsersWrite) && $googleFileUsersWrite != "") {
                $batch = $driveService->createBatch();
                $googleFileUsersWrite = explode(',', $googleFileUsersWrite);
    
                foreach ($googleFileUsersWrite as $email) {
                    $userPermission = new Drive\Permission([
                        'type' => 'user',
                        'role' => 'writer',
                        'emailAddress' => $email,
                    ]);
    
                    $request = $driveService->permissions->create($file->id, $userPermission, ['fields' => 'id']);
                    $batch->add($request, 'user'.$index);
                    $index++;
                }
                $results = $batch->execute();
            }
    
            return $file;
        } catch(Exception $e) {
            echo "Error Message: ".$e;
        } 

    }
    
}
