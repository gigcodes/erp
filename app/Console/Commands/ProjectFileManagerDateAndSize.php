<?php

namespace App\Console\Commands;

use DB;
use App\User;
use App\Setting;
use App\ProjectFileManager;
use Illuminate\Console\Command;
use App\ProjectFileManagerHistory;

class ProjectFileManagerDateAndSize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:filemanagementdate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Its For Local Part where we run this on local and send the data to whatsapp and server';

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
        // $row = 0;
        // $arr_id = [];
        // $is_file_exists_size = null;
        // $fileInformation = ProjectFileManager::limit(5)->get();

        // $fileInformation = ProjectFileManager::limit(5)->get();
        $fileInformation = ProjectFileManager::all();
        $param = [];

        foreach ($fileInformation as $key => $val) {
            $path = base_path() . DIRECTORY_SEPARATOR . (str_replace('./', '', $val->name));
            $file_size = 0;
            if (is_dir($path)) {
                if (file_exists($path)) {
                    $old_size = $val->size;

                    $limit_data = Setting::get('project_file_managers');

                    if ($limit_data) {
                        $limit_rec = $limit_data;
                    } else {
                        $limit_rec = 10;
                    }

                    $increase_size = (($old_size * $limit_rec) / 100);

                    $id = $val->id;
                    $name = $val->name;

                    $io = popen('/usr/bin/du -sk ' . $path, 'r');
                    $size = fgets($io, 4096);
                    $new_size = substr($size, 0, strpos($size, "\t"));

                    $new_size = round($new_size, 2);
                    pclose($io);
                    if ($old_size != $new_size) {
                        $updatesize = DB::table('project_file_managers')->where(['id' => $id])->update(['size' => $new_size]);

                        // $param[] = DB::table('project_file_managers_history')->insert([
                        //     ['project_id' => $id,
                        //     'name' => $name,
                        //     'old_size' => $old_size,
                        //     'new_size' => $new_size]
                        //  ]);

                        $param = [
                            'project_id' => $id,
                            'name' => $name,
                            'old_size' => $old_size . 'MB',
                            'new_size' => $new_size . 'MB',
                        ];

                        ProjectFileManagerHistory::create($param);
                    }

                    $both_size = ($old_size + $increase_size);

                    if ($new_size >= $both_size) {
                        $message = 'Project Directory Size increase in Path = ' . $name . ',' . ' OldSize = ' . $old_size . 'MB' . ' And ' . 'NewSize = ' . $new_size . 'MB';

                        $users = User::get();
                        foreach ($users as $user) {
                            if ($user->isAdmin()) {
                                app(\App\Http\Controllers\WhatsAppController::class)->sendWithWhatsApp($user->phone, $user->whatsapp_number, $message);
                                $this->info('message successfully send');
                            }
                        }

                        $updatesize = DB::table('project_file_managers')->where(['id' => $id])->update(['display_dev_master' => 1]);
                    } else {
                        $updatesize = DB::table('project_file_managers')->where(['id' => $id])->update(['display_dev_master' => 0]);
                    }
                }
            } else {
                if (file_exists($path)) {
                    $old_size = $val->size;
                    $limit_data = Setting::get('project_file_managers');

                    if ($limit_data) {
                        $limit_rec = $limit_data;
                    } else {
                        $limit_rec = 10;
                    }

                    $increase_size = (($old_size * $limit_rec) / 100);
                    $id = $val->id;
                    $name = $val->name;

                    $new_size = filesize($path) / 1024;
                    $new_size = round($new_size, 2);

                    if ($old_size != $new_size) {
                        $updatesize = DB::table('project_file_managers')->where(['id' => $id])->update(['size' => $new_size]);
                        // dd($updatesize,22);

                        $param = [
                            'project_id' => $id,
                            'name' => $name,
                            'old_size' => $old_size . 'MB',
                            'new_size' => $new_size . 'MB',
                        ];

                        ProjectFileManagerHistory::create($param);
                    }

                    $both_size = ($old_size + $increase_size);

                    if ($new_size > $both_size) {
                        $message = 'Project Directory Size increase in Path = ' . $name . ',' . ' OldSize = ' . $old_size . 'MB' . ' And ' . 'NewSize = ' . $new_size . 'MB';

                        $users = User::get();
                        foreach ($users as $user) {
                            if ($user->isAdmin()) {
                                app(\App\Http\Controllers\WhatsAppController::class)->sendWithWhatsApp($user->phone, $user->whatsapp_number, $message);
                                $this->info('message successfully send');
                            }
                        }
                        $updatesize = DB::table('project_file_managers')->where(['id' => $id])->update(['display_dev_master' => 1]);
                    } else {
                        $updatesize = DB::table('project_file_managers')->where(['id' => $id])->update(['display_dev_master' => 0]);
                    }

                    if (is_numeric($new_size)) {
                        $size = number_format($new_size / 1024, 2, '.', '');
                    }

                    $fileInformation->size = $new_size;
                }
            }
        }
        $this->info('success');
    }
}
