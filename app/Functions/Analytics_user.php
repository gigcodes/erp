<?php

// Load the Google API PHP Client Library.
require_once __DIR__ . '/../../vendor/autoload.php';
$data      = [];
$analytics = initializeAnalytics();

if (!empty($analytics)) {
    // $response = getReport($analytics, $request = '');
    // $data     = printResults($response);
}


/**
 * Initializes an Analytics Reporting API V4 service object.
 *
 * @return An authorized Analytics Reporting API V4 service object.
 */
function initializeAnalytics()
{

    // Use the developers console and download your service account
    // credentials in JSON format. Place them in this directory or
    // change the key file location if necessary.
    $KEY_FILE_LOCATION = storage_path('app/analytics/sololuxu-7674c35e7be5.json');
    $analytics         = '';
    if (file_exists($KEY_FILE_LOCATION)) {
        // Create and configure a new client object.
        $client = new Google_Client();
        // $client->setApplicationName("Hello Analytics Reporting");
        $client->setAuthConfig($KEY_FILE_LOCATION);
        $client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
        $analytics = new Google_Service_AnalyticsReporting($client);
    }
    return $analytics;
}

/**
 * Queries the Analytics Reporting API V4.
 *
 * @param service An authorized Analytics Reporting API V4 service object.
 * @return The Analytics Reporting API V4 response.
 */
function getReportRequest($analytics, $request)
{
    // Replace with your view ID, for example XXXX.
    if (isset($request['view_id'])) {
        $view_id = (string) $request['view_id'];
    } else {
        $view_id = env('ANALYTICS_VIEW_ID');
    }

    if(!empty($request)){
        $analytics = '';
        if(isset($request['google_service_account_json']) && $request['google_service_account_json'] != ''){
            $websiteKeyFile = base_path('resources/assets/analytics_files/'.$request['google_service_account_json']);
        }else{
            $websiteKeyFile = storage_path('app/analytics/sololuxu-7674c35e7be5.json');
        }
        if (file_exists($websiteKeyFile)) {
            $client = new Google_Client();
            $client->setAuthConfig($websiteKeyFile);
            $client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
            $analytics = new Google_Service_AnalyticsReporting($client);
        }
    }

    // Create the DateRange object.
    $dateRange = new Google_Service_AnalyticsReporting_DateRange();
    // $dateRange->setStartDate(!empty($request) && !empty($request['start_date']) ? $request['start_date'] : "28DaysAgo");
    $dateRange->setStartDate(!empty($request) && !empty($request['start_date']) ? $request['start_date'] : '2021-02-01');
    //$dateRange->setEndDate(!empty($request) && !empty($request['end_date']) ? $request['end_date'] : "1DaysAgo");
    $dateRange->setEndDate(!empty($request) && !empty($request['end_date']) ? $request['end_date'] : '2021-04-01');

    // Create the ReportRequest object.
    $request = new Google_Service_AnalyticsReporting_ReportRequest();
    $request->setViewId($view_id);
    $request->setDateRanges($dateRange);

    return array('requestObj' => $request,'analyticsObj' => $analytics);
}

function getDimensionWiseData( $analytics, $request, $GaDimension ){

    // Create the Dimensions object.
    $dimension = new Google_Service_AnalyticsReporting_Dimension();
    $dimension->setName($GaDimension);

    $request->setDimensions(array( $dimension ));

    // Create the Metrics object.
    // $metric = new Google_Service_AnalyticsReporting_Metric();
    // $metric->setExpression("ga:avgTimeOnPage");
    // $metric->setAlias("avgTimeOnPage");
    // $request->setMetrics(array($metric));

    $request->setDimensions(array( $dimension ));

    $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
    $body->setReportRequests(array($request));
    return $analytics->reports->batchGet($body);
}

/**
 * Parses and prints the Analytics Reporting API V4 response.
 *
 * @param An Analytics Reporting API V4 response.
 */
function printResults($reports, $websiteAnalyticsId)
{    
    // dump( $reports );
    for ( $reportIndex = 0; $reportIndex < $reports->count(); $reportIndex++ ) {
        
        $report           = $reports[$reportIndex];
        $header           = $report->getColumnHeader();
        $dimensionHeaders = $header->getDimensions();
        $metricHeaders    = $header->getMetricHeader()->getMetricHeaderEntries();
        $rows             = $report->getData()->getRows();

        for ( $rowIndex = 0; $rowIndex < count($rows); $rowIndex++) {
            $row        = $rows[ $rowIndex ];
            $dimensions = $row->getDimensions();
            $metrics    = $row->getMetrics();

            for ($i = 0; $i < count($dimensionHeaders) && $i < count($dimensions); $i++) {
                    $data[$rowIndex]['dimensions']      = str_replace('ga:', '', $dimensionHeaders[$i]);
                    $data[$rowIndex]['dimensions_name'] = $dimensions[$i];
                    $data[$rowIndex]['website_analytics_id'] = $websiteAnalyticsId;
            }

            for ($j = 0; $j < count($metrics); $j++) {
                $values = $metrics[$j]->getValues();
                for ($k = 0; $k < count($values); $k++) {
                    $entry = $metricHeaders[$k];
                    $data[$rowIndex]['dimensions_value'] = $values[$k];
                    // $data[$rowIndex]['dimensions_value_type'] = $entry->getName();
                }
            }
        }
        
        if (!empty($data)) {
            return $data;
        } else {
            return;
        }

    }
}
