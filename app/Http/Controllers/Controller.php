<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


/**
 * @SWG\Swagger(
 *	 schemes={"http" , "https"},
 *   basePath="/api" ,
 *   @SWG\Info(
 *     title="API Documentation",
 *     version="1.0",
 *	   
 *     description="This API documentation list all API endpoints available in applicaiton. Documentation also list Modules and variable available in each Modules",
 *     @SWG\Contact(
 *         email="xyz@xyz.com"
 *     )
 *   )
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
