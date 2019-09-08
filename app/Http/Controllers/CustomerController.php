<?php

namespace App\Http\Controllers;

use App\Complaint;
use Dompdf\Dompdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Imports\CustomerImport;
use App\Exports\CustomersExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Mail\CustomerEmail;
use App\Mail\RefundProcessed;
use App\Mail\OrderConfirmation;
use App\Mail\AdvanceReceipt;
use App\Mail\IssueCredit;
use Illuminate\Support\Facades\Mail;
use App\Customer;
use App\Suggestion;
use App\Setting;
use App\Leads;
use App\Order;
use App\Status;
use App\Product;
use App\Brand;
use App\Supplier;
use App\ApiKey;
use App\Category;
use App\User;
use App\MessageQueue;
use App\Message;
use App\Helpers;
use App\Reply;
use App\Email;
use App\Instruction;
use App\ChatMessage;
use App\ReplyCategory;
use App\CallRecording;
use App\CommunicationHistory;
use App\InstructionCategory;
use App\OrderStatus as OrderStatuses;
use App\ReadOnly\PurchaseStatus;
use App\ReadOnly\SoloNumbers;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use Webklex\IMAP\Client;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use Auth;

class CustomerController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:customer');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

//    public function __construct() {
//      $this->middleware('permission:customer', ['only' => ['index','show']]);
//    }

    public function index(Request $request)
    {
        $complaints = Complaint::whereNotNull('customer_id')->pluck('complaint', 'customer_id')->toArray();
        $instructions = Instruction::with('remarks')->orderBy('is_priority', 'DESC')->orderBy('created_at', 'DESC')->select(['id', 'instruction', 'customer_id', 'assigned_to', 'pending', 'completed_at', 'verified', 'is_priority', 'created_at'])->get()->groupBy('customer_id')->toArray();
        $orders = Order::latest()->select(['id', 'customer_id', 'order_status', 'created_at'])->get()->groupBy('customer_id')->toArray();
        $order_stats = DB::table('orders')->selectRaw('order_status, COUNT(*) as total')->whereNotNull('order_status')->groupBy('order_status')->get();

        $finalOrderStats = [];
        $totalCount = 0;
        foreach ($order_stats as $order_stat) {
            $totalCount += $order_stat->total;
        }

        foreach ($order_stats as $key => $order_stat) {
            $finalOrderStats[] = array(
                $order_stat->order_status,
                $order_stat->total,
                ($order_stat->total / $totalCount) * 100,
                [
                    '#CCCCCC',
                    '#95a5a6',
                    '#b2b2b2',
                    '#999999',
                    '#2c3e50',
                    '#7f7f7f',
                    '#666666',
                    '#4c4c4c',
                    '#323232',
                    '#191919',
                    '#000000',
                    '#414a4c',
                    '#353839',
                    '#232b2b',
                    '#34495e',
                    '#7f8c8d',
                ][ $key ]
            );
        }

        $order_stats = $finalOrderStats;

        // dd(';s');
        // $customers = Customer::with('whatsapps')->get();
        // $messages = DB::table('chat_messages')->selectRaw('id, message, customer_id GROUP BY customer_id')->get();
        // dd($messages);

        $results = $this->getCustomersIndex($request);

        $term = $request->input('term');
        $reply_categories = ReplyCategory::all();
        $api_keys = ApiKey::select('number')->get();

        $type = $request->type ?? '';

        $orderby = 'desc';
        if ($request->orderby == '') {
            $orderby = 'asc';
        }

        $customers_all = Customer::all();
        $customer_names = Customer::select(['name'])->get()->toArray();

        $category_suggestion = Category::attr(['name' => 'category[]', 'class' => 'form-control select-multiple', 'multiple' => 'multiple'])
            ->renderAsDropdown();

        $brands = Brand::all()->toArray();

        foreach ($customer_names as $name) {
            $search_suggestions[] = $name[ 'name' ];
        }

        $users_array = Helpers::getUserArray(User::all());

        $last_set_id = MessageQueue::max('group_id');

        $queues_total_count = MessageQueue::where('status', '!=', 1)->where('group_id', $last_set_id)->count();
        $queues_sent_count = MessageQueue::where('sent', 1)->where('status', '!=', 1)->where('group_id', $last_set_id)->count();

        $start_time = $request->range_start ? "$request->range_start 00:00" : Carbon::now()->subDay();
        $end_time = $request->range_end ? "$request->range_end 23:59" : Carbon::now()->subDay();


        return view('customers.index', [
            'customers' => $results[ 0 ],
            'customers_all' => $customers_all,
            'customer_ids_list' => json_encode($results[ 1 ]),
            'users_array' => $users_array,
            'instructions' => $instructions,
            'term' => $term,
            'orderby' => $orderby,
            'type' => $type,
            'queues_total_count' => $queues_total_count,
            'queues_sent_count' => $queues_sent_count,
            'search_suggestions' => $search_suggestions,
            'reply_categories' => $reply_categories,
            'orders' => $orders,
            'api_keys' => $api_keys,
            'category_suggestion' => $category_suggestion,
            'brands' => $brands,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'leads_data' => $results[ 2 ],
            'order_stats' => $order_stats,
            'complaints' => $complaints
        ]);
    }

    public function getCustomersIndex(Request $request)
    {
        // Set search term
        $term = $request->term;

        // Set delivery status
        $delivery_status = [
            'Follow up for advance',
            'Proceed without Advance',
            'Advance received',
            'Cancel',
            'Prepaid',
            'Product Shiped form Italy',
            'In Transist from Italy',
            'Product shiped to Client',
            'Delivered'
        ];

        // Set empty clauses for later usage
        $orderWhereClause = '';
        $searchWhereClause = '';
        $filterWhereClause = '';
        $leadsWhereClause = '';

        if (!empty($term)) {
            $searchWhereClause = " AND (customers.name LIKE '%$term%' OR customers.phone LIKE '%$term%' OR customers.instahandler LIKE '%$term%')";
            $orderWhereClause = "WHERE orders.order_id LIKE '%$term%'";
        }

        $orderby = 'DESC';

        if ($request->input('orderby')) {
            $orderby = 'ASC';
        }
        $sortby = 'communication';

        $sortBys = [
            'name' => 'name',
            'email' => 'email',
            'phone' => 'phone',
            'instagram' => 'instahandler',
            'lead_created' => 'lead_created',
            'order_created' => 'order_created',
            'rating' => 'rating',
            'communication' => 'communication'
        ];

        if (isset($sortBys[ $request->input('sortby') ])) {
            $sortby = $sortBys[ $request->input('sortby') ];
        }

        $start_time = $request->range_start ? "$request->range_start 00:00" : '';
        $end_time = $request->range_end ? "$request->range_end 23:59" : '';

        if ($start_time != '' && $end_time != '') {
            $filterWhereClause = " WHERE last_communicated_at BETWEEN '" . $start_time . "' AND '" . $end_time . "'";
        }

        if ($request->type == 'unread' || $request->type == 'unapproved') {
            $join = "RIGHT";
            $type = $request->type == 'unread' ? 0 : ($request->type == 'unapproved' ? 1 : 0);
            $orderByClause = " ORDER BY is_flagged DESC, message_status ASC, last_communicated_at $orderby";
            $filterWhereClause = " AND chat_messages.status = $type";
            $messageWhereClause = " WHERE chat_messages.status != 7 AND chat_messages.status != 8 AND chat_messages.status != 9 AND chat_messages.status != 10";
            // $messageWhereClause = " WHERE chat_messages.status = $type";

            if ($start_time != '' && $end_time != '') {
                $filterWhereClause = " WHERE (last_communicated_at BETWEEN '" . $start_time . "' AND '" . $end_time . "') AND message_status = $type";
            }
        } else {
            if (
                $request->get('type') === 'Advance received' ||
                $request->get('type') === 'Cancel' ||
                $request->get('type') === 'Delivered' ||
                $request->get('type') === 'Follow up for advance' ||
                $request->get('type') === 'HIGH PRIORITY' ||
                $request->get('type') === 'In Transist from Italy' ||
                $request->get('type') === 'Prepaid' ||
                $request->get('type') === 'Proceed without Advance' ||
                $request->get('type') === 'Product Shiped form Italy' ||
                $request->get('type') === 'Product shiped to Client' ||
                $request->get('type') === 'Refund Credited' ||
                $request->get('type') === 'Refund Dispatched' ||
                $request->get('type') === 'Refund to be processed'
            ) {
                $join = 'LEFT';
                $orderByClause = " ORDER BY is_flagged DESC, last_communicated_at $orderby";
                $messageWhereClause = ' WHERE chat_messages.status != 7 AND chat_messages.status != 8 AND chat_messages.status != 9';
                if ($orderWhereClause) {
                    $orderWhereClause .= ' AND ';
                } else {
                    $orderWhereClause = ' WHERE ';
                }
                $orderWhereClause .= 'orders.order_status = "' . $request->get('type') . '"';
                $filterWhereClause = ' AND order_status = "' . $request->get('type') . '"';

            } else {
                if ($request->type != 'new' && $request->type != 'delivery' && $request->type != 'Refund to be processed' && $request->type != '') {
                    $join = 'LEFT';
                    $orderByClause = " ORDER BY is_flagged DESC, last_communicated_at $orderby";
                    $messageWhereClause = " WHERE chat_messages.status != 7 AND chat_messages.status != 8 AND chat_messages.status != 9";

                    if ($request->type == '0') {
                        $leadsWhereClause = ' WHERE lead_status IS NULL';
                    } else {
                        $leadsWhereClause = " WHERE lead_status = $request->type";
                    }
                } else {
                    if ($sortby === 'communication') {
                        $join = "LEFT";
                        $orderByClause = " ORDER BY is_flagged DESC, last_communicated_at $orderby";
                        $messageWhereClause = " WHERE chat_messages.status != 7 AND chat_messages.status != 8 AND chat_messages.status != 9";
                    }
                }
            }
        }

        $assignedWhereClause = '';
        if (Auth::user()->hasRole('Customer Care')) {
            $user_id = Auth::id();
            $assignedWhereClause = " AND id IN (SELECT customer_id FROM user_customers WHERE user_id = $user_id)";
        }

        if (!$orderByClause) {
            $orderByClause = ' ORDER BY instruction_completed_at DESC';
        } else {
            $orderByClause .= ', instruction_completed_at DESC';
        }

        $sql = '
            SELECT
                customers.id,
                customers.frequency,
                customers.reminder_message,
                customers.name,
                customers.phone,
                customers.is_blocked,
                customers.is_flagged,
                customers.is_error_flagged,
                customers.is_priority,
                customers.instruction_completed_at,
                chat_messages.*,
                chat_messages.status AS message_status,
                chat_messages.number,
                orders.*,
                order_products.*,
                leads.*
            FROM
                customers
            LEFT JOIN
                (
                    SELECT
                        chat_messages.id AS message_id,
                        chat_messages.customer_id,
                        chat_messages.number,
                        chat_messages.message,
                        chat_messages.sent AS message_type,
                        chat_messages.status,
                        chat_messages.created_at,
                        chat_messages.created_at AS last_communicated_at
                    FROM
                        chat_messages
                    ' . $messageWhereClause .'
                ) AS chat_messages
            ON 
                customers.id=chat_messages.customer_id AND 
                chat_messages.message_id=(
                    SELECT
                        MAX(id)
                    FROM
                        chat_messages
                    ' . $messageWhereClause . (!empty($messageWhereClause) ? ' AND ' : '') . '
                        chat_messages.customer_id=customers.id
                    GROUP BY
                        chat_messages.customer_id
                )
            LEFT JOIN
                (
                    SELECT 
                        MAX(orders.id) as order_id, 
                        orders.customer_id, 
                        MAX(orders.created_at) as order_created, 
                        orders.order_status as order_status 
                    FROM 
                        orders
                    ' . $orderWhereClause . ' 
                    GROUP BY 
                        customer_id
                ) as orders
            ON
                customers.id=orders.customer_id
            LEFT JOIN
                (
                    SELECT 
                        order_products.order_id as purchase_order_id, 
                        order_products.purchase_status
                    FROM 
                        order_products 
                    GROUP BY 
                        purchase_order_id
                ) as order_products
            ON 
                orders.order_id=order_products.purchase_order_id
            LEFT JOIN
                (
                    SELECT 
                        MAX(id) as lead_id, 
                        leads.customer_id, 
                        leads.rating as lead_rating, 
                        MAX(leads.created_at) as lead_created, 
                        leads.status as lead_status
                    FROM 
                        leads
                    GROUP BY 
                        customer_id
                ) AS leads
            ON 
                customers.id = leads.customer_id
            WHERE
                customers.deleted_at IS NULL AND
                customers.id IS NOT NULL
            ' . $searchWhereClause . '
            ' . $filterWhereClause . '
            ' . $leadsWhereClause . '
            ' . $assignedWhereClause . '
            ' . $orderByClause . '
        ';
        $customers = DB::select($sql);

        echo "<!-- ";
        echo $sql;
        echo "-->";

        $oldSql = '
            SELECT
              *
            FROM
            (
                SELECT 
                    customers.id, 
                    customers.frequency, 
                    customers.reminder_message, 
                    customers.name, 
                    customers.phone, 
                    customers.is_blocked, 
                    customers.is_flagged, 
                    customers.is_error_flagged, 
                    customers.is_priority, 
                    customers.deleted_at, 
                    customers.instruction_completed_at,
                    order_status,
                    purchase_status,
                    (
                    SELECT 
                            mm5.status 
                        FROM 
                            leads mm5 
                        WHERE 
                            mm5.id=lead_id
                    ) AS lead_status,
                    lead_id,
                    (
                    SELECT
                            mm3.id 
                        FROM 
                            chat_messages mm3 
                        WHERE 
                            mm3.id=message_id
                    ) AS message_id,
                    (
                    SELECT 
                            mm1.message 
                        FROM 
                            chat_messages mm1 
                        WHERE mm1.id=message_id
                    ) as message,
                    (
                    SELECT
                            mm2.status 
                        FROM 
                            chat_messages mm2 
                        WHERE
                            mm2.id = message_id
                    ) AS message_status,
                    (
                    SELECT 
                            mm4.sent 
                        FROM 
                            chat_messages mm4 
                        WHERE 
                            mm4.id = message_id
                    ) AS message_type,
                    (
                    SELECT 
                            mm2.created_at 
                        FROM 
                            chat_messages mm2 
                        WHERE 
                            mm2.id = message_id
                    ) as last_communicated_at
                FROM
                    (
                        SELECT
                            *
                        FROM 
                            customers
                        LEFT JOIN
                            (
                                SELECT 
                                    MAX(id) as lead_id, 
                                    leads.customer_id as lcid, 
                                    leads.rating as lead_rating, 
                                    MAX(leads.created_at) as lead_created, 
                                    leads.status as lead_status
                                FROM 
                                    leads
                                GROUP BY 
                                    customer_id
                            ) AS leads
                        ON 
                            customers.id = leads.lcid
                        LEFT JOIN
                            (
                                SELECT 
                                    MAX(id) as order_id, 
                                    orders.customer_id as ocid, 
                                    MAX(orders.created_at) as order_created, 
                                    orders.order_status as order_status 
                                FROM 
                                    orders ' . $orderWhereClause . ' 
                                GROUP BY 
                                    customer_id
                            ) as orders
                        ON
                            customers.id = orders.ocid
                        LEFT JOIN
                            (
                                SELECT 
                                    order_products.order_id as purchase_order_id, 
                                    order_products.purchase_status 
                                FROM 
                                    order_products 
                                GROUP BY 
                                    purchase_order_id
                            ) as order_products
                        ON 
                            orders.order_id = order_products.purchase_order_id
                        ' . $join . ' JOIN
                            (
                                SELECT 
                                    MAX(id) as message_id, 
                                    customer_id, 
                                    message, 
                                    MAX(created_at) as message_created_At 
                                FROM 
                                    chat_messages ' . $messageWhereClause . ' 
                                GROUP BY 
                                    customer_id 
                                ORDER BY 
                                    chat_messages.created_at ' . $orderby . '
                            ) AS chat_messages
                        ON 
                            customers.id = chat_messages.customer_id
                    ) AS customers
                WHERE
                    deleted_at IS NULL
                ) AND (
                    id IS NOT NULL
                )
                ' . $searchWhereClause . '
          ) AS customers
          ' . $filterWhereClause . $leadsWhereClause .
            $assignedWhereClause .
            $orderByClause;

        // dd($customers);

        // $customers = DB::select('
        // 						SELECT * FROM
        //             (SELECT *
        //
        //             FROM (
        //               SELECT * FROM customers
        //
        //               LEFT JOIN (
        //                 SELECT MAX(id) as lead_id, leads.customer_id as lcid, leads.rating as lead_rating, MAX(leads.created_at) as lead_created, leads.status as lead_status
        //                 FROM leads
        //                 GROUP BY customer_id
        //               ) AS leads
        //               ON customers.id = leads.lcid
        //
        //               LEFT JOIN
        //                 (SELECT MAX(id) as order_id, orders.customer_id as ocid, MAX(orders.created_at) as order_created, orders.order_status as order_status FROM orders '. $orderWhereClause .' GROUP BY customer_id) as orders
        //                   LEFT JOIN (SELECT order_products.order_id as purchase_order_id, order_products.purchase_status FROM order_products GROUP BY purchase_order_id) as order_products
        //                   ON orders.order_id = order_products.purchase_order_id
        //               ON customers.id = orders.ocid
        //
        //               ' . $join . ' JOIN (SELECT MAX(id) as message_id, customer_id, message, MAX(created_at) as message_created_At FROM chat_messages ' . $messageWhereClause . ' ORDER BY chat_messages.created_at ' . $orderby . ') AS chat_messages
        //               ON customers.id = chat_messages.customer_id
        //
        //
        //             ) AS customers
        //             WHERE (deleted_at IS NULL) AND (id IS NOT NULL)
        //
        //             ' . $searchWhereClause . '
        //           ) AS customers
        //           ' . $filterWhereClause . $leadsWhereClause . ';
        // 				');


        // dd($customers);

        $leads_data = DB::select('
                      SELECT COUNT(*) AS total,
                      (SELECT mm1.status FROM leads mm1 WHERE mm1.id = lead_id) as lead_final_status
                       FROM customers

                      LEFT JOIN (
                        SELECT MAX(id) as lead_id, leads.customer_id as lcid, leads.rating as lead_rating, MAX(leads.created_at) as lead_created, leads.status as lead_status
                        FROM leads
                        GROUP BY customer_id
                      ) AS leads
                      ON customers.id = leads.lcid

                      WHERE (deleted_at IS NULL) AND (id IS NOT NULL)
                      GROUP BY lead_final_status;
  							');


        // dd($leads_data);
        $ids_list = [];

        // $leads_data = [0, 0, 0, 0, 0, 0, 0];
        foreach ($customers as $customer) {
            if ($customer->id != null) {
                $ids_list[] = $customer->id;

                // $lead_status = $customer->lead_status == null ? '0' : $customer->lead_status;
                //
                // $leads_data[$lead_status] += 1;
            }
        }


        // dd($leads_data);

        // if ($start_time != '' && $end_time != '') {
        //   $customers = $customers->whereBetween('chat_message_created_at', [$start_time, $end_time])->paginate(Setting::get('pagination'));
        // } else if ($request->type == 'unapproved') {
        //   // dd($customers->get());
        //   $customers = $customers->where('status', 1)->paginate(Setting::get('pagination'));
        // } else {
        //   $customers = $customers->paginate(Setting::get('pagination'));
        // }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = Setting::get('pagination');
        $currentItems = array_slice($customers, $perPage * ($currentPage - 1), $perPage);

        $customers = new LengthAwarePaginator($currentItems, count($customers), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath()
        ]);

        return [$customers, $ids_list, $leads_data];
    }

    public function customerstest(Request $request)
    {
        $instructions = Instruction::with('remarks')->orderBy('is_priority', 'DESC')->orderBy('created_at', 'DESC')->select(['id', 'instruction', 'customer_id', 'assigned_to', 'pending', 'completed_at', 'verified', 'is_priority', 'created_at'])->get()->groupBy('customer_id')->toArray();
        $orders = Order::latest()->select(['id', 'customer_id', 'order_status', 'created_at'])->get()->groupBy('customer_id')->toArray();

        $term = $request->input('term');
        $reply_categories = ReplyCategory::all();
        $api_keys = ApiKey::select('number')->get();

        // $type = $request->type ?? '';

        $orderby = 'desc';
        if ($request->orderby == '') {
            $orderby = 'asc';
        }

        $customers_all = Customer::all();
        $customer_names = Customer::select(['name'])->get()->toArray();

        $category_suggestion = Category::attr(['name' => 'category[]', 'class' => 'form-control select-multiple', 'multiple' => 'multiple'])
            ->renderAsDropdown();

        $brands = Brand::all()->toArray();

        foreach ($customer_names as $name) {
            $search_suggestions[] = $name[ 'name' ];
        }

        $users_array = Helpers::getUserArray(User::all());

        $last_set_id = MessageQueue::max('group_id');

        $queues_total_count = MessageQueue::where('status', '!=', 1)->where('group_id', $last_set_id)->count();
        $queues_sent_count = MessageQueue::where('sent', 1)->where('status', '!=', 1)->where('group_id', $last_set_id)->count();


        $term = $request->input('term');
        // $customers = DB::table('customers');
        $delivery_status = [
            'Follow up for advance',
            'Proceed without Advance',
            'Advance received',
            'Cancel',
            'Prepaid',
            'Product Shiped form Italy',
            'In Transist from Italy',
            'Product shiped to Client',
            'Delivered'
        ];

        $orderWhereClause = '';
        $searchWhereClause = '';
        $filterWhereClause = '';

        if (!empty($term)) {
            $searchWhereClause = " AND (customers.name LIKE '%$term%' OR customers.phone LIKE '%$term%' OR customers.instahandler LIKE '%$term%')";
            $orderWhereClause = "WHERE orders.order_id LIKE '%$term%'";

            // if ($request->type == 'delivery' || $request->type == 'new' || $request->type == 'Refund to be processed') {
            //   $status_array = [];
            //
            //   if ($request->type == 'delivery') {
            //     array_push($delivery_status, 'VIP', 'HIGH PRIORITY');
            //
            //     $status_array = $delivery_status;
            //   } else if ($request->type == 'Refund to be processed') {
            //     $status_array = [$request->type];
            //   } else if ($request->type == 'new') {
            //     $status_array = [
            //       'Delivered',
            //       'Refund Dispatched',
            //       'Refund Credited'
            //     ];
            //   }
            //
            //   $imploded = implode("','", $status_array);
            //
            //   $orderWhereClause = "WHERE orders.order_id LIKE '%$term%' AND orders.order_status IN ('" . $imploded . "')";
            // } else {
        }

        $orderby = 'DESC';

        if ($request->input('orderby')) {
            $orderby = 'ASC';
        }

        $sortby = 'communication';

        $sortBys = [
            'name' => 'name',
            'email' => 'email',
            'phone' => 'phone',
            'instagram' => 'instahandler',
            'lead_created' => 'lead_created',
            'order_created' => 'order_created',
            'rating' => 'rating',
            'communication' => 'communication'
        ];

        if (isset($sortBys[ $request->input('sortby') ])) {
            $sortby = $sortBys[ $request->input('sortby') ];
        }

        $start_time = $request->input('range_start') ?? '';
        $end_time = $request->input('range_end') ?? '';

        if ($start_time != '' && $end_time != '') {
            $filterWhereClause = " WHERE last_communicated_at BETWEEN '" . $start_time . "' AND '" . $end_time . "'";
        }

        if ($request->type == 'unread' || $request->type == 'unapproved') {
            $join = "RIGHT";
            $type = $request->type == 'unread' ? 0 : ($request->type == 'unapproved' ? 1 : 0);
            $orderByClause = " ORDER BY is_flagged DESC, message_status ASC, `last_communicated_at` $orderby";
            $filterWhereClause = " WHERE message_status = $type";

            if ($start_time != '' && $end_time != '') {
                $filterWhereClause = " WHERE (last_communicated_at BETWEEN '" . $start_time . "' AND '" . $end_time . "') AND message_status = $type";
            }
        } else {
            if ($sortby === 'communication') {
                $join = "LEFT";
                $orderByClause = " ORDER BY is_flagged DESC, last_communicated_at $orderby";
            }
        }

        $new_customers = DB::select('
  									SELECT * FROM
                    (SELECT customers.id, customers.name, customers.phone, customers.is_blocked, customers.is_flagged, customers.is_error_flagged, customers.is_priority, customers.deleted_at,
                    lead_id, lead_status, lead_created, lead_rating,
                    order_id, order_status, order_created, purchase_status,
                    (SELECT mm3.id FROM chat_messages mm3 WHERE mm3.id = message_id) AS message_id,
                    (SELECT mm1.message FROM chat_messages mm1 WHERE mm1.id = message_id) as message,
                    (SELECT mm2.status FROM chat_messages mm2 WHERE mm2.id = message_id) AS message_status,
                    (SELECT mm4.sent FROM chat_messages mm4 WHERE mm4.id = message_id) AS message_type,
                    (SELECT mm2.created_at FROM chat_messages mm2 WHERE mm2.id = message_id) as last_communicated_at

                    FROM (
                      SELECT * FROM customers

                      LEFT JOIN (
                        SELECT MAX(id) as lead_id, leads.customer_id as lcid, leads.rating as lead_rating, MAX(leads.created_at) as lead_created, leads.status as lead_status
                        FROM leads
                        GROUP BY customer_id
                      ) AS leads
                      ON customers.id = leads.lcid

                      LEFT JOIN
                        (SELECT MAX(id) as order_id, orders.customer_id as ocid, MAX(orders.created_at) as order_created, orders.order_status as order_status FROM orders ' . $orderWhereClause . ' GROUP BY customer_id) as orders
                          LEFT JOIN (SELECT order_products.order_id as purchase_order_id, order_products.purchase_status FROM order_products) as order_products
                          ON orders.order_id = order_products.purchase_order_id

                      ' . $join . ' JOIN (SELECT MAX(id) as message_id, customer_id, message, MAX(created_at) as message_created_At FROM chat_messages GROUP BY customer_id ORDER BY created_at DESC) AS chat_messages
                      ON customers.id = chat_messages.customer_id


                    ) AS customers
                    WHERE (deleted_at IS NULL)
                    ' . $searchWhereClause . '
                    ' . $orderByClause . '
                  ) AS customers
                  ' . $filterWhereClause . ';
  							');


        // dd($new_customers);

        $ids_list = [];
        foreach ($new_customers as $customer) {
            $ids_list[] = $customer->id;
        }


        // if ($start_time != '' && $end_time != '') {
        //   $customers = $customers->whereBetween('chat_message_created_at', [$start_time, $end_time])->paginate(Setting::get('pagination'));
        // } else if ($request->type == 'unapproved') {
        //   // dd($customers->get());
        //   $customers = $customers->where('status', 1)->paginate(Setting::get('pagination'));
        // } else {
        //   $customers = $customers->paginate(Setting::get('pagination'));
        // }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = Setting::get('pagination');
        $currentItems = array_slice($new_customers, $perPage * ($currentPage - 1), $perPage);

        $new_customers = new LengthAwarePaginator($currentItems, count($new_customers), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath()
        ]);


        dd([
            'customers' => $new_customers,
            'customers_all' => $customers_all,
            'customer_ids_list' => json_encode($ids_list),
            'users_array' => $users_array,
            'instructions' => $instructions,
            'term' => $term,
            'orderby' => $orderby,
            'type' => $type,
            'queues_total_count' => $queues_total_count,
            'queues_sent_count' => $queues_sent_count,
            'search_suggestions' => $search_suggestions,
            'reply_categories' => $reply_categories,
            'orders' => $orders,
            'api_keys' => $api_keys,
            'category_suggestion' => $category_suggestion,
            'brands' => $brands,
        ]);

        return view('customers.index', [
            'customers' => $new_customers,
            'customers_all' => $customers_all,
            'customer_ids_list' => json_encode($ids_list),
            'users_array' => $users_array,
            'instructions' => $instructions,
            'term' => $term,
            'orderby' => $orderby,
            'type' => $type,
            'queues_total_count' => $queues_total_count,
            'queues_sent_count' => $queues_sent_count,
            'search_suggestions' => $search_suggestions,
            'reply_categories' => $reply_categories,
            'orders' => $orders,
            'api_keys' => $api_keys,
            'category_suggestion' => $category_suggestion,
            'brands' => $brands,
        ]);
    }

    public function search(Request $request)
    {
        $keyword = $request->get('keyword');
        $messages = ChatMessage::where('message', 'LIKE', "%$keyword%")->where('customer_id', '>', 0)->groupBy('customer_id')->with('customer')->select(DB::raw('MAX(id) as message_id, customer_id, message'))->get()->map(function ($item) {
            return [
                'customer_id' => $item->customer_id,
                'customer_name' => $item->customer->name,
                'message_id' => $item->message_id,
                'message' => $item->message,
            ];
        });

        return response()->json($messages);
    }

    public function loadMoreMessages(Request $request)
    {
        $customer = Customer::find($request->customer_id);

        $chat_messages = $customer->whatsapps_all()->skip(1)->take(3)->get();

        $messages = [];

        foreach ($chat_messages as $chat_message) {
            $messages[] = $chat_message->message;
        }

        return response()->json([
            'messages' => $messages
        ]);
    }

    public function sendAdvanceLink(Request $request, $id)
    {
        $customer = Customer::find($id);

        $options = array(
            'trace' => true,
            'connection_timeout' => 120,
            'wsdl_cache' => WSDL_CACHE_NONE,
        );

        $proxy = new \SoapClient(config('magentoapi.url'), $options);
        $sessionId = $proxy->login(config('magentoapi.user'), config('magentoapi.password'));

        $errors = 0;

        $productData = array(
            'price' => $request->price_inr,
            'special_price' => $request->price_special,
        );

        try {
            $result = $proxy->catalogProductUpdate($sessionId, "QUICKADVANCEPAYMENT", $productData);

            $params = [
                'customer_id' => $customer->id,
                'number' => null,
                'message' => "https://www.sololuxury.co.in/advance-payment-product.html",
                'user_id' => Auth::id(),
                'approve' => 0,
                'status' => 1
            ];

            ChatMessage::create($params);

            return response('success');
            // return redirect()->back()->withSuccess('You have successfully sent a link');
        } catch (\Exception $e) {
            $errors++;

            return response($e->getMessage());
            // dd($e);
            // return redirect()->back()->withError('You have failed sending a link');
        }
    }

    public function initiateFollowup(Request $request, $id)
    {
        CommunicationHistory::create([
            'model_id' => $id,
            'model_type' => Customer::class,
            'type' => 'initiate-followup',
            'method' => 'whatsapp'
        ]);

        return redirect()->route('customer.show', $id)->with('success', 'You have successfully initiated follow up sequence!');
    }

    public function stopFollowup(Request $request, $id)
    {
        $histories = CommunicationHistory::where('model_id', $id)->where('model_type', Customer::class)->where('type', 'initiate-followup')->where('is_stopped', 0)->get();

        foreach ($histories as $history) {
            $history->is_stopped = 1;
            $history->save();
        }

        return redirect()->route('customer.show', $id)->with('success', 'You have successfully stopped follow up sequence!');
    }

    public function export()
    {
        $customers = Customer::select(['name', 'phone'])->get()->toArray();

        return Excel::download(new CustomersExport($customers), 'customers.xlsx');
    }

    public function block(Request $request)
    {
        $customer = Customer::find($request->customer_id);

        if ($customer->is_blocked == 0) {
            $customer->is_blocked = 1;
        } else {
            $customer->is_blocked = 0;
        }

        $customer->save();

        return response()->json(['is_blocked' => $customer->is_blocked]);
    }

    public function flag(Request $request)
    {
        $customer = Customer::find($request->customer_id);

        if ($customer->is_flagged == 0) {
            $customer->is_flagged = 1;
        } else {
            $customer->is_flagged = 0;
        }

        $customer->save();

        return response()->json(['is_flagged' => $customer->is_flagged]);
    }

    public function prioritize(Request $request)
    {
        $customer = Customer::find($request->customer_id);

        if ($customer->is_priority == 0) {
            $customer->is_priority = 1;
        } else {
            $customer->is_priority = 0;
        }

        $customer->save();

        return response()->json(['is_priority' => $customer->is_priority]);
    }

    public function sendInstock(Request $request)
    {
        $customer = Customer::find($request->customer_id);

        $products = Product::where('supplier', 'In-stock')->latest()->get();

        $params = [
            'customer_id' => $customer->id,
            'number' => null,
            'user_id' => Auth::id(),
            'message' => 'In Stock Products',
            'status' => 1
        ];

        $chat_message = ChatMessage::create($params);

        foreach ($products as $product) {
            $chat_message->attachMedia($product->getMedia(config('constants.media_tags'))->first(), config('constants.media_tags'));
        }

        return response('success');
    }

    public function load(Request $request)
    {
        $first_customer = Customer::find($request->first_customer);
        $second_customer = Customer::find($request->second_customer);

        return response()->json([
            'first_customer' => $first_customer,
            'second_customer' => $second_customer
        ]);
    }

    public function merge(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:3|max:255',
            'email' => 'required_without_all:phone,instahandler|nullable|email',
            'phone' => 'required_without_all:email,instahandler|nullable|numeric|regex:/^[91]{2}/|digits:12|unique:customers,phone,' . $request->first_customer_id,
            'instahandler' => 'required_without_all:email,phone|nullable|min:3|max:255',
            'rating' => 'required|numeric',
            'address' => 'sometimes|nullable|min:3|max:255',
            'city' => 'sometimes|nullable|min:3|max:255',
            'country' => 'sometimes|nullable|min:3|max:255',
            'pincode' => 'sometimes|nullable|max:6'
        ]);

        $first_customer = Customer::find($request->first_customer_id);

        $first_customer->name = $request->name;
        $first_customer->email = $request->email;
        $first_customer->phone = $request->phone;
        $first_customer->whatsapp_number = $request->whatsapp_number;
        $first_customer->instahandler = $request->instahandler;
        $first_customer->rating = $request->rating;
        $first_customer->address = $request->address;
        $first_customer->city = $request->city;
        $first_customer->country = $request->country;
        $first_customer->pincode = $request->pincode;

        $first_customer->save();

        $chat_messages = ChatMessage::where('customer_id', $request->second_customer_id)->get();

        foreach ($chat_messages as $chat) {
            $chat->customer_id = $first_customer->id;
            $chat->save();
        }

        $messages = Message::where('customer_id', $request->second_customer_id)->get();

        foreach ($messages as $message) {
            $message->customer_id = $first_customer->id;
            $message->save();
        }

        $leads = Leads::where('customer_id', $request->second_customer_id)->get();

        foreach ($leads as $lead) {
            $lead->customer_id = $first_customer->id;
            $lead->save();
        }

        $orders = Order::where('customer_id', $request->second_customer_id)->get();

        foreach ($orders as $order) {
            $order->customer_id = $first_customer->id;
            $order->save();
        }

        $instructions = Instruction::where('customer_id', $request->second_customer_id)->get();

        foreach ($instructions as $instruction) {
            $instruction->customer_id = $first_customer->id;
            $instruction->save();
        }

        $second_customer = Customer::find($request->second_customer_id);
        $second_customer->delete();

        return redirect()->route('customer.index');
    }

    public function import(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:xls,xlsx'
        ]);

        (new CustomerImport)->queue($request->file('file'));

        return redirect()->back()->with('success', 'Customers are being imported in the background');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $solo_numbers = (new SoloNumbers)->all();

        return view('customers.create', [
            'solo_numbers' => $solo_numbers
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:3|max:255',
            'email' => 'required_without_all:phone,instahandler|nullable|email',
            'phone' => 'required_without_all:email,instahandler|nullable|numeric|digits:12|unique:customers',
            'instahandler' => 'required_without_all:email,phone|nullable|min:3|max:255',
            'rating' => 'required|numeric',
            'address' => 'sometimes|nullable|min:3|max:255',
            'city' => 'sometimes|nullable|min:3|max:255',
            'country' => 'sometimes|nullable|min:2|max:255',
            'pincode' => 'sometimes|nullable|max:6',
        ]);

        $customer = new Customer;

        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->whatsapp_number = $request->whatsapp_number;
        $customer->instahandler = $request->instahandler;
        $customer->rating = $request->rating;
        $customer->address = $request->address;
        $customer->city = $request->city;
        $customer->country = $request->country;
        $customer->pincode = $request->pincode;

        $customer->save();

        return redirect()->route('customer.index')->with('success', 'You have successfully added new customer!');
    }

    public function addNote($id, Request $request)
    {
        $customer = Customer::findOrFail($id);
        $notes = $customer->notes;
        if (!is_array($notes)) {
            $notes = [];
        }

        $notes[] = $request->get('note');
        $customer->notes = $notes;
        $customer->save();

        return response()->json([
            'status' => 'success'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customer = Customer::with(['call_recordings', 'orders', 'leads', 'facebookMessages'])->where('id', $id)->first();
        $customers = Customer::select(['id', 'name', 'email', 'phone', 'instahandler'])->get();

        $emails = [];
        $lead_status = (New status)->all();
        $users_array = Helpers::getUserArray(User::all());
        $brands = Brand::all()->toArray();
        $reply_categories = ReplyCategory::all();
        $instruction_categories = InstructionCategory::all();
        $instruction_replies = Reply::where('model', 'Instruction')->get();
        $order_status_report = OrderStatuses::all();
        $purchase_status = (new PurchaseStatus)->all();
        $solo_numbers = (new SoloNumbers)->all();
        $api_keys = ApiKey::select(['number'])->get();
        $suppliers = Supplier::select(['id', 'supplier'])
            ->whereRaw("suppliers.id IN (SELECT product_suppliers.supplier_id FROM product_suppliers)")->get();
        $category_suggestion = Category::attr(['name' => 'category[]', 'class' => 'form-control select-multiple', 'multiple' => 'multiple'])
            ->renderAsDropdown();

        $facebookMessages = null;
        if ($customer->facebook_id) {
            $facebookMessages = $customer->facebookMessages()->get();
        }


        return view('customers.show', [
            'customer' => $customer,
            'customers' => $customers,
            'lead_status' => $lead_status,
            'brands' => $brands,
            'users_array' => $users_array,
            'reply_categories' => $reply_categories,
            'instruction_categories' => $instruction_categories,
            'instruction_replies' => $instruction_replies,
            'order_status_report' => $order_status_report,
            'purchase_status' => $purchase_status,
            'solo_numbers' => $solo_numbers,
            'api_keys' => $api_keys,
            'emails' => $emails,
            'category_suggestion' => $category_suggestion,
            'suppliers' => $suppliers,
            'facebookMessages' => $facebookMessages
        ]);
    }

    public function exportCommunication($id)
    {
        $messages = ChatMessage::where('customer_id', $id)->orderBy('created_at', 'DESC')->get();

        $html = view('customers.chat_export', compact('messages'));

        $pdf = new Dompdf();
        $pdf->loadHtml($html);
        $pdf->render();
        $pdf->stream('orders.pdf');
    }

    public function postShow(Request $request, $id)
    {
        $customer = Customer::with(['call_recordings', 'orders', 'leads', 'facebookMessages'])->where('id', $id)->first();
        $customers = Customer::select(['id', 'name', 'email', 'phone', 'instahandler'])->get();

        $searchedMessages = null;
        if ($request->get('sm')) {
            $searchedMessages = ChatMessage::where('customer_id', $id)->where('message', 'LIKE', '%' . $request->get('sm') . '%')->get();
        }


        $customer_ids = json_decode($request->customer_ids ?? "[0]");
        $key = array_search($id, $customer_ids);

        if ($key != 0) {
            $previous_customer_id = $customer_ids[ $key - 1 ];
        } else {
            $previous_customer_id = 0;
        }

        if ($key == (count($customer_ids) - 1)) {
            $next_customer_id = 0;
        } else {
            $next_customer_id = $customer_ids[ $key + 1 ];
        }

        $emails = [];
        $lead_status = (New status)->all();
        $users_array = Helpers::getUserArray(User::all());
        $brands = Brand::all()->toArray();
        $reply_categories = ReplyCategory::all();
        $instruction_categories = InstructionCategory::all();
        $instruction_replies = Reply::where('model', 'Instruction')->get();
        $order_status_report = OrderStatuses::all();
        $purchase_status = (new PurchaseStatus)->all();
        $solo_numbers = (new SoloNumbers)->all();
        $api_keys = ApiKey::select(['number'])->get();
        $suppliers = Supplier::select(['id', 'supplier'])->get();
        $category_suggestion = Category::attr(['name' => 'category[]', 'class' => 'form-control select-multiple', 'multiple' => 'multiple'])
            ->renderAsDropdown();

        $facebookMessages = null;
        if ($customer->facebook_id) {
            $facebookMessages = $customer->facebookMessages()->get();
        }


        return view('customers.show', [
            'customer_ids' => json_encode($customer_ids),
            'previous_customer_id' => $previous_customer_id,
            'next_customer_id' => $next_customer_id,
            'customer' => $customer,
            'customers' => $customers,
            'lead_status' => $lead_status,
            'brands' => $brands,
            'users_array' => $users_array,
            'reply_categories' => $reply_categories,
            'instruction_categories' => $instruction_categories,
            'instruction_replies' => $instruction_replies,
            'order_status_report' => $order_status_report,
            'purchase_status' => $purchase_status,
            'solo_numbers' => $solo_numbers,
            'api_keys' => $api_keys,
            'emails' => $emails,
            'category_suggestion' => $category_suggestion,
            'suppliers' => $suppliers,
            'facebookMessages' => $facebookMessages,
            'searchedMessages' => $searchedMessages
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */

    public function emailInbox(Request $request)
    {
        $imap = new Client([
            'host' => env('IMAP_HOST'),
            'port' => env('IMAP_PORT'),
            'encryption' => env('IMAP_ENCRYPTION'),
            'validate_cert' => env('IMAP_VALIDATE_CERT'),
            'username' => env('IMAP_USERNAME'),
            'password' => env('IMAP_PASSWORD'),
            'protocol' => env('IMAP_PROTOCOL')
        ]);

        $imap->connect();

        $customer = Customer::find($request->customer_id);

        if ($request->type == 'inbox') {
            $inbox_name = 'INBOX';
            $direction = 'from';
        } else {
            $inbox_name = 'INBOX.Sent';
            $direction = 'to';
        }

        $inbox = $imap->getFolder($inbox_name);

        $emails = $inbox->messages()->where($direction, $customer->email);
        $emails = $emails->setFetchFlags(false)
            ->setFetchBody(false)
            ->setFetchAttachment(false)->leaveUnread()->get();


        $emails_array = [];
        $count = 0;

        foreach ($emails as $key => $email) {
            $emails_array[ $key ][ 'uid' ] = $email->getUid();
            $emails_array[ $key ][ 'subject' ] = $email->getSubject();
            $emails_array[ $key ][ 'date' ] = $email->getDate();

            $count++;
        }

        if ($request->type != 'inbox') {
            $db_emails = $customer->emails;

            foreach ($db_emails as $key2 => $email) {
                $emails_array[ $count + $key2 ][ 'id' ] = $email->id;
                $emails_array[ $count + $key2 ][ 'subject' ] = $email->subject;
                $emails_array[ $count + $key2 ][ 'date' ] = $email->created_at;
            }
        }

        $emails_array = array_values(array_sort($emails_array, function ($value) {
            return $value[ 'date' ];
        }));

        $emails_array = array_reverse($emails_array);

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $currentItems = array_slice($emails_array, $perPage * ($currentPage - 1), $perPage);
        $emails = new LengthAwarePaginator($currentItems, count($emails_array), $perPage, $currentPage);

        $view = view('customers.email', [
            'emails' => $emails,
            'type' => $request->type
        ])->render();

        return response()->json(['emails' => $view]);
    }

    public function emailFetch(Request $request)
    {
        $imap = new Client([
            'host' => env('IMAP_HOST'),
            'port' => env('IMAP_PORT'),
            'encryption' => env('IMAP_ENCRYPTION'),
            'validate_cert' => env('IMAP_VALIDATE_CERT'),
            'username' => env('IMAP_USERNAME'),
            'password' => env('IMAP_PASSWORD'),
            'protocol' => env('IMAP_PROTOCOL')
        ]);

        $imap->connect();

        if ($request->type == 'inbox') {
            $inbox = $imap->getFolder('INBOX');
        } else {
            $inbox = $imap->getFolder('INBOX.Sent');
            $inbox->query();
        }

        if ($request->email_type == 'server') {
            $email = $inbox->getMessage($uid = $request->uid, null, null, true, true, true);
            // dd($email);
            if ($email->hasHTMLBody()) {
                $content = $email->getHTMLBody();
            } else {
                $content = $email->getTextBody();
            }
        } else {
            $email = Email::find($request->uid);

            if ($email->template == 'customer-simple') {
                $content = (new CustomerEmail($email->subject, $email->message))->render();
            } else {
                if ($email->template == 'refund-processed') {
                    $details = json_decode($email->additional_data, true);

                    $content = (new RefundProcessed($details[ 'order_id' ], $details[ 'product_names' ]))->render();
                } else {
                    if ($email->template == 'order-confirmation') {
                        $order = Order::find($email->additional_data);

                        $content = (new OrderConfirmation($order))->render();
                    } else {
                        if ($email->template == 'advance-receipt') {
                            $order = Order::find($email->additional_data);

                            $content = (new AdvanceReceipt($order))->render();
                        } else {
                            if ($email->template == 'issue-credit') {
                                $customer = Customer::find($email->model_id);

                                $content = (new IssueCredit($customer))->render();
                            } else {
                                $content = 'No Template';
                            }
                        }
                    }
                }
            }
        }

        return response()->json(['email' => $content]);
    }

    public function emailSend(Request $request)
    {
        $this->validate($request, [
            'subject' => 'required|min:3|max:255',
            'message' => 'required'
        ]);

        $customer = Customer::find($request->customer_id);

        Mail::to($customer->email)->send(new CustomerEmail($request->subject, $request->message));

        if ($request->order_id != '') {
            $order_data = json_encode(['order_id' => $request->order_id]);
        }

        $params = [
            'model_id' => $customer->id,
            'model_type' => Customer::class,
            'from' => 'customercare@sololuxury.co.in',
            'to' => $customer->email,
            'send' => 1,
            'subject' => $request->subject,
            'message' => $request->message,
            'template' => 'customer-simple',
            'additional_data' => isset($order_data) ? $order_data : ''
        ];

        Email::create($params);

        return redirect()->route('customer.show', $customer->id)->withSuccess('You have successfully sent an email!');
    }

    public function edit($id)
    {
        $customer = Customer::find($id);
        $solo_numbers = (new SoloNumbers)->all();

        return view('customers.edit', [
            'customer' => $customer,
            'solo_numbers' => $solo_numbers
        ]);
    }

    public function updateReminder(Request $request)
    {
        $customer = Customer::find($request->get('customer_id'));
        $customer->frequency = $request->get('frequency');
        $customer->reminder_message = $request->get('message');
        $customer->save();

        return response()->json([
            'success'
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $customer = Customer::find($id);

        $this->validate($request, [
            'name' => 'required|min:3|max:255',
            'email' => 'required_without_all:phone,instahandler|nullable|email',
            'phone' => 'required_without_all:email,instahandler|nullable|unique:customers,phone,' . $id,
            'instahandler' => 'required_without_all:email,phone|nullable|min:3|max:255',
            'rating' => 'required|numeric',
            'address' => 'sometimes|nullable|min:3|max:255',
            'city' => 'sometimes|nullable|min:3|max:255',
            'country' => 'sometimes|nullable|min:2|max:255',
            'pincode' => 'sometimes|nullable|max:6',
            'shoe_size' => 'sometimes|nullable',
            'clothing_size' => 'sometimes|nullable',
            'gender' => 'sometimes|nullable|string',
            'credit' => 'sometimes|nullable|numeric',
        ]);

        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->whatsapp_number = $request->whatsapp_number;
        $customer->instahandler = $request->instahandler;
        $customer->rating = $request->rating;
        $customer->do_not_disturb = $request->do_not_disturb == 'on' ? 1 : 0;
        $customer->is_blocked = $request->is_blocked == 'on' ? 1 : 0;
        $customer->address = $request->address;
        $customer->city = $request->city;
        $customer->country = $request->country;
        $customer->pincode = $request->pincode;
        $customer->credit = $request->credit;
        $customer->shoe_size = $request->shoe_size;
        $customer->clothing_size = $request->clothing_size;
        $customer->gender = $request->gender;

        $customer->save();

        if ($request->do_not_disturb == 'on') {
            MessageQueue::where('customer_id', $customer->id)->delete();

            // foreach ($message_queues as $message_queue) {
            //   $message_queue->status = 1; // message stopped
            //   $message_queue->save();
            // }
        }

        return redirect()->route('customer.show', $id)->with('success', 'You have successfully updated the customer!');
    }

    public function updateNumber(Request $request, $id)
    {
        $customer = Customer::find($id);

        $customer->whatsapp_number = $request->whatsapp_number;
        $customer->save();

        return response('success');
    }

    public function updateDnd(Request $request, $id)
    {
        $customer = Customer::find($id);

        // $customer->do_not_disturb = $request->do_not_disturb;

        if ($customer->do_not_disturb == 1) {
            $customer->do_not_disturb = 0;
        } else {
            $customer->do_not_disturb = 1;
        }

        $customer->save();

        if ($request->do_not_disturb == 1) {
            MessageQueue::where('customer_id', $customer->id)->delete();

            // foreach ($message_queues as $message_queue) {
            //   $message_queue->status = 1; // message stopped
            //   $message_queue->save();
            // }
        }

        return response()->json([
            'do_not_disturb' => $customer->do_not_disturb
        ]);
    }

    public function updatePhone(Request $request, $id)
    {
        $this->validate($request, [
            'phone' => 'required|numeric'
        ]);

        $customer = Customer::find($id);

        $customer->phone = $request->phone;
        $customer->save();

        return response('success');
    }

    public function issueCredit(Request $request)
    {
        $customer = Customer::find($request->customer_id);

        Mail::to($customer->email)->send(new IssueCredit($customer));

        $message = "Dear $customer->name, this is to confirm that an amount of Rs. $customer->credit - is credited with us against your previous order. You can use this credit note for reference on your next purchase. Thanks & Regards, Solo Luxury Team";
        $requestData = new Request();
        $requestData->setMethod('POST');
        $requestData->request->add(['customer_id' => $customer->id, 'message' => $message]);

        app('App\Http\Controllers\WhatsAppController')->sendMessage($requestData, 'customer');

        CommunicationHistory::create([
            'model_id' => $customer->id,
            'model_type' => Customer::class,
            'type' => 'issue-credit',
            'method' => 'whatsapp'
        ]);

        CommunicationHistory::create([
            'model_id' => $customer->id,
            'model_type' => Customer::class,
            'type' => 'issue-credit',
            'method' => 'email'
        ]);

        $params = [
            'model_id' => $customer->id,
            'model_type' => Customer::class,
            'from' => 'customercare@sololuxury.co.in',
            'to' => $customer->email,
            'subject' => "Customer Credit Issued",
            'message' => '',
            'template' => 'issue-credit',
            'additional_data' => ''
        ];

        Email::create($params);
    }

    public function sendSuggestion(Request $request)
    {
        $customer = Customer::find($request->customer_id);
        $params = [
            'customer_id' => $customer->id,
            'number' => $request->number,
            'brand' => '',
            'category' => '',
            'size' => '',
            'supplier' => ''
        ];

        if ($request->brand[ 0 ] != null) {
            $products = Product::whereIn('brand', $request->brand);

            $params[ 'brand' ] = json_encode($request->brand);
        }

        if ($request->category[ 0 ] != null && $request->category[ 0 ] != 1) {
            if ($request->brand[ 0 ] != null) {
                $products = $products->whereIn('category', $request->category);
            } else {
                $products = Product::whereIn('category', $request->category);
            }

            $params[ 'category' ] = json_encode($request->category);
        }

        if ($request->size[ 0 ] != null) {
            if ($request->brand[ 0 ] != null || ($request->category[ 0 ] != 1 && $request->category[ 0 ] != null)) {
                $products = $products->where(function ($query) use ($request) {
                    foreach ($request->size as $size) {
                        $query->orWhere('size', 'LIKE', "%$size%");
                    }

                    return $query;
                });
            } else {
                $products = Product::where(function ($query) use ($request) {
                    foreach ($request->size as $size) {
                        $query->orWhere('size', 'LIKE', "%$size%");
                    }

                    return $query;
                });
            }

            $params[ 'size' ] = json_encode($request->size);
        }

        if ($request->supplier[ 0 ] != null) {
            if ($request->brand[ 0 ] != null || ($request->category[ 0 ] != 1 && $request->category[ 0 ] != null) || $request->size[ 0 ] != null) {
                $products = $products->whereHas('suppliers', function ($query) use ($request) {
                    return $query->where(function ($q) use ($request) {
                        foreach ($request->supplier as $supplier) {
                            $q->orWhere('suppliers.id', $supplier);
                        }
                    });
                });
            } else {
                $products = Product::whereHas('suppliers', function ($query) use ($request) {
                    return $query->where(function ($q) use ($request) {
                        foreach ($request->supplier as $supplier) {
                            $q->orWhere('suppliers.id', $supplier);
                        }
                    });
                });
            }

            $params[ 'supplier' ] = json_encode($request->supplier);
        }

        if ($request->brand[ 0 ] == null && ($request->category[ 0 ] == 1 || $request->category[ 0 ] == null) && $request->size[ 0 ] == null && $request->supplier[ 0 ] == null) {
            $products = (new Product)->newQuery();
        }

        $price = explode(',', $request->get('price'));

        $products = $products->whereBetween('price_special', [$price[ 0 ], $price[ 1 ]]);

        $products = $products->where('is_scraped', 1)->where('category', '!=', 1)->latest()->take($request->number)->get();

        if ($customer->suggestion) {
            $suggestion = Suggestion::find($customer->suggestion->id);
            $suggestion->update($params);
        } else {
            $suggestion = Suggestion::create($params);
        }

        if (count($products) > 0) {
            $params = [
                'number' => null,
                'user_id' => Auth::id(),
                'approved' => 0,
                'status' => 1,
                'message' => 'Suggested images',
                'customer_id' => $customer->id
            ];

            $count = 0;

            foreach ($products as $product) {
                if (!$product->suggestions->contains($suggestion->id)) {
                    if ($image = $product->getMedia(config('constants.media_tags'))->first()) {
                        if ($count == 0) {
                            $chat_message = ChatMessage::create($params);
                        }

                        $chat_message->attachMedia($image->getKey(), config('constants.media_tags'));
                        $count++;
                    }

                    $product->suggestions()->attach($suggestion->id);
                }
            }
        }

        return redirect()->route('customer.show', $customer->id)->withSuccess('You have successfully created suggested message');
    }

    public function sendScraped(Request $request)
    {
        $customer = Customer::find($request->customer_id);

        if ($request->brand[ 0 ] != null) {
            $products = Product::whereIn('brand', $request->brand);
        }


        if ($request->category[ 0 ] != null && $request->category[ 0 ] != 1) {
            if ($request->brand[ 0 ] != null) {
                $products = $products->whereIn('category', $request->category);
            } else {
                $products = Product::whereIn('category', $request->category);
            }
        }

        if ($request->brand[ 0 ] == null && ($request->category[ 0 ] == 1 || $request->category[ 0 ] == null)) {
            $products = (new Product)->newQuery();
        }

        $products = $products->where('is_scraped', 1)->where('is_without_image', 0)->where('category', '!=', 1)->orderBy(DB::raw('products.created_   at'), 'DESC')->take(20)->get();
        if (count($products) > 0) {
            $params = [
                'number' => null,
                'user_id' => Auth::id(),
                'approved' => 0,
                'status' => 1,
                'message' => 'Suggested images',
                'customer_id' => $customer->id
            ];

            $count = 0;

            foreach ($products as $product) {
                if ($image = $product->getMedia(config('constants.media_tags'))->first()) {
                    if ($count == 0) {
                        $chat_message = ChatMessage::create($params);
                    }

                    $chat_message->attachMedia($image->getKey(), config('constants.media_tags'));
                    $count++;
                }
            }
        }

        if ($request->ajax()) {
            return response('success');
        }

        return redirect()->route('customer.show', $customer->id)->withSuccess('You have successfully created suggested message');
    }

    public function attachAll(Request $request)
    {
        $data = [];
        $term = $request->input('term');
        $roletype = $request->input('roletype');
        $model_type = $request->input('model_type');

        $data[ 'term' ] = $term;
        $data[ 'roletype' ] = $roletype;

        $doSelection = $request->input('doSelection');

        if (!empty($doSelection)) {

            $data[ 'doSelection' ] = true;
            $data[ 'model_id' ] = $request->input('model_id');
            $data[ 'model_type' ] = $request->input('model_type');

            $data[ 'selected_products' ] = ProductController::getSelectedProducts($data[ 'model_type' ], $data[ 'model_id' ]);
        }

        if ($request->brand[ 0 ] != null) {
            $productQuery = (new Product())->newQuery()
                ->latest()->whereIn('brand', $request->brand);

        }

        if ($request->color[ 0 ] != null) {
            if ($request->brand[ 0 ] != null) {
                $productQuery = $productQuery->whereIn('color', $request->color);
            } else {
                $productQuery = (new Product())->newQuery()
                    ->latest()->whereIn('color', $request->color);
            }
        }

        if ($request->category[ 0 ] != null && $request->category[ 0 ] != 1) {
            $category_children = [];

            foreach ($request->category as $category) {
                $is_parent = Category::isParent($category);

                if ($is_parent) {
                    $childs = Category::find($category)->childs()->get();

                    foreach ($childs as $child) {
                        $is_parent = Category::isParent($child->id);

                        if ($is_parent) {
                            $children = Category::find($child->id)->childs()->get();

                            foreach ($children as $chili) {
                                array_push($category_children, $chili->id);
                            }
                        } else {
                            array_push($category_children, $child->id);
                        }
                    }
                } else {
                    array_push($category_children, $category);
                }
            }

            if ($request->brand[ 0 ] != null || $request->color[ 0 ] != null) {
                $productQuery = $productQuery->whereIn('category', $category_children);
            } else {
                $productQuery = (new Product())->newQuery()
                    ->latest()->whereIn('category', $category_children);
            }
        }

        if ($request->price != null) {
            $exploded = explode(',', $request->price);
            $min = $exploded[ 0 ];
            $max = $exploded[ 1 ];

            if ($min != '0' || $max != '400000') {
                if ($request->brand[ 0 ] != null || $request->color[ 0 ] != null || ($request->category[ 0 ] != null && $request->category[ 0 ] != 1)) {
                    $productQuery = $productQuery->whereBetween('price_special', [$min, $max]);
                } else {
                    $productQuery = (new Product())->newQuery()
                        ->latest()->whereBetween('price_special', [$min, $max]);
                }
            }
        }

        if ($request->supplier[ 0 ] != null) {
            $suppliers_list = implode(',', $request->supplier);

            if ($request->brand[ 0 ] != null || $request->color[ 0 ] != null || ($request->category[ 0 ] != null && $request->category[ 0 ] != 1) || $request->price != "0,400000") {
                $productQuery = $productQuery->with('Suppliers')->whereRaw("products.id in (SELECT product_id FROM product_suppliers WHERE supplier_id IN ($suppliers_list))");
            } else {
                $productQuery = (new Product())->newQuery()->with('Suppliers')
                    ->latest()->whereRaw("products.id IN (SELECT product_id FROM product_suppliers WHERE supplier_id IN ($suppliers_list))");
            }
        }

        if (trim($request->size) != '') {
            if ($request->brand[ 0 ] != null || $request->color[ 0 ] != null || ($request->category[ 0 ] != null && $request->category[ 0 ] != 1) || $request->price != "0,400000" || $request->supplier[ 0 ] != null) {
                $productQuery = $productQuery->whereNotNull('size')->where('size', 'LIKE', "%$request->size%");
            } else {
                $productQuery = (new Product())->newQuery()
                    ->latest()->whereNotNull('size')->where('size', 'LIKE', "%$request->size%");
            }
        }

        if ($request->location[ 0 ] != null) {
            if ($request->brand[ 0 ] != null || $request->color[ 0 ] != null || ($request->category[ 0 ] != null && $request->category[ 0 ] != 1) || $request->price != "0,400000" || $request->supplier[ 0 ] != null || trim($request->size) != '') {
                $productQuery = $productQuery->whereIn('location', $request->location);
            } else {
                $productQuery = (new Product())->newQuery()
                    ->latest()->whereIn('location', $request->location);
            }

            $data[ 'location' ] = $request->location[ 0 ];
        }

        if ($request->type[ 0 ] != null) {
            if ($request->brand[ 0 ] != null || $request->color[ 0 ] != null || ($request->category[ 0 ] != null && $request->category[ 0 ] != 1) || $request->price != "0,400000" || $request->supplier[ 0 ] != null || trim($request->size) != '' || $request->location[ 0 ] != null) {
                if (count($request->type) > 1) {
                    $productQuery = $productQuery->where('is_scraped', 1)->orWhere('status', 2);
                } else {
                    if ($request->type[ 0 ] == 'scraped') {
                        $productQuery = $productQuery->where('is_scraped', 1);
                    } elseif ($request->type[ 0 ] == 'imported') {
                        $productQuery = $productQuery->where('status', 2);
                    } else {
                        $productQuery = $productQuery->where('isUploaded', 1);
                    }
                }
            } else {
                if (count($request->type) > 1) {
                    $productQuery = (new Product())->newQuery()
                        ->latest()->where('is_scraped', 1)->orWhere('status', 2);
                } else {
                    if ($request->type[ 0 ] == 'scraped') {
                        $productQuery = (new Product())->newQuery()
                            ->latest()->where('is_scraped', 1);
                    } elseif ($request->type[ 0 ] == 'imported') {
                        $productQuery = (new Product())->newQuery()
                            ->latest()->where('status', 2);
                    } else {
                        $productQuery = (new Product())->newQuery()
                            ->latest()->where('isUploaded', 1);
                    }
                }
            }

            $data[ 'type' ] = $request->type[ 0 ];
        }

        if ($request->date != '') {
            if ($request->brand[ 0 ] != null || $request->color[ 0 ] != null || ($request->category[ 0 ] != null && $request->category[ 0 ] != 1) || $request->price != "0,400000" || $request->supplier[ 0 ] != null || trim($request->size) != '' || $request->location[ 0 ] != null || $request->type[ 0 ] != null) {
                if ($request->type[ 0 ] != null && $request->type[ 0 ] == 'uploaded') {
                    $productQuery = $productQuery->where('is_uploaded_date', 'LIKE', "%$request->date%");
                } else {
                    $productQuery = $productQuery->where('created_at', 'LIKE', "%$request->date%");
                }
            } else {
                $productQuery = (new Product())->newQuery()
                    ->latest()->where('created_at', 'LIKE', "%$request->date%");
            }
        }

        if ($request->quick_product === 'true') {
            $productQuery = (new Product())->newQuery()
                ->latest()->where('quick_product', 1);
        }

        if (trim($term) != '') {
            $productQuery = (new Product())->newQuery()
                ->latest()
                ->orWhere('sku', 'LIKE', "%$term%")
                ->orWhere('id', 'LIKE', "%$term%")//		                                 ->orWhere( 'category', $term )
            ;

            if ($term == -1) {
                $productQuery = $productQuery->orWhere('isApproved', -1);
            }

            if (Brand::where('name', 'LIKE', "%$term%")->first()) {
                $brand_id = Brand::where('name', 'LIKE', "%$term%")->first()->id;
                $productQuery = $productQuery->orWhere('brand', 'LIKE', "%$brand_id%");
            }

            if ($category = Category::where('title', 'LIKE', "%$term%")->first()) {
                $category_id = $category = Category::where('title', 'LIKE', "%$term%")->first()->id;
                $productQuery = $productQuery->orWhere('category', CategoryController::getCategoryIdByName($term));
            }

            if (!empty($stage->getIDCaseInsensitive($term))) {

                $productQuery = $productQuery->orWhere('stage', $stage->getIDCaseInsensitive($term));
            }

            if (!(\Auth::user()->hasRole(['Admin', 'Supervisors']))) {

                $productQuery = $productQuery->where('stage', '>=', $stage->get($roletype));
            }

            if ($roletype != 'Selection' && $roletype != 'Searcher') {

                $productQuery = $productQuery->whereNull('dnf');
            }
        } else {
            if ($request->brand[ 0 ] == null && $request->color[ 0 ] == null && ($request->category[ 0 ] == null || $request->category[ 0 ] == 1) && $request->price == "0,400000" && $request->supplier[ 0 ] == null && trim($request->size) == '' && $request->date == '' && $request->type == null && $request->location[ 0 ] == null) {
                $productQuery = (new Product())->newQuery()->latest();
            }
        }

        if ($request->ids[ 0 ] != null) {
            $productQuery = (new Product())->newQuery()
                ->latest()->whereIn('id', $request->ids);
        }

        $data[ 'products' ] = $productQuery->select(['id', 'sku', 'size', 'price_special', 'brand', 'supplier', 'isApproved', 'stage', 'status', 'is_scraped', 'created_at'])->get();

        $params = [
            'user_id' => Auth::id(),
            'number' => null,
            'status' => 1,
            'customer_id' => $request->customer_id
        ];

        $chat_message = ChatMessage::create($params);

        foreach ($data[ 'products' ] as $product) {
            if ($product->hasMedia(config('constants.media_tags'))) {
                $chat_message->attachMedia($product->getMedia(config('constants.media_tags'))->first(), config('constants.media_tags'));
            }
        }

        return redirect()->route('customer.show', $request->customer_id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer = Customer::find($id);

        if (count($customer->leads) > 0 || count($customer->orders) > 0) {
            return redirect()->route('customer.index')->with('warning', 'You have related leads or orders to this customer');
        }

        $customer->delete();

        return redirect()->route('customer.index')->with('success', 'You have successfully deleted a customer');
    }
}
