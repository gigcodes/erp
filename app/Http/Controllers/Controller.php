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
 *     title="API Live Test",
 *     version="1.0",
 *	   
 *     description="This is api testing",
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
