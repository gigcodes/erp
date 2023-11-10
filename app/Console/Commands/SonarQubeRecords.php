<?php

namespace App\Console\Commands;

use App\Models\SonarQube;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SonarQubeRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insert-sonar-qube';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sonar Qube Records';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            Log::info('Start Sonar Qube');
            $url = env('SONARQUBE_URL') . 'api/issues/search';
            $queryParams = [];

            $username = env('SONARQUBE_USERNAME');
            $password = env('SONARQUBE_PASSWORD');

            $response = Http::withBasicAuth($username, $password)
                ->get($url, $queryParams);

            $responseData = $response->json();

            if (isset($responseData['total'])) {
                $total = (int) $responseData['total'];

                Log::info($total);

                if ($total > 0) {
                    $pageSize = 100;
                    $counter = 1;

                    while ($counter <= ceil($total / $pageSize)) {

                        Log::info($counter);

                        /*$url = "api/issues/search?ps=100&p=$counter";
                        Log::info($url);*/

                        $queryParams = [
                            'ps' => $pageSize,
                            'p' => $counter
                        ];

                        $responseDatasub = Http::withBasicAuth($username, $password)
                            ->get($url, $queryParams);

                        $responseDataPage = $responseDatasub->json();

                        if (!empty($responseDataPage['issues'])) {
                            foreach ($responseDataPage['issues'] as $value) {
                                
                                Log::info($value['key']);

                                $input = [
                                    'key' => $value['key'],
                                    'rule' => $value['rule'],
                                    'severity' => $value['severity'],
                                    'component' => $value['component'],
                                    'project' => $value['project'],
                                    'hash' => $value['hash'],
                                    'textRange' => json_encode($value['textRange']),
                                    'flows' => json_encode($value['flows']),
                                    'resolution' => $value['resolution'],
                                    'status' => $value['status'],
                                    'message' => $value['message'],
                                    'effort' => $value['effort'],
                                    'debt' => $value['debt'],
                                    'author' => $value['author'],
                                    'tags' => json_encode($value['tags']),
                                    'creationDate' => $value['creationDate'],
                                    'updateDate' => $value['updateDate'],
                                    'closeDate' => $value['closeDate'],
                                    'type' => $value['type'],
                                    'scope' => $value['scope'],
                                    'quickFixAvailable' => $value['quickFixAvailable'],
                                    'messageFormattings' => json_encode($value['messageFormattings']),
                                    'codeVariants' => json_encode($value['codeVariants']), 
                                ];

                                $SonarQubeRecords = SonarQube::where('key', $value['key'])->first();
                                if(!empty($SonarQubeRecords)){
                                    SonarQube::find($SonarQubeRecords->id)->update($input);
                                } else{
                                    SonarQube::create($input);
                                }
                            }
                        }
                        $counter++;
                    }
                } else {
                    echo "No issues found with the specified parameters.\n";
                }
            } else {
                echo "Failed to get the total number of issues from SonarQube.\n";
            }

            Log::info('End Sonar Qube');
            
        } catch (\Exception $e) {
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
