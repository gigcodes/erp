<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Benchmark;
use App\User;
use App\Leads;
use App\Order;
use App\Product;
use App\ScrapedProducts;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ActivityConroller extends Controller {


	private $dataLabelDay = [
		"12:00 am",
		"1:00 am",
		"2:00 am",
		"3:00 am",
		"4:00 am",
		"5:00 am",
		"6:00 am",
		"7:00 am",
		"8:00 am",
		"9:00 am",
		"10:00 am",
		"11:00 am",
		"12:00 pm",
		"1:00 pm",
		"2:00 pm",
		"3:00 pm",
		"4:00 pm",
		"5:00 pm",
		"6:00 pm",
		"7:00 pm",
		"8:00 pm",
		"9:00 pm",
		"10:00 pm",
		"11:00 pm"
	];

	private $dataLabelMonth = [
		1,
		2,
		3,
		4,
		5,
		6,
		7,
		8,
		9,
		10,
		11,
		12,
		13,
		14,
		15,
		16,
		17,
		18,
		19,
		20,
		21,
		22,
		23,
		24,
		25,
		26,
		27,
		28,
		29,
		30,
		31
	];

	public function __construct() {

		$this->middleware( 'permission:view-activity', [ 'only' => [ 'index', 'store' ] ] );
	}

	public function showActivity( Request $request ) {

		$data['users']         = $this->getUserArray();
		$data['selected_user'] = $request->input( 'selected_user' );
		$data['type']          = [
			'selection'    => 'Selection',
			'searcher'     => 'Searcher',
			'attribute'    => 'Attribute',
			'supervisor'   => 'Supervisor',
			'imagecropper' => 'Imagecropper',
			'lister'       => 'Lister',
			'approver'     => 'Approver',
			'inventory'    => 'Inventory',
			'sales'        => 'Sales',
		];

		$data['range_start'] = $request->input( 'range_start' );
		$data['range_end']   = $request->input( 'range_end' );


		if ( ! $request->has( 'range_start' ) && ! $request->has( 'range_end' ) ) {
			$end   = date( 'Y-m-d H:i:s' );
			$start = date( 'Y-m-d H:i:s', strtotime( '-7 Days', strtotime( $end ) ) );
		} else {
			$start = $request->input( 'range_start' ) . " 00:00:00.000000";
			$end   = $request->input( 'range_end' ) . " 23:59:59.000000";
		}

		if ( $request->has( 'selected_user' ) ) {
			$users = implode( ',', $request->input( 'selected_user' ) );

			$results = DB::select( '
									SELECT causer_id,subject_type,COUNT(*) AS total FROM
								 		(SELECT DISTINCT activities.subject_id,activities.subject_type,activities.causer_id
								  		 FROM activities
								  		 WHERE activities.description = "create"
								  		 AND activities.causer_id IN (' . $users . ')
								  		 AND activities.created_at BETWEEN ? AND ?)
								    AS SUBQUERY
								   	GROUP BY subject_type,causer_id;
							', [ $start, $end ] );

			$results2 = DB::select( '
									SELECT subject_type,COUNT(*) AS total FROM
								 		(SELECT DISTINCT activities.subject_id,activities.subject_type
								  		 FROM activities
								  		 WHERE activities.description = "create"
								  		 AND activities.causer_id IN (' . $users . ')
								  		 AND activities.created_at BETWEEN ? AND ?)
								    AS SUBQUERY
								   	GROUP BY subject_type;
							', [ $start, $end ] );

		} else {

			$results = DB::select( '
									SELECT causer_id,subject_type,COUNT(*) AS total FROM
								 		(SELECT DISTINCT activities.subject_id,activities.subject_type,activities.causer_id
								  		 FROM activities
								  		 WHERE activities.description = "create"
								  		 AND activities.created_at BETWEEN ? AND ?)
								    AS SUBQUERY
								   	GROUP BY subject_type,causer_id;
							', [ $start, $end ] );


			$results2 = DB::select( '
									SELECT subject_type,COUNT(*) AS total FROM
								 		(SELECT DISTINCT activities.subject_id,activities.subject_type
								  		 FROM activities
								  		 WHERE activities.description = "create"
								  		 AND activities.created_at BETWEEN ? AND ?)
								    AS SUBQUERY
								   	GROUP BY subject_type;
							', [ $start, $end ] );
		}



		// $benchmark2 = Benchmark::whereBetween('for_date', [$start, $end])->get();
		//
		// // dd();
		//
		// $day_difference = Carbon::parse($end)->diffInDays(Carbon::parse($start));
		//
		// if ($day_difference != $benchmark2->count()) {
		// 	if ($benchmark2->count() == 0) {
		// 		$benchmark_last = Benchmark::orderBy('for_date', 'DESC')->first();
		// 		$benchmark[0]['selections'] = $benchmark_last->selections * $day_difference;
		// 		$benchmark[0]['searches'] = $benchmark_last->searches * $day_difference;
		// 		$benchmark[0]['attributes'] = $benchmark_last->attributes * $day_difference;
		// 		$benchmark[0]['supervisor'] = $benchmark_last->supervisor * $day_difference;
		// 		$benchmark[0]['imagecropper'] = $benchmark_last->imagecropper * $day_difference;
		// 		$benchmark[0]['lister'] = $benchmark_last->lister * $day_difference;
		// 		$benchmark[0]['approver'] = $benchmark_last->approver * $day_difference;
		// 		$benchmark[0]['inventory'] = $benchmark_last->inventory * $day_difference;
		// 	} else {
		//
		// 	}
		// } else {
			$benchmark = Benchmark::whereBetween( 'for_date', [ $start, $end ] )
			                      ->selectRaw( 'sum(selections) as selections,
			                                             sum(searches) as searches,
			                                             sum(attributes) as attributes,
			                                             sum(supervisor) as supervisor,
			                                             sum(imagecropper) as imagecropper,
			                                             sum(lister) as lister,
			                                             sum(approver) as approver,
			                                             sum(inventory) as inventory' )
			                      ->get()->toArray();
		// }
		// dd('stap');
		$rows      = [];

		foreach ( $results as $result ) {

			$rows[ $result->causer_id ][ $result->subject_type ] = $result->total;
		}

		$total_data = [];

		$total_data['selection']    = 0;
		$total_data['searcher']     = 0;
		$total_data['attribute']    = 0;
		$total_data['supervisor']   = 0;
		$total_data['imagecropper'] = 0;
		$total_data['lister']       = 0;
		$total_data['approver']     = 0;
		$total_data['inventory']    = 0;
		$total_data['sales']        = 0;

		foreach ( $results2 as $result ) {
			$total_data[ $result->subject_type ] += $result->total;
		}

		$data['results']    = $rows;
		$data['total_data'] = $total_data;
		$data['benchmark']  = $benchmark[0];

		$leads = Leads::where('created_at', '>=', date('Y-m-d 00:00:00'))->get()->count();
		$orders = Order::where('created_at', '>=', date('Y-m-d 00:00:00'))->get()->count();

		$data['leads'] = $leads;
		$data['orders'] = $orders;

		$data['scraped_gnb_count'] = ScrapedProducts::where('website', 'G&B')->whereBetween('created_at', [$start, $end])->get()->count();
		$data['scraped_wise_count'] = ScrapedProducts::where('website', 'Wiseboutique')->whereBetween('created_at', [$start, $end])->get()->count();
		$data['scraped_double_count'] = ScrapedProducts::where('website', 'DoubleF')->whereBetween('created_at', [$start, $end])->get()->count();

		// $data['scraped_gnb_product_count'] = ScrapedProducts::with('Product')->where('website', 'G&B')->whereHas('Product')->get();
		// $data['scraped_wise_product_count'] = ScrapedProducts::with('Product')->where('website', 'Wiseboutique')->whereHas('Product')->get()->count();
		// $data['scraped_double_product_count'] = ScrapedProducts::with('Product')->where('website', 'DoubleF')->whereHas('Product')->get()->count();

		$data['scraped_gnb_product_count'] = Product::where('supplier', 'G & B Negozionline')->where('is_scraped', 1)->whereBetween('created_at', [$start, $end])->get()->count();
		$data['scraped_wise_product_count'] = Product::where('supplier', 'Wise Boutique')->where('is_scraped', 1)->whereBetween('created_at', [$start, $end])->get()->count();
		$data['scraped_double_product_count'] = Product::where('supplier', 'Double F')->where('is_scraped', 1)->whereBetween('created_at', [$start, $end])->get()->count();

		$data['import_created_product_count'] = Product::where('status', 2)->whereBetween('import_date', [$start, $end])->get()->count();
		$data['import_updated_product_count'] = Product::where('status', 3)->whereBetween('import_date', [$start, $end])->get()->count();
		$data['import_total_created_product_count'] = Product::where('status', 2)->get()->count();
		$data['import_total_updated_product_count'] = Product::where('status', 3)->get()->count();

		return view( 'activity.index', $data );
	}

	public function showGraph( Request $request ) {

//		return $request->all();

		$data['date_type'] = $request->input( 'date_type' ) ?? 'week';

		$data['week_range']  = $request->input( 'week_range' ) ?? date( 'Y-\WW' );
		$data['month_range'] = $request->input( 'month_range' ) ?? date( 'Y-m' );

		if ( $data['date_type'] == 'week' ) {

			$weekRange = $this->getStartAndEndDateByWeek( $data['week_range'] );
			$start     = $weekRange['start_date'];
			$end       = $weekRange['end_date'];

			$workDoneResult = DB::select( '
									SELECT WEEKDAY(created_at) as xaxis ,COUNT(*) AS total FROM
								 		(SELECT DISTINCT activities.subject_id,activities.subject_type,activities.created_at
								  		 FROM activities
								  		 WHERE activities.description = "create"
								  		 AND activities.created_at BETWEEN ? AND ?)
								    AS SUBQUERY
								   	GROUP BY WEEKDAY(created_at);
							', [ $start, $end ] );

			$benchmarkResult = DB::select( '
							SELECT WEEKDAY(for_date) as day,
								sum(selections + searches + attributes + supervisor + imagecropper + lister + approver + inventory) as total
							FROM benchmarks
							WHERE
							created_at BETWEEN ? AND ?
							GROUP BY WEEKDAY(for_date);
						', [ $start, $end ] );

			$workDone = [];
			$dowMap   = array( 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' );

			foreach ( $workDoneResult as $item ) {
				$workDone[ $dowMap[ $item->xaxis ] ] = $item->total;
			}

			$benchmark = [];

			foreach ( $benchmarkResult as $item ) {
				$benchmark[ $dowMap[ $item->day ] ] = $item->total;
			}

		} else {

			$monthRange = $this->getStartAndEndDateByMonth( $data['month_range'] );
			$start      = $monthRange['start_date'];
			$end        = $monthRange['end_date'];

			$workDoneResult = DB::select( '
									SELECT DAYOFMONTH(created_at) as xaxis ,COUNT(*) AS total FROM
								 		(SELECT DISTINCT activities.subject_id,activities.subject_type,activities.created_at
								  		 FROM activities
								  		 WHERE activities.description = "create"
								  		 AND activities.created_at BETWEEN ? AND ?)
								    AS SUBQUERY
								   	GROUP BY DAYOFMONTH(created_at);
							', [ $start, $end ] );

			$benchmarkResult = DB::select( '
							SELECT DAYOFMONTH(for_date) as day,
								sum(selections + searches + attributes + supervisor + imagecropper + lister + approver + inventory) as total
							FROM benchmarks
							WHERE
							created_at BETWEEN ? AND ?
							GROUP BY DAYOFMONTH(for_date);
						', [ $start, $end ] );

			foreach ( $workDoneResult as $item ) {
				$workDone[ $item->xaxis ] = $item->total;
			}

			foreach ( $benchmarkResult as $item ) {
				$benchmark[ $item->day ] = $item->total;
			}
		}


//		return $benchmark;

		$data['benchmark'] = $benchmark ?? [];
		$data['workDone']  = $workDone ?? [];

		return view( 'activity.graph', $data );

	}

	public function showUserGraph( Request $request ) {

//		return $request->all();

		$data['users']         = $this->getUserArray();
		$data['selected_user'] = $request->input( 'selected_user' ) ?? 3;

		$data['date_type'] = $request->input( 'date_type' ) ?? 'day';

		$data['day_range']   = $request->input( 'day_range' ) ?? date( 'Y-m-d' );
		$data['month_range'] = $request->input( 'month_range' ) ?? date( 'Y-m' );

		if ( $data['date_type'] == 'day' ) {


			$start = $data['day_range'] . " 00:00:00.000000";
			$end   = $data['day_range'] . " 23:59:59.000000";

			$workDoneResult = DB::select( '
									SELECT HOUR(created_at) as xaxis,subject_type ,COUNT(*) AS total FROM
								 		(SELECT DISTINCT activities.subject_id,activities.subject_type,activities.created_at
								  		 FROM activities
								  		 WHERE activities.description = "create"
								  		 AND activities.causer_id = ?
								  		 AND activities.created_at BETWEEN ? AND ?)
								    AS SUBQUERY
								   	GROUP BY HOUR(created_at),subject_type ORDER By xaxis;
							', [ $data['selected_user'], $start, $end ] );

			$workDone = [];

			foreach ( $workDoneResult as $item ) {
				$workDone[ $item->subject_type ][ $item->xaxis ] = $item->total;
			}


			foreach ( $workDone as $subject_type => $subject_type_array ) {

				for ( $i = 0; $i <= 23; $i ++ ) {
					$workDone[ $subject_type ][ $i ] = $subject_type_array[ $i ] ?? 0;
//					$workDone[ $subject_type ][gmdate("g:i a",$i*3600 )] = $subject_type_array[$i] ?? 0;
				}
			}

		} else {

			$monthRange = $this->getStartAndEndDateByMonth( $data['month_range'] );
			$start      = $monthRange['start_date'];
			$end        = $monthRange['end_date'];

			$workDoneResult = DB::select( '
									SELECT DAYOFMONTH(created_at) as xaxis,subject_type ,COUNT(*) AS total FROM
								 		(SELECT DISTINCT activities.subject_id,activities.subject_type,activities.created_at
								  		 FROM activities
								  		 WHERE activities.description = "create"
								  		 AND activities.causer_id = ?
								  		 AND activities.created_at BETWEEN ? AND ?)
								    AS SUBQUERY
								   	GROUP BY DAYOFMONTH(created_at),subject_type ORDER By xaxis;
							', [ $data['selected_user'], $start, $end ] );

			$workDone = [];

			foreach ( $workDoneResult as $item ) {
				$workDone[ $item->subject_type ][ $item->xaxis ] = $item->total;
			}


			foreach ( $workDone as $subject_type => $subject_type_array ) {

				for ( $i = 1; $i <= 31; $i ++ ) {

					$workDone[ $subject_type ][ $i ] = $subject_type_array[ $i ] ?? 0;
				}
			}
		}

		$data['workDone']  = $workDone ?? [];
		$data['dataLabel'] = $data['date_type'] == 'day' ? $this->dataLabelDay : $this->dataLabelMonth;
//		return $data;

		return view( 'activity.graph-user', $data );

	}

	public function getUserArray() {

		$users = User::all();

		$userArray = [];

		foreach ( $users as $user ) {

			$userArray[ ( (string) $user->id ) ] = $user->name;
		}

		return $userArray;
	}


	public static function create( $subject_id, $subject_type, $description ) {

		$activity = new Activity();

		$activity->create( [
			'subject_id'   => $subject_id,
			'subject_type' => $subject_type,
			'causer_id'    => \Auth::id() ?? 0,
			'description'  => $description
		] );

	}


	function getStartAndEndDateByWeek( $week_range ) {

		$arr = explode( '-', $week_range );

		$week = str_replace( 'W', '', $arr[1] );
		$year = $arr[0];

		$dateTime = new \DateTime();
		$dateTime->setISODate( $year, $week );
		$result['start_date'] = $dateTime->format( 'Y-m-d' ) . " 00:00:00.000000";
		$dateTime->modify( '+6 days' );
		$result['end_date'] = $dateTime->format( 'Y-m-d' ) . " 23:59:59.000000";

		return $result;
	}

	function getStartAndEndDateByMonth( $month_range ) {

		$arr = explode( '-', $month_range );

		$year  = $arr[0];
		$month = $arr[1];

		$dateTime = new \DateTime();
		$dateTime->setDate( $year, $month, 1 );
		$result['start_date'] = $dateTime->format( 'Y-m-d' ) . " 00:00:00.000000";
		$dateTime->modify( '+1 month' );
		$dateTime->modify( '-1 days' );
		$result['end_date'] = $dateTime->format( 'Y-m-d' ) . " 23:59:59.000000";

		return $result;
	}

}


/*$total_data['selection']    += isset( $results['selection'] ) ? $results['selection'] : 0;
		$total_data['searcher']     += isset( $results['searcher'] ) ? $results['searcher'] : 0;
		$total_data['attribute']    += isset( $results['attribute'] ) ? $results['attribute'] : 0;
		$total_data['supervisor']   += isset( $results['supervisor'] ) ? $results['supervisor'] : 0;
		$total_data['imagecropper'] += isset( $results['imagecropper'] ) ? $results['imagecropper'] : 0;
		$total_data['lister']       += isset( $results['lister'] ) ? $results['lister'] : 0;
		$total_data['approver']     += isset( $results['approver'] ) ? $results['approver'] : 0;
		$total_data['inventory']    += isset( $results['inventory'] ) ? $results['inventory'] : 0;
		$total_data['sales']        += isset( $results['sales'] ) ? $results['sales'] : 0;*/
