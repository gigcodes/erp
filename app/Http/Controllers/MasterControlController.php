<?php

namespace App\Http\Controllers;

use App\MessageQueue;
use App\Task;
use App\Helpers;
use App\User;
use App\Instruction;
use App\InstructionCategory;
use App\ReplyCategory;
use App\DeveloperTask;
use App\Order;
use App\Purchase;
use App\Email;
use App\Supplier;
use App\Review;
use App\PushNotification;
use App\CronJob;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MasterControlController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $today = Carbon::now()->format('Y-m-d');
      $yesterday = Carbon::now()->subDay()->format('Y-m-d');
      $two_days_ago = Carbon::now()->subDays(2)->format('Y-m-d');
      $yesterday_day = Carbon::now()->subDay()->format('d');
      $after_week = Carbon::now()->addWeek()->format('Y-m-d');

      $start = $request->range_start ?  "$request->range_start 00:00" : Carbon::now()->subDay()->format('Y-m-d 00:00');
      $end = $request->range_end ? "$request->range_end 23:59" : Carbon::now()->subDay()->format('Y-m-d 23:59');

      // dd($start, $end);

      $message_groups = MessageQueue::whereBetween('sending_time', ["$yesterday 00:00", "$after_week 23:59"])->get()->groupBy([function($query) {
        return Carbon::parse($query->sending_time)->format('Y-m-d');
      }, 'group_id', 'sent', 'status']);

      $new_data = [];
      // $new_data[$today] = [];
      //
      // $month_days = Carbon::now()->subDay()->daysInMonth;
      //
      // for ($i = 1; $i <= 8; $i++) {
      //   $day = $i < 10 ? "0" . $i : $i;
      //
      //   if ($month_days == 1) {
      //     $new_data[$request->sending_time] = [];
      //   } else {
      //     $date = $month_back->format('Y-m-') . $day;
      //
      //     $new_data[$date] = [];
      //   }
      // }

      $message_groups_array = [];
      foreach ($message_groups as $date => $info) {
        $new_data[$date] = [];

        foreach ($info as $group_id => $datas) {
          $sent_count = 0;
          $received_count = 0;
          $stopped_count = 0;
          $total_count = 0;
          foreach ($datas as $sent_status => $data) {

            foreach ($data as $stopped_status => $items) {
              if ($sent_status == 1) {
                $sent_count += count($items);

                foreach ($items as $item) {
                  $received_count += ($item->chat_message && $item->chat_message->sent == 1) ? 1 : 0;
                }
              }

              $total_count += count($items);

              if ($stopped_status == 0) {
                $can_be_stopped = true;
              } else {
                $can_be_stopped = false;
                $stopped_count += count($items);
              }

              $message_groups_array[$group_id]['message'] = json_decode($items[0]->data, true)['message'];
              $message_groups_array[$group_id]['image'] = array_key_exists('image', json_decode($items[0]->data, true)) ? json_decode($items[0]->data, true)['image'] : [];
              $message_groups_array[$group_id]['linked_images'] = array_key_exists('linked_images', json_decode($items[0]->data, true)) ? json_decode($items[0]->data, true)['linked_images'] : [];
              $message_groups_array[$group_id]['can_be_stopped'] = $can_be_stopped;
              $message_groups_array[$group_id]['sending_time'] = $items[0]->sending_time;
              $message_groups_array[$group_id]['whatsapp_number'] = $items[0]->whatsapp_number;
            }

            $message_groups_array[$group_id]['sent'] = $sent_count;
            $message_groups_array[$group_id]['received'] = $received_count;
            $message_groups_array[$group_id]['stopped'] = $stopped_count;
            $message_groups_array[$group_id]['total'] = $total_count;
            $message_groups_array[$group_id]['expecting_time'] = MessageQueue::where('group_id', $group_id)->orderBy('sending_time', 'DESC')->first()->sending_time;
          }

          $new_data[Carbon::parse($message_groups_array[$group_id]['sending_time'])->format('Y-m-d')][$group_id] = $message_groups_array[$group_id];
        }
      }

      ksort($new_data);
      // dd($new_data);

      $tasks = [];
      $userid = Auth::id();
  		$tasks['tasks']      = Task::where('is_statutory', '=', 0)
  										->where(function ($query) use ($userid) {
  											return $query->orWhere('assign_from', '=', $userid)
  											             ->orWhere('assign_to', '=', $userid);
  										})
  		                               ->oldest()->get()->groupBy(['assign_to', function ($query) {
                                       // return $query->is_completed;
                                       if ($query->is_completed != '') {
                                         return 1;
                                       } else {
                                         return 0;
                                       }
                                     }])->toArray();
                                     // dd($tasks['tasks']);

  		// $tasks['completed']  = Task::where( 'is_statutory', '=', 0 )
  		//                                     ->whereNotNull( 'is_completed'  )
  		// 									->where( function ($query ) use ($userid) {
  		// 										return $query->orWhere( 'assign_from', '=', $userid )
  		// 										             ->orWhere( 'assign_to', '=', $userid );
  		// 									})
  		//                                     ->get()->groupBy('assign_to')->toArray();

      // $tasks['last_pending'] = Task::where( 'is_statutory', '=', 0 )
  		//                                     ->whereNull( 'is_completed'  )
  		// 									->where( function ($query ) use ($userid) {
  		// 										return $query->orWhere( 'assign_from', '=', $userid )
  		// 										             ->orWhere( 'assign_to', '=', $userid );
  		// 									})
  		//                                     ->latest()->first()->toArray();

      $users_array = Helpers::getUserArray(User::all());

      $instruction_categories_array = [];
      $instructions_categories = InstructionCategory::all();
      $instructions = Instruction::where('assigned_from', Auth::id())->oldest()->get()->groupBy(['assigned_to', 'category_id', function ($query) {
        if ($query->completed_at != '') {
          return 1;
        } else {
          return 0;
        }
      }])->toArray();

      // dd($instructions);

      foreach ($instructions_categories as $category) {
        $instruction_categories_array[$category->id]['name'] = $category->name;
        $instruction_categories_array[$category->id]['icon'] = $category->icon;
      }

      // dd($instructions);
      // $last_pending_instruction = Instruction::where('assigned_from', Auth::id())->whereNull('completed_at')->first();
      // $completed_instructions = Instruction::where('assigned_from', Auth::id())->whereNotNull('completed_at')->get()->groupBy('assigned_to');

      $developer_tasks = DeveloperTask::oldest()->get()->groupBy(['user_id', function ($query) {
        if ($query->status == 'Done') {
          return '1';
        } else {
          return '0';
        }
      }])->toArray();
      // dd($developer_tasks);
      // $last_pending_developer_task = DeveloperTask::where('status', '!=', 'Done')->first();
      // $completed_developer_tasks = DeveloperTask::where('status', 'Done')->get()->groupBy('user_id');

      $customers = DB::select( '
									SELECT * FROM (SELECT id, name, created_at, is_error_flagged,
                  (SELECT mm1.created_at FROM chat_messages mm1 WHERE mm1.id = chat_message_id) AS last_communicated_at,
                  (SELECT mm2.message FROM chat_messages mm2 WHERE mm2.id = chat_message_id) AS message,
                  (SELECT mm3.status FROM chat_messages mm3 WHERE mm3.id = chat_message_id) AS message_status,
                  (SELECT mm4.id FROM chat_messages mm4 WHERE mm4.id = chat_message_id) AS message_id,
                  (SELECT mm4.sent FROM chat_messages mm4 WHERE mm4.id = chat_message_id) AS message_type
                  FROM customers
                  LEFT JOIN (SELECT MAX(id) AS chat_message_id, chat_messages.customer_id as cmcid, MAX(chat_messages.created_at) as chat_message_created_at, message, status, sent
                     FROM chat_messages
                     WHERE chat_messages.status != 7 AND chat_messages.status != 8 AND chat_messages.status != 9
                     GROUP BY chat_messages.customer_id
                     ORDER BY chat_messages.created_at DESC) AS chat_messages
                  ON customers.id = chat_messages.cmcid
                  ORDER BY chat_message_created_at DESC)
                  AS customers
                  LIMIT 10;
							');

              $unread_messages = DB::select( '
        									SELECT COUNT(CASE message_status WHEN 0 THEN 1 ELSE null END) AS unread, COUNT(CASE message_status WHEN 1 THEN 1 ELSE null END) AS waiting_approval FROM
                          (SELECT
                          (SELECT mm1.created_at FROM chat_messages mm1 WHERE mm1.id = chat_message_id) AS last_communicated_at,
                          (SELECT mm3.status FROM chat_messages mm3 WHERE mm3.id = chat_message_id) AS message_status
                          FROM customers
                          LEFT JOIN (SELECT MAX(id) AS chat_message_id, chat_messages.customer_id as cmcid, MAX(chat_messages.created_at) as chat_message_created_at, message, status, sent
                             FROM chat_messages
                             WHERE chat_messages.status != 7 AND chat_messages.status != 8 AND chat_messages.status != 9
                             GROUP BY chat_messages.customer_id
                             ORDER BY chat_messages.created_at DESC) AS chat_messages
                          ON customers.id = chat_messages.cmcid
                          ORDER BY chat_message_created_at DESC)
                          AS customers;
        							');

      $reply_categories = ReplyCategory::all();

      // $orders = Order::where('order_date', $today)->count();
      $orders_data = Order::whereBetween('order_date', [$start, $end])->get();

      // foreach ($orders as $key => $order) {
        $orders = [];
        $orders['orders'] = $orders_data;
        $orders['cod'] = Order::whereBetween('order_date', [$start, $end])->where('payment_mode', 'cash on delivery')->count();

      // }

      // $orders = DB::select( '
      //             SELECT
      //               COUNT(CASE payment_mode WHEN "cash on delivery" THEN 1 ELSE null END) AS cod_count,
      //               COUNT(CASE message_status WHEN 1 THEN 1 ELSE null END) AS waiting_approval
      //             FROM orders
      //             LIMIT 10;
      //         ');

      // dd($orders['orders'][0]->order_product);
      // $purchases = Purchase::where('created_at', 'LIKE', "%$today%")->count();
      $purchases = (new Purchase())->newQuery()->with(['Products' => function ($query) {
        $query->with(['orderproducts' => function ($quer) {
          $quer->with(['Order' => function ($q) {
            $q->with('customer');
          }]);
        }]);
      }, 'purchase_supplier'])->whereBetween('created_at', [$start, $end])
      ->whereNotIn('status', ['Pending Purchase', 'Request Sent to Supplier', 'Price under Negotiation'])->get()
      ->groupBy('supplier_id')->toArray();

      // dd($purchases);

      $scraped_count = DB::select("
									SELECT website, created_at, COUNT(*) as total FROM
								 		(SELECT scraped_products.website, DATE_FORMAT(scraped_products.created_at, '%Y-%m-%d') as created_at
								  		 FROM scraped_products
								  		 WHERE scraped_products.created_at BETWEEN '$start' AND '$end')
								    AS SUBQUERY
                    GROUP BY website;
							");

      // $scraped_days_ago_count = DB::select( '
			// 						SELECT website, created_at, COUNT(*) as total FROM
			// 					 		(SELECT scraped_products.website, DATE_FORMAT(scraped_products.created_at, "%Y-%m-%d") as created_at
			// 					  		 FROM scraped_products
			// 					  		 WHERE scraped_products.created_at LIKE "%?%"
      //                  AND scraped_products.sku IN (SELECT products.sku FROM products WHERE products.sku = scraped_products.sku)
      //                  )
			// 					    AS SUBQUERY
      //               GROUP BY website;
			// 				', [$two_days_ago]);

      $products_count = DB::select("
									SELECT website, created_at, COUNT(*) as total FROM
								 		(SELECT scraped_products.website, scraped_products.sku, DATE_FORMAT(scraped_products.created_at, '%Y-%m-%d') as created_at
								  		 FROM scraped_products
								  		 WHERE scraped_products.created_at BETWEEN '$start' AND '$end'
                       AND scraped_products.sku IN (SELECT products.sku FROM products WHERE products.sku = scraped_products.sku)
                       )

								    AS SUBQUERY
                    GROUP BY website;
							");

              // dd($scraped_count);

        $listed_days_ago_count = DB::select("
  									SELECT website, created_at, COUNT(*) as total FROM
  								 		(SELECT scraped_products.website, scraped_products.sku, DATE_FORMAT(scraped_products.created_at, '%Y-%m-%d') as created_at
  								  		 FROM scraped_products
  								  		 WHERE scraped_products.created_at BETWEEN '$start' AND '$end'
                         AND scraped_products.sku IN (SELECT products.sku FROM products WHERE products.sku = scraped_products.sku AND products.isUploaded = 1)
                         )

  								    AS SUBQUERY
                      GROUP BY website;
  							");

        $inventory_data = DB::select("
  									SELECT website, status, created_at, COUNT(*) as total FROM
  								 		(SELECT scrap_activities.website, scrap_activities.status, DATE_FORMAT(scrap_activities.created_at, '%Y-%m-%d') as created_at
  								  		 FROM scrap_activities
  								  		 WHERE scrap_activities.created_at BETWEEN '$start' AND '$end')
  								    AS SUBQUERY
  								   	GROUP BY website, status;
  							");

        $new_inventory = [];
        foreach ($inventory_data as $data) {
          $new_inventory[$data->website][$data->status] = $data->total;
        }

                // dd($new_inventory);

      $emails = Email::where('type', 'incoming')->where(function($query) {
        $query->where('model_type', 'App\Supplier')->orWhere('model_type', 'App\Purchase');
      })->latest()->get()->groupBy(['model_id', 'seen']);

      $emails_array = [];

      foreach ($emails as $supplier_id => $data) {
        $emails_array[$supplier_id]['0'] = 0;
        $emails_array[$supplier_id]['1'] = 0;
        foreach ($data as $seen => $info) {
          if ($seen == 0) {
            $emails_array[$supplier_id]['0'] = count($info);
          } else {
            $emails_array[$supplier_id]['1'] = count($info);
          }
        }
      }

      // dd($emails_array);
      $suppliers_array = [];
      $suppliers = Supplier::all();

      foreach ($suppliers as $supplier) {
        $suppliers_array[$supplier->id] = $supplier->supplier;
      }

      $reviews_array = [];
      $reviews = Review::whereBetween('created_at', [$start, $end])->get()->groupBy(['platform', function($query) {
        if ($query->status == 'posted') {
          return 'posted';
        } else {
          return 'notposted';
        }
      }]);

      foreach ($reviews as $platform => $data) {
        $reviews_array[$platform]['notposted'] = 0;
        $reviews_array[$platform]['posted'] = 0;
        foreach ($data as $status => $review) {
          if ($status == 'notposted') {
            $reviews_array[$platform]['notposted'] = count($review);
          } else {
            $reviews_array[$platform]['posted'] = count($review);
          }
        }
      }

      $cron_job = CronJob::where('signature', 'run:message-queues')->first();
      $pending_messages_count = MessageQueue::where('sent', 0)->where('status', '!=', 1)->where('sending_time', '<', Carbon::now())->count();

      // dd($reviews);

              // dd($scraped_count);
      // dd($unread_messages);

      return view('mastercontrol.index', [
        'message_groups'  => $new_data,
        'tasks'           => $tasks,
        'users_array'     => $users_array,
        'instructions'     => $instructions,
        // 'last_pending_instruction'     => $last_pending_instruction,
        // 'completed_instructions'     => $completed_instructions,
        'customers'     => $customers,
        'reply_categories'     => $reply_categories,
        'developer_tasks'     => $developer_tasks,
        // 'last_pending_developer_task'     => $last_pending_developer_task,
        // 'completed_developer_tasks'     => $completed_developer_tasks,
        'orders'     => $orders,
        'purchases'     => $purchases,
        'unread_messages'     => $unread_messages,
        'scraped_count'     => $scraped_count,
        // 'scraped_days_ago_count'     => $scraped_days_ago_count,
        'products_count'     => $products_count,
        'listed_days_ago_count'     => $listed_days_ago_count,
        'inventory_data'     => $new_inventory,
        'emails'     => $emails_array,
        'suppliers_array'     => $suppliers_array,
        'start'     => $start,
        'end'     => $end,
        'reviews'     => $reviews_array,
        'instruction_categories_array'     => $instruction_categories_array,
        'cron_job'     => $cron_job,
        'pending_messages_count'     => $pending_messages_count,
      ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function clearAlert(Request $request)
    {
      PushNotification::where('model_type', 'MasterControl')->delete();

      return redirect()->route('mastercontrol.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
