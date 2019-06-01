<?php

namespace App\Console\Commands;

use App\ColdLeads;
use App\PeopleNames;
use Illuminate\Console\Command;

class ClassifyGendersInColdLeads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cold-leads:classify-genders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $coldLeads = ColdLeads::where('is_gender_processed', 0)->take(5000)->get();

        foreach ($coldLeads as $key=>$coldLead) {
            echo "$key \n";
            $coldLead->gender = 'm';

            $gender = PeopleNames::whereRaw("INSTR('$coldLead->username', `name`) > 0")->orWhereRaw("INSTR('$coldLead->name', `name`) > 0")->where('name', '!=', '')->first();
            if ($gender) {
                $gender = $gender->gender;
                $coldLead->gender = $gender;
            }

            $coldLead->is_gender_processed = 1;
            $coldLead->save();
        }
    }
}
