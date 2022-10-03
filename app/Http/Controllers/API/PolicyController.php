<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PolicyResource;
use App\Http\Controllers\Controller;

/** Live  table */
//use App\Models\OnlinePayment;

/** Test  table */
use App\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class PolicyController extends Controller
{

   /* request for policy status and amount check */
   public function info(Request $request)
   {
    $polInfo = $request->all();
        // $validator = Validator::make($polInfo, [
        //     'policy_no' => 'required'
        // ]);

        // if ($validator->fails()) {
        //     return response(['error' => $validator->errors(), 'Validation Error']);
        // }
        $polNo = $polInfo['policy_no'];

             $policies = DB::select('SELECT POLICY_NO,MATURITY, INSTPREM AS AMOUNT, STATUS AS POLICY_STATUS, PROPOSER, DOB, MOBILE,nextprem 
                FROM POLICY.POLICY_ALL  WHERE STATUS = 1
                AND MATURITY>SYSDATE
                AND NEXTPREM+30>SYSDATE
                AND POLICY_NO = :policy_no', ['POLICY_NO' => $polNo]);

     //$policies = DB::select('SELECT POLICY_NO, INSTPREM AS AMOUNT, STATUS AS POLICY_STATUS, PROPOSER, DOB, MOBILE from POLICY.POLICY_ALL WHERE STATUS = 1 AND POLICY_NO = :policy_no', ['POLICY_NO' => $polNo]);
    //    dd($policies);
    //    exit();

       if (!($policies)) {
         return response(['message' => 'Policy Number is invaild or lapse. Please contact Fareast Life HO @ +88 09612 666 999 or 16681'], 400);
        }        

       $policies = json_decode( json_encode($policies), true);

    //    print_r($policies['0']['dob']);
    //    exit();
        $policy_status = $policies['0']['policy_status'];

        switch ($policy_status) {
        case "1":
            $policy_status ='Inforce';
            $policies['0']['policy_status'] = $policy_status;
            break;
        case "2":
            $policy_status ='Lapse';
            $policies['0']['policy_status'] = $policy_status;
            break;
        case "3":
            $policy_status ='Dorment';
            $policies['0']['policy_status'] = $policy_status;
            break;
        default:
        $policy_status ='Other';
        $policies['0']['policy_status'] = $policy_status;;
        }

       $dob = Carbon::parse($policies['0']['dob']);
       $policies['0']['dob'] = $dob->format('d M Y');
       
    //    print_r($policies);
    //    exit();

       return response([ 'policy_info' => PolicyResource::collection($policies), 'message' => 'Retrieved successfully'], 200);
   }

   public function payment(Request $request)
   {    

       // $token='eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiNzE3ZTk2OGVkZDBjMjZmNzQ5OWM1YjM2MDFkNzRhYTMxMTRmNDM4MDQzMzg2ZmZlYWNkZmY2YjY4MTI3Yjk2ZDg5MWMwNGYxOGY3YzIyZTQiLCJpYXQiOjE2NTk2MTE3NDcuNTg2ODc5LCJuYmYiOjE2NTk2MTE3NDcuNTg2ODg0LCJleHAiOjE2OTExNDc3NDcuNTgyMTA1LCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.sDpmx3Cl3dFpdZoAdEzCUl-3hlhyhQTS5LkIk0G0vlbydO3so_hNp72phVJElA5UxKuQM11nVHnd2KbrdIiPXI6siHkVYbESOSYIH0Sf0ky8qjdk7bRgODtsgo8WP1BXOSl9paa2bFDxC3QmCJOrmkp8izXYq2hOfIwzDIxiIYbCs6tLH6-kf20CzkotV1fUiH0mi3Er71T5PHvBfxI3xCznyYMfXgS4pjuB06vlFyJHjHm596FbieURy-DZZzwW9g1T0uh55zmO519Rzy8_0w8nEdmYFpqZxlINx-r9Z5mBp6h_r8qwan-Uca58IJNjRz5SaHBo3LBxg0sHcjCer2BJ94zF-J2SBT71-hVK-PGmwIpG2NEwPASEC_yAuh85rS8_jSD-Hj45g1hsc6NbDs55Mm9evHstaOrC-sQ1jf6kuitlmQ-L5rPVrLdGEUljVlsBQfXqM3n3Bl5k6SZYiGSZ-zaSNLQhHOcS_fAdz5rcH7yiGjJznGjfRm4PIxA_8FlrnTDpshRHFHCPjVaEFjKZBfPswxTSrjNzNCiVSjWfW_iF3KR69NlYDO0O8Oekp7RPSxu6cr8S-foLuiEIbh_cWi3f7BPoAKAlrma06LDKCIIR6MQuMx-G3xNm8VRz93RQI-k0gQGiZJGK9CA3jVfpt5HJ0C1JKFdbMjtIVWM'

       $header=$request->header('Authorization');

       if($header==''){
           $message='Authorization is required';
           return response()->json(['message'=>$message],422);
       }else{
          
           if($header=='eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiNzE3ZTk2OGVkZDBjMjZmNzQ5OWM1YjM2MDFkNzRhYTMxMTRmNDM4MDQzMzg2ZmZlYWNkZmY2YjY4MTI3Yjk2ZDg5MWMwNGYxOGY3YzIyZTQiLCJpYXQiOjE2NTk2MTE3NDcuNTg2ODc5LCJuYmYiOjE2NTk2MTE3NDcuNTg2ODg0LCJleHAiOjE2OTExNDc3NDcuNTgyMTA1LCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.sDpmx3Cl3dFpdZoAdEzCUl-3hlhyhQTS5LkIk0G0vlbydO3so_hNp72phVJElA5UxKuQM11nVHnd2KbrdIiPXI6siHkVYbESOSYIH0Sf0ky8qjdk7bRgODtsgo8WP1BXOSl9paa2bFDxC3QmCJOrmkp8izXYq2hOfIwzDIxiIYbCs6tLH6-kf20CzkotV1fUiH0mi3Er71T5PHvBfxI3xCznyYMfXgS4pjuB06vlFyJHjHm596FbieURy-DZZzwW9g1T0uh55zmO519Rzy8_0w8nEdmYFpqZxlINx-r9Z5mBp6h_r8qwan-Uca58IJNjRz5SaHBo3LBxg0sHcjCer2BJ94zF-J2SBT71-hVK-PGmwIpG2NEwPASEC_yAuh85rS8_jSD-Hj45g1hsc6NbDs55Mm9evHstaOrC-sQ1jf6kuitlmQ-L5rPVrLdGEUljVlsBQfXqM3n3Bl5k6SZYiGSZ-zaSNLQhHOcS_fAdz5rcH7yiGjJznGjfRm4PIxA_8FlrnTDpshRHFHCPjVaEFjKZBfPswxTSrjNzNCiVSjWfW_iF3KR69NlYDO0O8Oekp7RPSxu6cr8S-foLuiEIbh_cWi3f7BPoAKAlrma06LDKCIIR6MQuMx-G3xNm8VRz93RQI-k0gQGiZJGK9CA3jVfpt5HJ0C1JKFdbMjtIVWM'){

               if($request->ismethod('POST')){
                   $PREM_NAGAD = $request->all();

                   // validation check
                   $rules=[
                       'payment_ref_id'=>'required',
                       'policy_no'=>'required',
                       'amount'=>'required',
                       'ref_mobile_no'=>'required',
                       'status'=>'required'
                       
                    ];
        
                    $customMessage=[
                       'payment_ref_id.required'=>'payment_ref_id is required',
                       'policy_no.required'=>'policy_no is required',
                       'amount.required'=>'amount is required',
                       'ref_mobile_no.required'=>'ref_mobile_no is required',
                       'ref_mobile_no.required'=>'ref_mobile_no is required'

                    ];
        
                    $validator=Validator::make($PREM_NAGAD,$rules,$customMessage);
        
                    if($validator->fails()){
                        return response()->json($validator->errors(),422);
                    }
       
                   $nagad = new OnlinePayment;
                   
                   //$nagad->id = $request->id;
                   // $nagad->id = $online_payments['id'];
                   $nagad->payment_ref_id = $PREM_NAGAD['payment_ref_id'];
                   $nagad->policy_no = $PREM_NAGAD['policy_no'];
                   $nagad->amount = $PREM_NAGAD['amount'];
                   $nagad->ref_mobile_no =$PREM_NAGAD['ref_mobile_no'];
                   $nagad->status =$PREM_NAGAD['status'];
                   // $nagad->client_ip =$request->getClientIp();
                   // $nagad->created_at = $online_payments['created_at'];
                   // $nagad->updated_at = $online_payments['updated_at'];
                   $nagad->save();
                   // return response([
                   //     'payment' => new PolicyResource($nagad)
                   // ],201);
                   $message='Payment Successful';
                    
                   return response()->json(['message'=>$message],200);
       
               }

           }else{
               $message='Authorization does not match';
               return response()->json(['message'=>$message],422);
           }
       }        
   }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * 
     * Payment for Nagad
     */
    public function payment2(Request $request)
    {    
        $online_payments = $request->all();
        // if (strpos(\Request::ip(), '127.0.0.1') == false) {
        //     return response(['message' => 'IP address not match'], 403);
        // }   
         //$nagads['client_ip'] = request()->ip();
         $online_payments['id'] = strtotime("now").rand(1000, 10000);
         $online_payments['client_ip'] =$request->getClientIp();
         $online_payments['client_id'] ='100';
         $online_payments['client_name'] ='Nagad';
         $online_payments['created_at']  = Carbon::now()->toDateTimeString(); 

         //Insert in test table 'payments'
        DB::table( 'payments' )->insert( $online_payments );
        return response([
            'payment-nagad' => new PolicyResource($online_payments)
        ],201);

    }

    /*
     * 
     * Payment for Rocket
     */
    public function payment_rocket(Request $request)
    {    
        $payments = $request->all();
         $payments['id'] = strtotime("now").rand(1000, 10000);
         $payments['client_ip'] =$request->getClientIp();
         $payments['client_id'] ='200';
         $payments['client_name'] ='Rocket';
         $payments['created_at']  = Carbon::now()->toDateTimeString(); 
        
        //Insert in test table 'payments' 
        DB::table( 'payments' )->insert( $payments );
        return response([
            'payment-rocket' => new PolicyResource($payments)
        ],201);
    }

    /*
     * 
     * Payment for bkash
     */
    public function payment_bkash(Request $request)
    {    
        $payments = $request->all();

         $payments['id'] = strtotime("now").rand(1000, 10000);
         $payments['client_ip'] =$request->getClientIp();
         $payments['client_id'] ='300';
         $payments['client_name'] ='Bkash';
         $payments['created_at']  = Carbon::now()->toDateTimeString(); 

        //Insert in test table 'payments'
        DB::table( 'payments' )->insert( $payments );
        return response([
            'payment-bkash' => new PolicyResource($payments)
        ],201);

    }

    public function status(Request $request, $ref_id)
    {

      //$policies = DB::select('SELECT POLICY_NO, INSTPREM AS AMOUNT, STATUS, PROPOSER, DOB, MOBILE from POLICY.POLICY_ALL WHERE STATUS = 1 AND POLICY_NO = :policy_no', ['POLICY_NO' => $pol]);
      
      $payment_status = DB::select('SELECT * from SHAHIDUL.ONLINE_PAYMENTS WHERE PAYMENT_REF_ID = :payment_ref_id', ['PAYMENT_REF_ID' => $ref_id]);
        // dd($payment_status);
        // exit();

        if (!($payment_status)) {
          return response(['message' => 'This payment does not exist'], 400);
         }
         
        $payment_status = json_decode( json_encode($payment_status), true);
        return response([ 'payment_status' => PolicyResource::collection($payment_status), 'message' => 'Retrieved successfully'], 200);
    }
}
