<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use NagadAPI\Nagad;


class NagadController extends Controller
{

    public function test()
    {
        print_r('test');
    }
    public function createPayment() 
    {
        /**
         * Method 1: Quickest
         * This will automatically redirect you to the Nagad PG Page
         * */

        return Nagad::setOrderID('ORDERID123')
            ->setAmount('540')
            ->checkout()
            ->redirect();
    }
    

	//To receive the callback response use this method: 

    /**
     * This is the routed callback method
     * which receives a GET request.
     * 
     * */

    public function callback(Request $request)
    {
        $verified = Nagad::callback($request)->verify();
        if($verified->success()) {

            // Get Additional Data
            dd($verified->getAdditionalData());
            
            // Get Full Response
            dd($verified->getVerifiedResponse());
        } else {
            dd($verified->getErrors());
        }
    }
}
