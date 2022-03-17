<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function customerDetails(Request $request){
        $CustomerID=$request->post('CustomerID');
        $infoCustomer = null;

        if($CustomerID!=''){
            $infoCustomer=DB::table('infoCustomer')->where('infoCustomer.CustomerID','=',$CustomerID)
                ->leftJoin('address',function($join) {
                    $join->on( 'infoCustomer.CustomerID', '=', 'address.CustomerID')
                        ->where('address.status','DELIVERY');
                })
                ->first();
            $ltm_spend=DB::table('infoOrder')->select(['Total'])->where('CustomerID','=',$CustomerID)->where('Status','=','FULFILLED')->where('created_at','>=',date('Y-m-d',strtotime('-12 months')))->get();
            $spend=0;
            foreach ($ltm_spend as $order){
                $spend+=$order->Total;
            }
            $infoCustomer->ltm_spend=$spend;
        }

        return response()->json(['customer'=>$infoCustomer]);
    }

    public function getAllCustomers(Request $request){
        $customers = DB::table('infoCustomer')
                        ->join( 'address', function ($join){
                            $join->on( 'address.CustomerID', '=', 'infoCustomer.CustomerID')
                                 ->where('address.status', "DELIVERY");
                        })
                        ->Leftjoin( 'infoOrder', function ($join){
                            $join->on( 'infoOrder.CustomerID', '=', 'infoCustomer.CustomerID')
                                 ->where('infoOrder.Status', "FULFILLED");
                        })
                        ->select(
                            DB::raw('IF(infoCustomer.CustomerIDMaster <> "" AND infoCustomer.CustomerIDMasterAccount <> "" AND infoCustomer.IsMaster = 1 AND infoCustomer.IsMasterAccount = 1, "B2B", "B2C") as type'),
                            DB::raw('LCASE(infoCustomer.TypeDelivery) as active_in'), 
                            DB::raw('LCASE(infoCustomer.Name) as name'), 
                            DB::raw('CONCAT_WS(", ", CONCAT_WS(" ", address.address1, address.address2), address.Town, address.Country, address.postcode) as address'),
                            DB::raw('LCASE(infoCustomer.EmailAddress) as email'),
                            DB::raw('IF(infoCustomer.Phone = "", "--", infoCustomer.Phone) as phone'),
                            DB::raw('IF(DATE_FORMAT(MAX(infoOrder.created_at), "%d/%m/%y") = "", "--", DATE_FORMAT(MAX(infoOrder.created_at), "%d/%m/%y")) as last_order'),
                            DB::raw('CEIL(SUM(infoOrder.Total)) as total_spent'),
                        )
                        ->groupBy('infoCustomer.CustomerID');

        $total_count =  $customers->get()->count();
        $customers   =  $customers->skip($request->skip ? $request->skip : 0)
                                ->take(10)
                                ->get();
        return response()->json([
            'customers'     => $customers,
            'total_count'   => $total_count,
        ]);
    }
}
