<?php

namespace App\Console\Commands;

use DB;
use App\User;
use Exception;
use App\UserRate;
use App\Helpers\LogHelper;
use Illuminate\Console\Command;
use App\Hubstaff\HubstaffActivity;
use App\Hubstaff\HubstaffPaymentAccount;

class AccountHubstaffActivities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hubstaff:account';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Accounts for the hubstaff activity in terms of payments';

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
        //
        try {
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Cron was started to run']);

            DB::beginTransaction();
            $firstUnaccountedActivity = HubstaffActivity::orderBy('starts_at')->first();
            if (! $firstUnaccountedActivity) {
                LogHelper::createCustomLogForCron($this->signature, ['message' => 'No found any first unaccounted activity']);

                return;
            }

            // UTC midnight
            $today = strtotime('today+00:00');

            $firstUnaccountActivityTime = strtotime($firstUnaccountedActivity->starts_at . ' UTC') . PHP_EOL;

            echo $today . PHP_EOL;
            echo $firstUnaccountActivityTime . PHP_EOL;

            // account only previous days activity
            if ($firstUnaccountActivityTime < $today) {
                // accounting periods
                $start   = $firstUnaccountedActivity->starts_at; // inclusive
                $endTime = strtotime($start) + (1 * 24 * 60 * 60);
                $end     = date('Y-m-d', $endTime) . ' 23:59:59'; //exclusive

                echo $start . PHP_EOL;
                echo $end . PHP_EOL;

                //get the rate for the start of yesterday
                $userRatesForStartOfDayYesterday = UserRate::latestRatesBeforeTime($end);
                $rateChangesForYesterday         = UserRate::rateChangesForDate($start, $end);
                $activities                      = HubstaffActivity::getActivitiesBetween($start, $end);

                LogHelper::createCustomLogForCron($this->signature, ['message' => 'Getting user rate & hubstuff activities']);

                $userId = [];
                if (! empty($activities)) {
                    foreach ($activities as $acts) {
                        if ($acts->system_user_id > 0) {
                            $userId[] = $acts->system_user_id;
                        }
                    }
                }

                $users = User::whereIn('id', array_unique($userId))->get();

                LogHelper::createCustomLogForCron($this->signature, ['message' => 'Getting all the hubstuff users']);

                // store accounting records for the calculation here
                // user
                //
                $accountingEntries = [];

                foreach ($users as $user) {
                    $accountingEntry = [
                        'user'          => $user->id,
                        'accountedTime' => $end,
                        'activityIds'   => [],
                        'amount'        => 0,
                        'hrs'           => 0,
                        'tasks'         => [],
                    ];

                    $user->total = 0;

                    $activityIds = [];

                    $invidualRatesStartOfDayYesterday = $userRatesForStartOfDayYesterday->first(function ($value, $key) use ($user) {
                        return $value->user_id == $user->id;
                    });

                    $rates = [];

                    if ($invidualRatesStartOfDayYesterday) {
                        $rates[] = [
                            'start_date' => $start,
                            'rate'       => $invidualRatesStartOfDayYesterday->hourly_rate,
                            'currency'   => $invidualRatesStartOfDayYesterday->currency,
                        ];
                    }

                    $rateChangesYesterdayForUser = $rateChangesForYesterday->filter(function ($value, $key) use ($user) {
                        return $value->user_id == $user->id;
                    });

                    if ($rateChangesYesterdayForUser) {
                        foreach ($rateChangesYesterdayForUser as $rate) {
                            $rates[] = [
                                'start_date' => $rate->start_date,
                                'rate'       => $rate->hourly_rate,
                                'currency'   => $rate->currency,
                            ];
                        }
                    }

                    usort($rates, function ($a, $b) {
                        return strtotime($a['start_date']) - strtotime($b['start_date']);
                    });

                    if (count($rates) > 0) {
                        $lastEntry = $rates[count($rates) - 1];

                        $rates[] = [
                            'start_date' => $end,
                            'rate'       => $lastEntry['rate'],
                            'currency'   => $lastEntry['currency'],
                        ];

                        $user->currency = $lastEntry['currency'];
                    }

                    $userActivities = $activities->filter(function ($value, $key) use ($user) {
                        return $value->system_user_id === $user->id;
                    });

                    if (count($rates) == 0) {
                        // no rates have been set for the user and hence mark them as zero (0) telling accounted
                        $activityIds = $userActivities->map(function ($value) {
                            return $value->id;
                        })->toArray();

                        $accountingEntry['activityIds'] = $activityIds;
                    } else {
                        foreach ($userActivities as $activity) {
                            $accountingEntry['activityIds'][] = $activity->id;

                            if (empty($accountingEntry['tasks'][$activity->task_id])) {
                                $accountingEntry['tasks'][$activity->task_id] = $activity->tracked / 60;
                            } else {
                                $accountingEntry['tasks'][$activity->task_id] += $activity->tracked / 60;
                            }

                            $i = 0;
                            while ($i < count($rates) - 1) {
                                $startRate = $rates[$i];
                                $endRate   = $rates[$i + 1];

                                if ($activity->starts_at >= $startRate['start_date'] && $activity->start_time < $endRate['start_date']) {
                                    // the activity needs calculation for the start rate and hence do it
                                    $earnings = $activity->tracked * ($startRate['rate'] / 60 / 60);

                                    $accountingEntry['amount'] += $earnings;
                                    $accountingEntry['hrs'] += (float) $activity->tracked / 60 / 60;
                                    break;
                                }
                                $i++;
                            }
                        }
                    }

                    $accountingEntries[] = $accountingEntry;
                }

                echo print_r($accountingEntries, true);

                // get the activities not accounted
                $unaccountedActivities = $activities->filter(function ($value) use ($accountingEntries) {
                    $isAccounted = false;
                    foreach ($accountingEntries as $entry) {
                        if (in_array($value->id, $entry['activityIds'])) {
                            $isAccounted = true;
                            break;
                        }
                    }

                    return ! $isAccounted;
                });

                $accountedActivityCount = array_reduce(
                    $accountingEntries,
                    function ($previous, $item) {
                        return $previous + count($item['activityIds']);
                    },
                    0
                );

                echo 'Account activities: ' . $accountedActivityCount . PHP_EOL;
                echo 'Unaccounted activities: ' . count($unaccountedActivities) . PHP_EOL;

                //update the accounted activities with the account entry id
                foreach ($accountingEntries as $entry) {
                    $paymentAccount                   = new HubstaffPaymentAccount;
                    $paymentAccount->user_id          = $entry['user'];
                    $paymentAccount->accounted_at     = $entry['accountedTime'];
                    $paymentAccount->amount           = $entry['amount'];
                    $paymentAccount->hrs              = $entry['hrs'];
                    $paymentAccount->billing_start    = $start;
                    $paymentAccount->billing_end      = $end;
                    $paymentAccount->rate             = (float) $entry['amount'] / $entry['hrs'];
                    $paymentAccount->payment_currency = 'INR';
                    $paymentAccount->total_payout     = ($entry['amount']) * 68;
                    $paymentAccount->ex_rate          = 68;
                    $paymentAccount->save();

                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'Saved hubstaff payment account detail by ID:' . $paymentAccount->id]);

                    foreach ($entry['activityIds'] as $activityId) {
                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'Update hubstaff activity detail by ID:' . $activityId]);

                        HubstaffActivity::where('id', $activityId)
                            ->update([
                                'hubstaff_payment_account_id' => $paymentAccount->id,
                            ]);
                    }
                }

                // once account stored now update the time into db
                if (! empty($accountingEntries)) {
                    foreach ($accountingEntries as $entires) {
                        if (! empty($entires['tasks'])) {
                            foreach ($entires['tasks'] as $taskid => $task) {
                                $developerTask = \App\DeveloperTask::where('hubstaff_task_id', $taskid)->first();
                                if ($developerTask) {
                                    $developerTask->estimate_minutes += $task;
                                    $developerTask->save();

                                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'Update developer task estimation by ID:' . $developerTask->id]);
                                }
                            }
                        }
                    }
                }

                // update un accounted activities so that they can be skipped in next iteration
                foreach ($unaccountedActivities as $activity) {
                    $activity->hubstaff_payment_account_id = -1;
                    $activity->save();
                }
            }
            DB::commit();
            echo PHP_EOL . '=====DONE====' . PHP_EOL;
        } catch (Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            \App\CronJob::insertLastError($this->signature, $e->getMessage());

            echo $e->getMessage();
            DB::rollBack();
            echo PHP_EOL . '=====FAILED====' . PHP_EOL;
        }
    }
}
