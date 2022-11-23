<?php

namespace App\Jobs;

use App\GoogleDoc;
use Exception;
use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateGoogleSpreadsheet implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
     *
     * @return void
     */
    public function handle()
    {
        $client = new Client();
        $client->useApplicationDefaultCredentials();
        $client->addScope(Drive::DRIVE);
//        $service = new \Google_Service_Sheets($client);
        try {
            // @todo add DB transaction
            // @todo based on mimet type create file.
            $createFile = $this->createDriveFile(env('GOOGLE_SHARED_FOLDER'));
            $spreadsheetId = $createFile->id;

//            $spreadsheet = new Google_Service_Sheets_Spreadsheet([
//                'properties' => [
//                    'title' => $this->googleDoc->name
//                ]
//            ]);

//            $spreadsheet = $service->spreadsheets->create($spreadsheet,[
//                'fields' => 'spreadsheetId,properties'
//            ]);
//
//            // https://docs.google.com/spreadsheets/d/1jJAHaCJfTfqNuMQrbs9Uz-x39XxDd2PdFGZUIcJ2SKg/edit
//            // 1OJ-vkF5y7PZ4NGKs8SuT3-wbHqkggWOtosOu8OkkyVQ
//            dd([$spreadsheet, get_class_methods($spreadsheet), $spreadsheet->getProperties()]);
//            $spreadsheetId = $spreadsheet->spreadsheetId;


            $this->googleDoc->docId = $spreadsheetId;
            $this->googleDoc->save();
//            dump(['$spreadsheet->spreadsheetId', $spreadsheetId]);
//            $file = $this->moveFileToFolder($spreadsheetId, '1vbPlxUqg0a7edhgZQ60ZaD9mm3NA3w0c');
//            dd(['file and spreadsheet',$file, $spreadsheet]);

//            return $spreadsheet->spreadsheetId;
        } catch (Exception $e) {
            // TODO(developer) - handle error appropriately
            echo 'Message: ' . $e->getMessage();

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
            $file = $driveService->files->get($fileId, array('fields' => 'parents'));
            dump('$file', $file);
            $previousParents = join(',', $file->parents);
            dump('$previousParents', $previousParents);
            // Move the file to the new folder
            $file = $driveService->files->update($fileId, $emptyFileMetadata, array(
                'addParents' => $folderId,
                'removeParents' => $previousParents,
                'fields' => 'id, parents'));
            return $file->parents;
        } catch (Exception $e) {
            echo "Error Message: " . $e;
        }
    }

    // @todo make doc public.
    public function createDriveFile($folderId)
    {
        try {
            $client = new Client();
            $client->useApplicationDefaultCredentials();
            $client->addScope(Drive::DRIVE);
            $driveService = new Drive($client);
            $fileMetadata = new Drive\DriveFile(array(
                'name' => $this->googleDoc->name,
                'parents' => array($folderId),
                "mimeType" => "application/vnd.google-apps.spreadsheet",
            ));

            $file = $driveService->files->create($fileMetadata, array(
                'mimeType' => 'application/vnd.google-apps.spreadsheet',
                'fields' => 'id,parents,mimeType'));

            return $file;
        } catch (Exception $e) {
            echo "Error Message: " . $e;
            dd($e);
        }
    }
}
