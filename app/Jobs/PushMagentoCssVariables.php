<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\LogRequest;
use App\MagentoCssVariableJobLog;

class PushMagentoCssVariables implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $magentoCssVariable;

    public $tries = 5;

    public $backoff = 5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($magentoCssVariable)
    {
        $this->magentoCssVariable = $magentoCssVariable;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::info("PushMagentoCssVariables Queue");
        try {
            // Set time limit
            set_time_limit(0);

            // Load product and website
            $magentoCssVariable = $this->magentoCssVariable;
            $project_name=optional($magentoCssVariable->project)->name;
            $filepath=$magentoCssVariable->file_path;
            $key=$magentoCssVariable->variable;
            $value=$magentoCssVariable->value;

            $cmd = 'bash ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . 'magento-cssvariable-update.sh -p "' . $project_name . '" -f "' . $filepath . '" -k "'.$key. '" -v "'.$value. '" 2>&1';
            \Log::info("Start Magento Css Variable Update Vaule");
            $result = exec($cmd, $output, $return_var);
            \Log::info("command:".$cmd);
            \Log::info("output:".print_r($output,true));
            \Log::info("return_var:".$return_var);
            \Log::info("End Magento Css Variable Update Vaule");
            if(!isset($output[0])){
                // return response()->json(['code' => 500, 'message' => 'The response is not found!']);
                // Maintain Error Log here in new table. 
                MagentoCssVariableJobLog::create([
                    'magento_css_variable_id' => $magentoCssVariable->id,
                    'command' => $cmd,
                    'message' => json_encode($output), 
                    'status' => 'Error', 
                ]);
            }

            $response = json_decode($output[0]);
            if(isset($response->status)  && ($response->status=='true' || $response->status)){
                $message = "Variable updated";
                if(isset($response->message) && $response->message!=''){
                    $message = $response->message;
                }
                // Maintain Success Log here in new table. 
                MagentoCssVariableJobLog::create([
                    'magento_css_variable_id' => $magentoCssVariable->id,
                    'command' => $cmd,
                    'message' => json_encode($output), 
                    'status' => 'Success', 
                ]);
                // return response()->json(['code' => 200, 'message' => $message]);
            }else{
                $message = "Something Went Wrong! Please check Logs for more details";
                if(isset($response->message) && $response->message!=''){
                    $message=$response->message;
                }
                // Maintain Error Log here in new table. 
                MagentoCssVariableJobLog::create([
                    'magento_css_variable_id' => $magentoCssVariable->id,
                    'command' => $cmd,
                    'message' =>  json_encode($output), 
                    'status' => 'Error', 
                ]);
                // return response()->json(['code' => 500, 'message' => $message]);
            }
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }

    public function tags()
    {
        return ['pushMagentoCssVariables', $this->magentoCssVariable->id];
    }
}
