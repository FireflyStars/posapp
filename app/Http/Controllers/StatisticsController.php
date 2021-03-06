<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\InfoOrder;
use App\Models\InfoCustomer;
use App\Models\Infoitem;
use App\Models\Poste;
use App\Models\Item;

class StatisticsController extends Controller
{
    public function getStatistics(Request $request)
    {
        $customFilter   =   $request->post('customFilter');
        $startDate      =   $request->post('startDate');
        $endDate        =   $request->post('endDate');
        $dateRangeType  =   $request->post('dateRangeType');
        $compareMode    =   $request->post('compareMode');
        $compareCustomFilter    =   $request->post('compareCustomFilter');
        $compareStartDate       =   $request->post('compareStartDate');
        $compareEndDate         =   $request->post('compareEndDate');
        $compareMode            =   $request->post('compareMode');

        $period         = [ Carbon::parse($startDate)->startOfDay()->toDateTimeString(), Carbon::parse($endDate)->endOfDay()->toDateTimeString() ];
        if(!$compareCustomFilter){
            if($compareMode == 'year')
                $last_period    = [ Carbon::parse($startDate)->subYear(1)->startOfDay()->toDateTimeString(), Carbon::parse($endDate)->subYear(1)->endOfDay()->toDateTimeString() ];
            else        
                $last_period    = [ Carbon::parse($startDate)->subMonth(1)->startOfDay()->toDateTimeString(), Carbon::parse($endDate)->subMonth(1)->endOfDay()->toDateTimeString() ];
        }else{
            $last_period = [ Carbon::parse($compareStartDate)->subMonth(1)->startOfDay()->toDateTimeString(), Carbon::parse($compareEndDate)->subMonth(1)->endOfDay()->toDateTimeString() ];
        }

        // new code added by YH
        if( !$customFilter ){
            $start_first_quarter_day = Carbon::now()->startOfYear();
            $end_first_quarter_day = Carbon::parse($start_first_quarter_day)->lastOfQuarter();
            if( $dateRangeType == 'Today' ){
                $period = [Carbon::now()->startOfDay()->toDateTimeString(), Carbon::now()->endOfDay()->toDateTimeString()];
                if(!$compareCustomFilter && $compareMode == 'year'){
                    $last_period = [Carbon::now()->subYear(1)->startOfDay()->toDateTimeString(), Carbon::now()->subYear(1)->endOfDay()->toDateTimeString()];
                }
                if(!$compareCustomFilter && $compareMode == 'month'){
                    $last_period = [Carbon::now()->subMonth(1)->startOfDay()->toDateTimeString(), Carbon::now()->subMonth(1)->endOfDay()->toDateTimeString()];
                }
            }else if ( $dateRangeType == 'Yesterday' ){
                $period = [Carbon::yesterday()->startOfDay()->toDateTimeString(), Carbon::yesterday()->endOfDay()->toDateTimeString()];
                if($compareMode == 'year'){
                    $last_period = [Carbon::yesterday()->subYear()->startOfDay()->toDateTimeString(), Carbon::yesterday()->subYear()->endOfDay()->toDateTimeString()];
                }else{
                    $last_period = [Carbon::yesterday()->subMonth()->startOfDay()->toDateTimeString(), Carbon::yesterday()->subMonth()->endOfDay()->toDateTimeString()];
                }
            }else if ( $dateRangeType == 'Last 7 days' ){
                $period = [Carbon::now()->subDays(7)->startOfDay()->toDateTimeString(), Carbon::now()->endOfDay()->toDateTimeString()];
                if(!$compareCustomFilter && $compareMode == 'year'){
                    $last_period = [Carbon::now()->subYear()->subDays(7)->startOfDay()->toDateTimeString(), Carbon::now()->subYear()->endOfDay()->toDateTimeString()];
                }else{
                    $last_period = [Carbon::now()->subMonth()->subDays(7)->startOfDay()->toDateTimeString(), Carbon::now()->subMonth()->endOfDay()->toDateTimeString()];
                }
            }else if ( $dateRangeType == 'Last 90 days' ){
                $period = [Carbon::now()->subDays(90)->startOfDay()->toDateTimeString(), Carbon::now()->endOfDay()->toDateTimeString()];

                if(!$compareCustomFilter && $compareMode == 'year'){
                    $last_period = [Carbon::now()->subYear()->subDays(90)->startOfDay()->toDateTimeString(), Carbon::now()->subYear()->endOfDay()->toDateTimeString()];
                }else{
                    $last_period = [Carbon::now()->subMonth()->subDays(90)->startOfDay()->toDateTimeString(), Carbon::now()->subMonth()->endOfDay()->toDateTimeString()];
                }
            }else if ( $dateRangeType == 'Last Month' ){
                $period = [Carbon::now()->subMonth()->startOfDay()->toDateTimeString(), Carbon::now()->endOfDay()->toDateTimeString()];
                if(!$compareCustomFilter && $compareMode == 'year'){
                    $last_period = [Carbon::now()->subYear()->subMonth()->startOfDay()->toDateTimeString(), Carbon::now()->subYear()->endOfDay()->toDateTimeString()];
                }else{
                    $last_period = [Carbon::now()->subMonth(2)->startOfDay()->toDateTimeString(), Carbon::now()->subMonth(1)->endOfDay()->toDateTimeString()];
                }                
            }else if ( $dateRangeType == 'Year to date' ){
                $period = [Carbon::now()->startOfYear()->toDateTimeString(), Carbon::now()->endOfDay()->toDateTimeString()];
                if(!$compareCustomFilter && $compareMode == 'year'){
                    $last_period = [Carbon::now()->subYear()->startOfYear()->toDateTimeString(), Carbon::now()->subYear()->endOfDay()->toDateTimeString()];
                }else{
                    $last_period = [Carbon::now()->subMonth()->startOfMonth()->toDateTimeString(), Carbon::now()->endOfDay()->toDateTimeString()];
                }                
            }else if ( $dateRangeType == '4th Quarter' ){
                $period = [Carbon::parse($start_first_quarter_day)->addMonths(9)->startOfDay()->toDateTimeString(), Carbon::parse($end_first_quarter_day)->addMonths(9)->endOfDay()->toDateTimeString()];
                if(!$compareCustomFilter && $compareMode == 'year')
                    $last_period = [Carbon::parse($start_first_quarter_day)->subYear()->addMonths(9)->startOfDay()->toDateTimeString(), Carbon::parse($end_first_quarter_day)->subYear()->addMonths(9)->endOfDay()->toDateTimeString()];
                else
                    $last_period = [Carbon::parse($start_first_quarter_day)->subMonth()->addMonths(9)->startOfDay()->toDateTimeString(), Carbon::parse($end_first_quarter_day)->subMonth()->addMonths(9)->endOfDay()->toDateTimeString()];
            }else if ( $dateRangeType == '3rd Quarter' ){
                $period = [Carbon::parse($start_first_quarter_day)->addMonths(6)->startOfDay()->toDateTimeString(), Carbon::parse($end_first_quarter_day)->addMonths(6)->endOfDay()->toDateTimeString()];
                if(!$compareCustomFilter && $compareMode == 'year')
                    $last_period = [Carbon::parse($start_first_quarter_day)->subYear()->addMonths(6)->startOfDay()->toDateTimeString(), Carbon::parse($end_first_quarter_day)->subYear()->addMonths(6)->endOfDay()->toDateTimeString()];
                else
                    $last_period = [Carbon::parse($start_first_quarter_day)->subMonth()->addMonths(6)->startOfDay()->toDateTimeString(), Carbon::parse($end_first_quarter_day)->subMonth()->addMonths(6)->endOfDay()->toDateTimeString()];
            }else if ( $dateRangeType == '2nd Quarter' ){
                $period = [Carbon::parse($start_first_quarter_day)->addMonths(3)->startOfDay()->toDateTimeString(), Carbon::parse($end_first_quarter_day)->addMonths(3)->endOfDay()->toDateTimeString()];
                if(!$compareCustomFilter && $compareMode == 'year')
                    $last_period = [Carbon::parse($start_first_quarter_day)->subYear()->addMonths(3)->startOfDay()->toDateTimeString(), Carbon::parse($end_first_quarter_day)->subYear()->addMonths(3)->endOfDay()->toDateTimeString()];
                else
                    $last_period = [Carbon::parse($start_first_quarter_day)->subMonth()->addMonths(3)->startOfDay()->toDateTimeString(), Carbon::parse($end_first_quarter_day)->subMonth()->addMonths(3)->endOfDay()->toDateTimeString()];
            }else{
                $start = Carbon::now()->subYear()->startOfYear();
                $period = [$start_first_quarter_day->toDateTimeString(), $end_first_quarter_day->toDateTimeString()];
                if(!$compareCustomFilter && $compareMode == 'year')
                    $last_period = [Carbon::now()->subYear()->startOfYear()->toDateTimeString(), Carbon::now()->subYear()->lastOfQuarter()->toDateTimeString()];
                else
                    $last_period = [Carbon::now()->subMonth()->startOfMonth()->toDateTimeString(), Carbon::now()->endOfDay()->toDateTimeString()];
            }
        }
        $total_sale_stores = InfoOrder::whereBetween('created_at', $period)
                                    ->select(DB::raw('CEIL(AVG(Total)) as avg'), DB::raw('CEIL(SUM(Total)) as total'), 'TypeDelivery')
                                    ->groupBy('TypeDelivery')
                                    ->get();    
        $last_total_sale_stores = InfoOrder::whereBetween('created_at', $last_period)
                                    ->select(DB::raw('CEIL(AVG(Total)) as avg'), DB::raw('CEIL(SUM(Total)) as total'), 'TypeDelivery')
                                    ->groupBy('TypeDelivery')
                                    ->get();
        
        $stats = [];
        $statistique['mb'] = 0;
        $statistique['nh'] = 0;
        $statistique['sk'] = 0;
        $statistique['ch'] = 0;
        $statistique['last_mb'] = 0;
        $statistique['last_nh'] = 0;
        $statistique['last_sk'] = 0;
        $statistique['last_ch'] = 0;
        
        $total_sales_store = 0;
        $last_total_sales_store = 0;
        
        $avg_store_order = 0;
        $last_avg_store_order = 0;
        
        $avg_delivery_order = 0;
        $last_avg_delivery_order = 0;
        
        $toal_delivery_sale = 0;
        $last_toal_delivery_sale = 0;

        $delivery_count = 0;
        $last_delivery_count = 0;
        foreach ($total_sale_stores as $key => $store) {
            if($store->TypeDelivery == 'MARYLEBONE') {
                $total_sales_store    +=  $store->total;
                $statistique['mb'] = $store->total ;
            }
            if($store->TypeDelivery == 'NOTTING HILL') {
                $total_sales_store  += $store->total;
                $statistique['nh'] = $store->total;
            }
            if($store->TypeDelivery == 'SOUTH KEN') {
                $total_sales_store  += $store->total;
                $statistique['sk'] = $store->total;
            }
            if($store->TypeDelivery == 'CHELSEA') {
                $total_sales_store  += $store->total;
                $statistique['ch'] = $store->total;
            }
            if($store->TypeDelivery == 'DELIVERY') {
                $toal_delivery_sale += $store->total;
                $delivery_count ++;
                $avg_delivery_order +=  $store->avg;
            }
        }
        // last year
        foreach ($last_total_sale_stores as $key => $store) {
            if($store->TypeDelivery == 'MARYLEBONE') {
                $last_total_sales_store    +=  $store->total;
                $statistique['last_mb'] = $store->total;
            }
            if($store->TypeDelivery == 'NOTTING HILL') {
                $last_total_sales_store  += $store->total;
                $statistique['last_nh'] = $store->total;
            }
            if($store->TypeDelivery == 'SOUTH KEN') {
                $last_total_sales_store  += $store->total;
                $statistique['last_sk'] = $store->total;
            }
            if($store->TypeDelivery == 'CHELSEA') {
                $last_total_sales_store  += $store->total;
                $statistique['last_ch'] = $store->total;
            }
            if($store->TypeDelivery == 'DELIVERY') {
                $last_toal_delivery_sale += $store->total;
                $last_delivery_count ++;
                $last_avg_delivery_order +=  $store->avg;
            }
        }
        // end total sales store
        
        // start total sales deliveries (B2B, B2C)

        // $statistique['avg_store_order'] = ($total_sale_stores->count() - $delivery_count) <= 0 ? 0 : number_format( $total_sales_store / ($total_sale_stores->count() - $delivery_count), 0);
        $statistique['avg_store_order'] = number_format( 
            InfoOrder::whereBetween('created_at', $period)
                                    ->select(DB::raw('AVG(Total) as avg'))
                                    ->where('TypeDelivery', '!=', 'DELIVERY')->value('avg') , 2);
        $statistique['last_avg_store_order'] = number_format( 
            InfoOrder::whereBetween('created_at', $last_period)
                                    ->select(DB::raw('AVG(Total) as avg'))
                                    ->where('TypeDelivery', '!=', 'DELIVERY')->value('avg') , 2);
        $statistique['avg_delivery_order'] = number_format( 
            InfoOrder::whereBetween('created_at', $period)
                    ->select(DB::raw('AVG(Total) as avg'))
                    ->where('TypeDelivery', 'DELIVERY')->value('avg'), 2);
        $statistique['last_avg_delivery_order'] = number_format( 
            InfoOrder::whereBetween('created_at', $last_period)
                    ->select(DB::raw('AVG(Total) as avg'))
                    ->where('TypeDelivery', 'DELIVERY')->value('avg'), 2);

        $total_sales_b2b = InfoOrder::join('infoCustomer', 'infoOrder.CustomerID', '=', 'infoCustomer.CustomerID')
                                    ->whereBetween('infoOrder.created_at', $period)
                                    ->where( function( $query ) {
                                        $query->where('infoCustomer.CustomerIDMaster', '!=', '')
                                                ->orWhere('infoCustomer.CustomerIDMasterAccount', '!=', '')
                                                ->orWhere('infoCustomer.IsMaster', 1)
                                                ->orWhere('infoCustomer.IsMasterAccount', 1);
                                    } )                                    
                                    ->where('total', '!=', 0)
                                    ->select(DB::raw('AVG(Total) as avg'), DB::raw('CEIL(SUM(Total)) as total'))
                                    ->first();
        $last_total_sales_b2b = InfoOrder::join('infoCustomer', 'infoOrder.CustomerID', '=', 'infoCustomer.CustomerID')
                                    ->whereBetween('infoOrder.created_at', $last_period)
                                    ->where( function( $query ) {
                                        $query->where('infoCustomer.CustomerIDMaster', '!=', '')
                                                ->orWhere('infoCustomer.CustomerIDMasterAccount', '!=', '')
                                                ->orWhere('infoCustomer.IsMaster', 1)
                                                ->orWhere('infoCustomer.IsMasterAccount', 1);
                                    } )                                    
                                    ->where('total', '!=', 0)
                                    ->select(DB::raw('AVG(Total) as avg'), DB::raw('CEIL(SUM(Total)) as total'))
                                    ->first();
        $statistique['b2b'] = $total_sales_b2b->total;
        $statistique['last_b2b'] = $last_total_sales_b2b->total;

        $statistique['avg_b2b_order'] = number_format( $total_sales_b2b->avg, 2);
        $statistique['last_avg_b2b_order'] = number_format( $last_total_sales_b2b->avg, 2);

        $total_sales_b2c = InfoOrder::join('infoCustomer', 'infoOrder.CustomerID', '=', 'infoCustomer.CustomerID')
                                    ->whereBetween('infoOrder.created_at', $period)
                                    ->where('infoCustomer.CustomerIDMaster', '')
                                    ->where('infoCustomer.CustomerIDMasterAccount', '')
                                    ->where('infoCustomer.IsMaster', 0)
                                    ->where('infoCustomer.IsMasterAccount', 0)
                                    ->where('total', '!=', 0)
                                    ->select(DB::raw('AVG(Total) as avg'), DB::raw('CEIL(SUM(Total)) as total'))
                                    ->first();

        $last_total_sales_b2c = InfoOrder::join('infoCustomer', 'infoOrder.CustomerID', '=', 'infoCustomer.CustomerID')
                                    ->whereBetween('infoOrder.created_at', $last_period)
                                    ->where('infoCustomer.CustomerIDMaster', '')
                                    ->where('infoCustomer.CustomerIDMasterAccount', '')
                                    ->where('infoCustomer.IsMaster', 0)
                                    ->where('infoCustomer.IsMasterAccount', 0)
                                    ->where('total', '!=', 0)
                                    ->select(DB::raw('AVG(Total) as avg'), DB::raw('CEIL(SUM(Total)) as total'))
                                    ->first();

        $statistique['b2c'] = $total_sales_b2c->total;
        $statistique['last_b2c'] = $last_total_sales_b2c->total;

        $statistique['avg_b2c_order'] = number_format( $total_sales_b2c->avg, 2);
        $statistique['last_avg_b2c_order'] = number_format( $last_total_sales_b2c->avg, 2);

        $statistique['avg_total_order'] = number_format(InfoOrder::whereBetween('created_at', $period)
                                                    ->where('total', '!=', 0)
                                                    ->select(DB::raw('AVG(total) as total'))->value('total'), 2);

        $statistique['last_avg_total_order'] = number_format(InfoOrder::whereBetween('created_at', $last_period)
                                                    ->where('total', '!=', 0)
                                                    ->select(DB::raw('AVG(total) as total'))->value('total'), 2);
        $statistique['total_sales'] = number_format( $total_sales_store + $total_sales_b2c->total + $total_sales_b2b->total, 0 );
        $statistique['last_total_sales'] = number_format( $last_total_sales_store + $last_total_sales_b2c->total + $last_total_sales_b2b->total, 0 );

        // end total sales store

        // start new signup data
        $total_new_signup = InfoCustomer::where(function($query) use ($period){
                                            $query->whereBetween('SignupDate', $period)
                                                ->orWhereBetween('SignupDateOnline', $period);
                                        })
                                        ->select(DB::raw('count(*) as count'))->value('count');
        $last_total_new_signup = InfoCustomer::where(function($query) use ($last_period){
                                            $query->whereBetween('SignupDate', $last_period)
                                                ->orWhereBetween('SignupDateOnline', $last_period);
                                        })
                                        ->select(DB::raw('count(*) as count'))->value('count');

        $stores_new_signup = InfoCustomer::where(function($query) use ($period){
                                            $query->whereBetween('SignupDate', $period)
                                                ->orWhereBetween('SignupDateOnline', $period);
                                        })
                                        ->select(DB::raw('count(*) as count'))->where('TypeDelivery', '!=','DELIVERY')->value('count');
        $last_stores_new_signup = InfoCustomer::where(function($query) use ($last_period){
                                            $query->whereBetween('SignupDate', $last_period)
                                                ->orWhereBetween('SignupDateOnline', $last_period);
                                        })
                                        ->select(DB::raw('count(*) as count'))->where('TypeDelivery', '!=','DELIVERY')->value('count');

        $delivery_new_signup = InfoCustomer::where(function($query) use ($period){
                                                $query->whereBetween('SignupDate', $period)
                                                    ->orWhereBetween('SignupDateOnline', $period);
                                            })
                                        ->select(DB::raw('count(*) as count'))->where('TypeDelivery' ,'DELIVERY')->value('count');
        $last_delivery_new_signup = InfoCustomer::where(function($query) use ($last_period){
                                                $query->whereBetween('SignupDate', $last_period)
                                                    ->orWhereBetween('SignupDateOnline', $last_period);
                                            })
                                        ->select(DB::raw('count(*) as count'))->where('TypeDelivery' ,'DELIVERY')->value('count');

        $b2b_new_signup = InfoCustomer::where(function($query) use ($period){
                                            $query->whereBetween('SignupDate', $period)
                                                ->orWhereBetween('SignupDateOnline', $period);
                                        })
                                        ->where( function( $query ) {
                                            $query->where('CustomerIDMaster', '!=', '')
                                                    ->orWhere('CustomerIDMasterAccount', '!=', '')
                                                    ->orWhere('IsMaster', 1)
                                                    ->orWhere('IsMasterAccount', 1);
                                        } )
                                        ->select(DB::raw('count(*) as count'))->value('count');
        $last_b2b_new_signup = InfoCustomer::where(function($query) use ($last_period){
                                            $query->whereBetween('SignupDate', $last_period)
                                                ->orWhereBetween('SignupDateOnline', $last_period);
                                        })
                                        ->where( function( $query ) {
                                            $query->where('CustomerIDMaster', '!=', '')
                                                    ->orWhere('CustomerIDMasterAccount', '!=', '')
                                                    ->orWhere('IsMaster', 1)
                                                    ->orWhere('IsMasterAccount', 1);
                                        } )
                                        ->select(DB::raw('count(*) as count'))->value('count');

        $b2c_new_signup = InfoCustomer::where(function($query) use ($period){
                                            $query->whereBetween('SignupDate', $period)
                                                ->orWhereBetween('SignupDateOnline', $period);
                                        })
                                        ->where( function( $query ) {
                                            $query->where('CustomerIDMaster', '')
                                                    ->where('CustomerIDMasterAccount', '')
                                                    ->where('IsMaster', 0)
                                                    ->where('IsMasterAccount', 0);
                                        } )
                                        ->select(DB::raw('count(*) as count'))->value('count');
        $last_b2c_new_signup = InfoCustomer::where(function($query) use ($last_period){
                                            $query->whereBetween('SignupDate', $last_period)
                                                ->orWhereBetween('SignupDateOnline', $last_period);
                                        })
                                        ->where( function( $query ) {
                                            $query->where('CustomerIDMaster', '')
                                                    ->where('CustomerIDMasterAccount', '')
                                                    ->where('IsMaster', 0)
                                                    ->where('IsMasterAccount', 0);
                                        } )
                                        ->select(DB::raw('count(*) as count'))->value('count');

        $app_new_signup = InfoCustomer::where(function($query) use ($period){
                                            $query->whereBetween('SignupDateOnline', $period);
                                        })
                                        ->select(DB::raw('count(*) as count'))->value('count');
        $last_app_new_signup = InfoCustomer::where(function($query) use ($last_period){
                                            $query->whereBetween('SignupDateOnline', $last_period);
                                        })
                                        ->select(DB::raw('count(*) as count'))->value('count');
        $statistique['total_new_signup'] = $total_new_signup;
        $statistique['last_total_new_signup'] = $last_total_new_signup;

        $statistique['b2c_new_signup'] = $b2c_new_signup;
        $statistique['last_b2c_new_signup'] = $last_b2c_new_signup;

        $statistique['b2b_new_signup'] = $b2b_new_signup;
        $statistique['last_b2b_new_signup'] = $last_b2b_new_signup;

        $statistique['delivery_new_signup'] = $delivery_new_signup;
        $statistique['last_delivery_new_signup'] = $last_delivery_new_signup;

        $statistique['stores_new_signup'] = $stores_new_signup;
        $statistique['last_stores_new_signup'] = $last_stores_new_signup;

        $statistique['app_new_signup'] = $app_new_signup;
        $statistique['last_app_new_signup'] = $last_app_new_signup;
        // end new signup data

        // booking data
        $statistique['total_booking'] = DB::table('pickup')
                                          ->whereBetween('date', $period)
                                          ->where('status', 'not LIKE', '%DEL%') 
                                          ->select(DB::raw('count(*) as count'))->value('count') ?? 0;
        $statistique['last_total_booking'] = DB::table('pickup')
                                          ->whereBetween('date', $last_period)
                                          ->where('status', 'not LIKE', '%DEL%') 
                                          ->select(DB::raw('count(*) as count'))->value('count') ?? 0;

        $statistique['app_booking'] = DB::table('pickup')
                                          ->whereBetween('date', $period)
                                          ->where('status', 'not LIKE', '%DEL%')
                                          ->where('status', 'LIKE', '%API%')
                                          ->select(DB::raw('count(*) as count'))->value('count') ?? 0;

        $statistique['last_app_booking'] = DB::table('pickup')
                                          ->whereBetween('date', $last_period)
                                          ->where('status', 'not LIKE', '%DEL%')
                                          ->where('status', 'LIKE', '%API%')
                                          ->select(DB::raw('count(*) as count'))->value('count') ?? 0;
        $statistique['pos_booking'] = DB::table('pickup')
                                          ->whereBetween('date', $period)
                                          ->where('status', 'not LIKE', '%DEL%')
                                          ->where('status', 'LIKE', '%PMS%')
                                          ->select(DB::raw('count(*) as count'))->value('count') ?? 0;
        $statistique['last_pos_booking'] = DB::table('pickup')
                                          ->whereBetween('date', $last_period)
                                          ->where('status', 'not LIKE', '%DEL%')
                                          ->where('status', 'LIKE', '%PMS%')
                                          ->select(DB::raw('count(*) as count'))->value('count') ?? 0;
        $statistique['rec_booking'] = DB::table('pickup')
                                          ->whereBetween('date', $period)
                                          ->where('status', 'not LIKE', '%DEL%')
                                          ->where('status', 'LIKE', '%REC%')
                                          ->select(DB::raw('count(*) as count'))->value('count') ?? 0;
        $statistique['last_rec_booking'] = DB::table('pickup')
                                          ->whereBetween('date', $last_period)
                                          ->where('status', 'not LIKE', '%DEL%')
                                          ->where('status', 'LIKE', '%REC%')
                                          ->select(DB::raw('count(*) as count'))->value('count') ?? 0;
        // end booking data

        // service section data
        $dep_list = Infoitem::distinct('DepartmentName')
                            ->orderByRaw("FIELD(DepartmentName, 'Garment Care','Shirts Hung','Shirts Folded','Shirts','Business','Laundry','Bedlinen','Household','Suede/Leather/Wax','Laundry Bag','Other')")
                            ->where('DepartmentName', '!=','')
                            ->select('DepartmentName')
                            ->get();

        $statistique['sale_store_by_dep'] = [];
        $statistique['item_store_by_dep'] = [];
        $statistique['sale_delivery_by_dep'] = [];
        $statistique['item_delivery_by_dep'] = [];
        $statistique['total_sale_store_by_dep'] = 0;
        $statistique['total_item_store_by_dep'] = 0;
        $statistique['total_sale_delivery_by_dep'] = 0;
        $statistique['total_item_delivery_by_dep'] = 0;
        foreach ($dep_list as $dep) {
            $store = Infoitem::whereBetween('PromisedDate', $period)
                    ->where('StoreName', '!=', 'ATELIER')
                    ->where('DepartmentName', $dep->DepartmentName)
                    ->select( 
                        DB::raw('CEIL(SUM(priceTotal)) as total'), DB::raw('COUNT(*) as count'),
                    )
                    ->first();
            $statistique['sale_store_by_dep'][] = $store->total ? $store->total : 0;
            $statistique['total_sale_store_by_dep'] += $store->total ? $store->total : 0;
            $statistique['item_store_by_dep'][] = $store->count ? $store->count : 0;
            $statistique['total_item_store_by_dep'] += $store->count ? $store->count : 0;

            $delivery = Infoitem::whereBetween('PromisedDate', $period)
                    ->where('StoreName', 'ATELIER')
                    ->where('DepartmentName', $dep->DepartmentName)
                    ->select( 
                        DB::raw('CEIL(SUM(priceTotal)) as total'), DB::raw('COUNT(*) as count'),
                    )
                    ->first();            
            $statistique['sale_delivery_by_dep'][] = $delivery->total ? $delivery->total : 0;
            $statistique['total_sale_delivery_by_dep'] += $delivery->total ? $delivery->total : 0;
            $statistique['item_delivery_by_dep'][] = $delivery->count ? $delivery->count : 0;
            $statistique['total_item_delivery_by_dep'] += $delivery->count ? $delivery->count : 0;
        }
        // $store_by_dep = Infoitem::where('PromisedDate', $period)
        //                             ->where('StoreName', '!=', 'ATELIER')
        //                             ->groupBy('DepartmentName')
        //                             ->select( 
        //                                 DB::raw('SUM(priceTotal) as total'), 'DepartmentName',
        //                                 DB::raw('COUNT(*) as count'),
        //                             )
        //                             ->orderByRaw( 
        //                                 "FIELD(DepartmentName, 'Garment Care','Shirts Hung','Shirts Folded','Shirts','Business','Laundry','Bedlinen','Household','Suede/Leather/Wax','Laundry Bag','Other')")
        //                             ->get();
                                    
        // $delivery_by_dep = Infoitem::where('PromisedDate', $period)
        //                             ->where('StoreName', 'ATELIER')
        //                             ->groupBy('DepartmentName')
        //                             ->select( 
        //                                 DB::raw('(SUM(priceTotal)) as total'), 'DepartmentName',
        //                                 DB::raw('COUNT(*) as count'),
        //                             )
        //                             ->orderByRaw("FIELD(DepartmentName, 'Garment Care','Shirts Hung','Shirts Folded','Shirts','Business','Laundry','Bedlinen','Household','Suede/Leather/Wax','Laundry Bag','Other')")
        //                             ->get();

        // $statistique['store_by_dep'] = $store_by_dep;
        // $statistique['sale_store_by_dep'] = $store_by_dep->sum('total');
        // $statistique['item_store_by_dep'] = $store_by_dep->sum('count');

        // $statistique['delivery_by_dep'] = $delivery_by_dep;
        // $statistique['sale_delivery_by_dep'] = $delivery_by_dep->sum('total');
        // $statistique['item_delivery_by_dep'] = $delivery_by_dep->sum('count');
        $statistique['dep_list'] = $dep_list;

        return response()->json(
            [
                'stats'=> $statistique
            ]
        );
    }
    public static function getAssemblyHomeStats(Request $request){
        $grouped_postes = Poste::getAssemblyPostes();
        $main_stats = new \stdClass();
        $due_today_std = 0;
        $due_today_exp = 0;
        $due_tomorrow_std = 0;
        $due_tomorrow_exp = 0;
        $overdue_std = 0;
        $overdue_exp = 0;
        $later_std = 0;
        $later_exp = 0;

        /*To do loop and increment*/
        $now = strtotime('now');
        $today = date('Y-m-d');
        $day = 24*3600;
        $tomorrow = date("Y-m-d",$now+$day);

        $stats_per_postes_today = [];
        $stats_per_postes_tomorrow = [];
        $stats_per_postes_overdue = [];
        $stats_per_postes_later = [];



        foreach($grouped_postes as $k=>$v){
            $stats_per_postes_today[$v['group_name']] = [
                'std'=>0,
                'exp'=>0,
            ];

            $stats_per_postes_tomorrow[$v['group_name']] = [
                'std'=>0,
                'exp'=>0,
            ];

            $stats_per_postes_overdue[$v['group_name']] = [
                'std'=>0,
                'exp'=>0,
            ];

            $stats_per_postes_later[$v['group_name']] = [
                'std'=>0,
                'exp'=>0,
            ];
        }


        $stats = DB::table('infoitems')
            ->join('postes','infoitems.nextpost','=','postes.id')
            ->whereIn('PromisedDate',[$today, $tomorrow])
            ->get();

        $other_stats = DB::table('infoitems')
            ->join('postes','infoitems.nextpost','=','postes.id')
            ->where('Actif',1)
            ->whereNotIn('PromisedDate',[$today,$tomorrow])
            ->get();


        $exp_index = [1,3,4,5,6]; //VOIR Item::getTypeService()

        $post_shelving=[30,31,32];
        $post_tailoring=[1];
        $post_spotting=[2,3,4,5,27];// spotting is cleaning
        $post_qc1=[6,7];//qc1
        $post_pressing=[8,9,10,11,35];//pressing
        $post_qc2=[12,13,25];//qc2
        $post_loading_station=[23];// Assembly loading station
        $post_conveyor=[24];// Assembly conveyor
        $post_storage=[36];// Storage conveyor
        $post_ccin=[33,40];//Customer Care IN

        $allpost=array_merge($post_shelving,$post_tailoring,$post_spotting,$post_qc1,$post_pressing,$post_qc2,$post_loading_station,$post_conveyor,$post_storage,$post_ccin);


        $stats_partnerin_today_std = 0;
        $stats_partnerin_today_exp = 0;
        $stats_partnerin_today_delivery = 0;
        $stats_partnerin_today_store = 0;
        $stats_partnerin_tomorrow_std = 0;
        $stats_partnerin_tomorrow_exp = 0;
        $stats_partnerin_tomorrow_delivery = 0;
        $stats_partnerin_tomorrow_store = 0;


        $stats_shelving_today_exp = 0;
        $stats_shelving_today_std = 0;
        $stats_shelving_today_delivery = 0;
        $stats_shelving_today_store = 0;
        $stats_shelving_tomorrow_exp = 0;
        $stats_shelving_tomorrow_std = 0;
        $stats_shelving_tomorrow_delivery = 0;
        $stats_shelving_tomorrow_store = 0;

        $stats_tailoring_today_exp = 0;
        $stats_tailoring_today_std = 0;
        $stats_tailoring_today_delivery = 0;
        $stats_tailoring_today_store = 0;
        $stats_tailoring_tomorrow_exp = 0;
        $stats_tailoring_tomorrow_std = 0;
        $stats_tailoring_tomorrow_delivery = 0;
        $stats_tailoring_tomorrow_store = 0;

        $stats_spotting_today_exp = 0;
        $stats_spotting_today_std = 0;
        $stats_spotting_today_delivery = 0;
        $stats_spotting_today_store = 0;
        $stats_spotting_tomorrow_exp = 0;
        $stats_spotting_tomorrow_std = 0;
        $stats_spotting_tomorrow_delivery = 0;
        $stats_spotting_tomorrow_store = 0;

        $stats_qc1_today_exp = 0;
        $stats_qc1_today_std = 0;
        $stats_qc1_today_delivery = 0;
        $stats_qc1_today_store = 0;
        $stats_qc1_tomorrow_exp = 0;
        $stats_qc1_tomorrow_std = 0;
        $stats_qc1_tomorrow_delivery = 0;
        $stats_qc1_tomorrow_store = 0;


        $stats_pressing_today_exp = 0;
        $stats_pressing_today_std = 0;
        $stats_pressing_today_delivery = 0;
        $stats_pressing_today_store = 0;
        $stats_pressing_tomorrow_exp = 0;
        $stats_pressing_tomorrow_std = 0;
        $stats_pressing_tomorrow_delivery = 0;
        $stats_pressing_tomorrow_store = 0;

        $stats_qc2_today_exp = 0;
        $stats_qc2_today_std = 0;
        $stats_qc2_today_delivery = 0;
        $stats_qc2_today_store = 0;
        $stats_qc2_tomorrow_exp = 0;
        $stats_qc2_tomorrow_std = 0;
        $stats_qc2_tomorrow_delivery = 0;
        $stats_qc2_tomorrow_store = 0;

        $stats_conveyor_today_exp = 0;
        $stats_conveyor_today_std = 0;
        $stats_conveyor_today_delivery = 0;
        $stats_conveyor_today_store = 0;
        $stats_conveyor_tomorrow_exp = 0;
        $stats_conveyor_tomorrow_std = 0;
        $stats_conveyor_tomorrow_delivery = 0;
        $stats_conveyor_tomorrow_store = 0;

        $stats_loading_station_today_exp = 0;
        $stats_loading_station_today_std = 0;
        $stats_loading_station_today_delivery = 0;
        $stats_loading_station_today_store = 0;
        $stats_loading_station_tomorrow_exp = 0;
        $stats_loading_station_tomorrow_std = 0;
        $stats_loading_station_tomorrow_delivery = 0;
        $stats_loading_station_tomorrow_store = 0;

        $stats_storage_today_exp = 0;
        $stats_storage_today_std = 0;
        $stats_storage_today_delivery = 0;
        $stats_storage_today_store = 0;
        $stats_storage_tomorrow_exp = 0;
        $stats_storage_tomorrow_std = 0;
        $stats_storage_tomorrow_delivery = 0;
        $stats_storage_tomorrow_store = 0;

        $stats_ccin_today_exp = 0;
        $stats_ccin_today_std = 0;
        $stats_ccin_today_delivery = 0;
        $stats_ccin_today_store = 0;
        $stats_ccin_tomorrow_exp = 0;
        $stats_ccin_tomorrow_std = 0;
        $stats_ccin_tomorrow_delivery = 0;
        $stats_ccin_tomorrow_store = 0;

        if(!empty($stats)){
            foreach($stats as $k=>$v){
                if($v->PromisedDate==$today){

                    if($v->PartnerINOUT==1||in_array($v->nextpost,$allpost))
                        if (in_array($v->express, $exp_index)) {
                            $due_today_exp += 1;
                        } else {
                            $due_today_std += 1;
                        }
                        if($v->Actif==1){
                        if($v->PartnerINOUT==1) { //Partner IN
                            if($v->store=='DELIVERY'){
                                $stats_partnerin_today_delivery+=1;
                            }else{
                                $stats_partnerin_today_store+=1;
                            }
                        }else{
                            if(in_array($v->nextpost,$post_shelving)){//shelving
                                if($v->store=='DELIVERY'){
                                    $stats_shelving_today_delivery+=1;
                                }else{
                                    $stats_shelving_today_store+=1;
                                }
                            }
                            if(in_array($v->nextpost,$post_tailoring)){//taloring
                                if($v->store=='DELIVERY'){
                                    $stats_tailoring_today_delivery+=1;
                                }else{
                                    $stats_tailoring_today_store+=1;
                                }
                            }
                            if(in_array($v->nextpost,$post_spotting)){//cleaning
                                if($v->store=='DELIVERY'){
                                    $stats_spotting_today_delivery+=1;
                                }else{
                                    $stats_spotting_today_store+=1;
                                }
                            }
                            if(in_array($v->nextpost,$post_qc1)){//qc1
                                if($v->store=='DELIVERY'){
                                    $stats_qc1_today_delivery+=1;
                                }else{
                                    $stats_qc1_today_store+=1;
                                }
                            }
                            if(in_array($v->nextpost,$post_pressing)){//pressing
                                if($v->store=='DELIVERY'){
                                    $stats_pressing_today_delivery+=1;
                                }else{
                                    $stats_pressing_today_store+=1;
                                }
                            }
                            if(in_array($v->nextpost,$post_qc2)){//qc2
                                if($v->store=='DELIVERY'){
                                    $stats_qc2_today_delivery+=1;
                                }else{
                                    $stats_qc2_today_store+=1;
                                }
                            }
                            if(in_array($v->nextpost,$post_loading_station)){// loading station
                                if($v->store=='DELIVERY'){
                                    $stats_loading_station_today_delivery+=1;
                                }else{
                                    $stats_loading_station_today_store+=1;
                                }
                            }
                            if(in_array($v->nextpost,$post_conveyor)){//conveyor
                                if($v->store=='DELIVERY'){
                                    $stats_conveyor_today_delivery+=1;
                                }else{
                                    $stats_conveyor_today_store+=1;
                                }
                            }
                            if(in_array($v->nextpost,$post_storage)){//storage
                                if($v->store=='DELIVERY'){
                                    $stats_storage_today_delivery+=1;
                                }else{
                                    $stats_storage_today_store+=1;
                                }
                            }
                            if(in_array($v->nextpost,$post_ccin)){//ccin
                                if($v->store=='DELIVERY'){
                                    $stats_ccin_today_delivery+=1;
                                }else{
                                    $stats_ccin_today_store+=1;
                                }
                            }
                        }

                        if(in_array($v->express,$exp_index)){
                            if($v->PartnerINOUT==1) { //Partner IN
                                $stats_partnerin_today_exp += 1;
                            }else{
                                if(in_array($v->nextpost,$post_shelving)){//shelving
                                    $stats_shelving_today_exp+=1;
                                }
                                if(in_array($v->nextpost,$post_tailoring)){//taloring
                                    $stats_tailoring_today_exp+=1;
                                }
                                if(in_array($v->nextpost,$post_spotting)){//cleaning
                                    $stats_spotting_today_exp+=1;
                                }
                                if(in_array($v->nextpost,$post_qc1)){//qc1
                                    $stats_qc1_today_exp+=1;
                                }

                                if(in_array($v->nextpost,$post_pressing)){//pressing
                                    $stats_pressing_today_exp+=1;
                                }
                                if(in_array($v->nextpost,$post_qc2)){//qc2
                                    $stats_qc2_today_exp+=1;
                                }

                                if(in_array($v->nextpost,$post_loading_station)){//loading station
                                    $stats_loading_station_today_exp+=1;
                                }

                                if(in_array($v->nextpost,$post_conveyor)){//conveyor
                                    $stats_conveyor_today_exp+=1;
                                }

                                if(in_array($v->nextpost,$post_storage)){//storage
                                    $stats_storage_today_exp+=1;
                                }
                                if(in_array($v->nextpost,$post_ccin)){//ccin
                                    $stats_ccin_today_exp+=1;
                                }
                        }

                        }else{
                            if($v->PartnerINOUT==1) { //Partner IN
                                $stats_partnerin_today_std += 1;
                            }else{
                                if(in_array($v->nextpost,$post_shelving)){//shelving
                                    $stats_shelving_today_std+=1;
                                }

                                if(in_array($v->nextpost,$post_tailoring)){//taloring
                                    $stats_tailoring_today_std+=1;
                                }

                                if(in_array($v->nextpost,$post_spotting)){//cleaning
                                    $stats_spotting_today_std+=1;
                                }
                                if(in_array($v->nextpost,$post_qc1)){//qc1
                                    $stats_qc1_today_std+=1;
                                }
                                if(in_array($v->nextpost,$post_pressing)){//pressing
                                    $stats_pressing_today_std+=1;
                                }
                                if(in_array($v->nextpost,$post_qc2)){//qc2
                                    $stats_qc2_today_std+=1;
                                }
                                if(in_array($v->nextpost,$post_loading_station)){//loading station
                                    $stats_loading_station_today_std+=1;
                                }
                                if(in_array($v->nextpost,$post_conveyor)){//conveyor
                                    $stats_conveyor_today_std+=1;
                                }
                                if(in_array($v->nextpost,$post_storage)){//storage
                                    $stats_storage_today_std+=1;
                                }
                                if(in_array($v->nextpost,$post_ccin)){//ccin
                                    $stats_ccin_today_std+=1;
                                }
                        }
                        }
                    }

                }
                elseif($v->PromisedDate==$tomorrow){

                    if($v->PartnerINOUT==1||in_array($v->nextpost,$allpost))
                        if (in_array($v->express,$exp_index)) {
                            $due_tomorrow_exp += 1;
                        } else {
                            $due_tomorrow_std += 1;
                        }
                    if($v->Actif==1){
                        if($v->PartnerINOUT==1) { //Partner IN
                            if($v->store=='DELIVERY'){
                                $stats_partnerin_tomorrow_delivery+=1;
                            }else{
                                $stats_partnerin_tomorrow_store+=1;
                            }
                        }else{
                            if(in_array($v->nextpost,$post_shelving)){//shelving
                                if($v->store=='DELIVERY'){
                                    $stats_shelving_tomorrow_delivery+=1;
                                }else{
                                    $stats_shelving_tomorrow_store+=1;
                                }
                            }
                            if(in_array($v->nextpost,$post_tailoring)){//taloring
                                if($v->store=='DELIVERY'){
                                    $stats_tailoring_tomorrow_delivery+=1;
                                }else{
                                    $stats_tailoring_tomorrow_store+=1;
                                }
                            }
                            if(in_array($v->nextpost,$post_spotting)){//cleaning
                                if($v->store=='DELIVERY'){
                                    $stats_spotting_tomorrow_delivery+=1;
                                }else{
                                    $stats_spotting_tomorrow_store+=1;
                                }
                            }
                            if(in_array($v->nextpost,$post_qc1)){//qc1
                                if($v->store=='DELIVERY'){
                                    $stats_qc1_tomorrow_delivery+=1;
                                }else{
                                    $stats_qc1_tomorrow_store+=1;
                                }
                            }
                            if(in_array($v->nextpost,$post_pressing)){//pressing
                                if($v->store=='DELIVERY'){
                                    $stats_pressing_tomorrow_delivery+=1;
                                }else{
                                    $stats_pressing_tomorrow_store+=1;
                                }
                            }
                            if(in_array($v->nextpost,$post_qc2)){//qc2
                                if($v->store=='DELIVERY'){
                                    $stats_qc2_tomorrow_delivery+=1;
                                }else{
                                    $stats_qc2_tomorrow_store+=1;
                                }
                            }
                            if(in_array($v->nextpost,$post_loading_station)){//loading station
                                if($v->store=='DELIVERY'){
                                    $stats_loading_station_tomorrow_delivery+=1;
                                }else{
                                    $stats_loading_station_tomorrow_store+=1;
                                }
                            }
                            if(in_array($v->nextpost,$post_conveyor)){//conveyor
                                if($v->store=='DELIVERY'){
                                    $stats_conveyor_tomorrow_delivery+=1;
                                }else{
                                    $stats_conveyor_tomorrow_store+=1;
                                }
                            }
                            if(in_array($v->nextpost,$post_storage)){//storage
                                if($v->store=='DELIVERY'){
                                    $stats_storage_tomorrow_delivery+=1;
                                }else{
                                    $stats_storage_tomorrow_store+=1;
                                }
                            }
                            if(in_array($v->nextpost,$post_ccin)){//ccin
                                if($v->store=='DELIVERY'){
                                    $stats_ccin_tomorrow_delivery+=1;
                                }else{
                                    $stats_ccin_tomorrow_store+=1;
                                }
                            }
                        }

                        if(in_array($v->express,$exp_index)){
                            if($v->PartnerINOUT==1) { //Partner IN
                                $stats_partnerin_tomorrow_exp += 1;
                            }else{

                            if(in_array($v->nextpost,$post_shelving)){//shelving
                                $stats_shelving_tomorrow_exp+=1;
                            }
                             if(in_array($v->nextpost,$post_tailoring)){//tailoring
                                $stats_tailoring_tomorrow_exp+=1;
                            }

                            if(in_array($v->nextpost,$post_spotting)){//cleaning
                                $stats_spotting_tomorrow_exp+=1;
                            }

                            if(in_array($v->nextpost,$post_qc1)){//qc1
                                $stats_qc1_tomorrow_exp+=1;
                            }
                            if(in_array($v->nextpost,$post_pressing)){//pressing
                                $stats_pressing_tomorrow_exp+=1;
                            }
                            if(in_array($v->nextpost,$post_qc2)){//qc2
                                $stats_qc2_tomorrow_exp+=1;
                            }
                            if(in_array($v->nextpost,$post_loading_station)){//loading station
                                $stats_loading_station_tomorrow_exp+=1;
                            }
                            if(in_array($v->nextpost,$post_conveyor)){//conveyor
                                $stats_conveyor_tomorrow_exp+=1;
                            }
                            if(in_array($v->nextpost,$post_storage)){//storage
                                $stats_storage_tomorrow_exp+=1;
                            }
                            if(in_array($v->nextpost,$post_ccin)){//ccin
                                $stats_ccin_tomorrow_exp+=1;
                            }
                        }
                        }else{
                            if($v->PartnerINOUT==1) { //Partner IN
                                $stats_partnerin_tomorrow_std += 1;
                            }else{
                                if(in_array($v->nextpost,$post_shelving)){//shelving
                                    $stats_shelving_tomorrow_std+=1;
                                }
                                if(in_array($v->nextpost,$post_tailoring)){//tailoring
                                    $stats_tailoring_tomorrow_std+=1;
                                }

                                if(in_array($v->nextpost,$post_spotting)){//cleaning
                                    $stats_spotting_tomorrow_std+=1;
                                }

                                if(in_array($v->nextpost,$post_qc1)){//qc1
                                    $stats_qc1_tomorrow_std+=1;
                                }

                                if(in_array($v->nextpost,$post_pressing)){//pressing
                                    $stats_pressing_tomorrow_std+=1;
                                }
                                if(in_array($v->nextpost,$post_qc2)){//qc2
                                    $stats_qc2_tomorrow_std+=1;
                                }
                                if(in_array($v->nextpost,$post_loading_station)){//loading station
                                    $stats_loading_station_tomorrow_std+=1;
                                }
                                if(in_array($v->nextpost,$post_conveyor)){//conveyor
                                    $stats_conveyor_tomorrow_std+=1;
                                }

                                if(in_array($v->nextpost,$post_storage)){//storage
                                    $stats_storage_tomorrow_std+=1;
                                }
                                if(in_array($v->nextpost,$post_ccin)){//ccin
                                    $stats_ccin_tomorrow_std+=1;
                                }
                            }
                        }
                    }
                }
            }
        }
        $stats_per_postes_today['Partner']['std'] = $stats_partnerin_today_std;
        $stats_per_postes_today['Partner']['exp'] = $stats_partnerin_today_exp;
        $stats_per_postes_today['Partner']['all'] = $stats_partnerin_today_std+$stats_partnerin_today_exp;
        $stats_per_postes_today['Partner']['delivery'] = $stats_partnerin_today_delivery;
        $stats_per_postes_today['Partner']['store'] = $stats_partnerin_today_store;
        $stats_per_postes_tomorrow['Partner']['std'] = $stats_partnerin_tomorrow_std;
        $stats_per_postes_tomorrow['Partner']['exp'] =  $stats_partnerin_tomorrow_exp;
        $stats_per_postes_tomorrow['Partner']['all'] = $stats_partnerin_tomorrow_std+$stats_partnerin_tomorrow_exp;
        $stats_per_postes_tomorrow['Partner']['delivery'] = $stats_partnerin_tomorrow_delivery;
        $stats_per_postes_tomorrow['Partner']['store'] =  $stats_partnerin_tomorrow_store;


        $stats_per_postes_today['shelving']['std'] = $stats_shelving_today_std;
        $stats_per_postes_today['shelving']['exp'] = $stats_shelving_today_exp;
        $stats_per_postes_today['shelving']['all'] = $stats_shelving_today_std+$stats_shelving_today_exp;
        $stats_per_postes_today['shelving']['delivery'] = $stats_shelving_today_delivery;
        $stats_per_postes_today['shelving']['store'] = $stats_shelving_today_store;
        $stats_per_postes_tomorrow['shelving']['std'] = $stats_shelving_tomorrow_std;
        $stats_per_postes_tomorrow['shelving']['exp'] =  $stats_shelving_tomorrow_exp;
        $stats_per_postes_tomorrow['shelving']['all'] = $stats_shelving_tomorrow_std+$stats_shelving_tomorrow_exp;
        $stats_per_postes_tomorrow['shelving']['delivery'] = $stats_shelving_tomorrow_delivery;
        $stats_per_postes_tomorrow['shelving']['store'] =  $stats_shelving_tomorrow_store;

        $stats_per_postes_today['Tailoring']['std'] = $stats_tailoring_today_std;
        $stats_per_postes_today['Tailoring']['exp'] = $stats_tailoring_today_exp;
        $stats_per_postes_today['Tailoring']['all'] = $stats_tailoring_today_std+$stats_tailoring_today_exp;
        $stats_per_postes_today['Tailoring']['delivery'] = $stats_tailoring_today_delivery;
        $stats_per_postes_today['Tailoring']['store'] = $stats_tailoring_today_store;
        $stats_per_postes_tomorrow['Tailoring']['std'] = $stats_tailoring_tomorrow_std;
        $stats_per_postes_tomorrow['Tailoring']['exp'] =  $stats_tailoring_tomorrow_exp;
        $stats_per_postes_tomorrow['Tailoring']['all'] = $stats_tailoring_tomorrow_std+ $stats_tailoring_tomorrow_exp;
        $stats_per_postes_tomorrow['Tailoring']['delivery'] = $stats_tailoring_tomorrow_delivery;
        $stats_per_postes_tomorrow['Tailoring']['store'] =  $stats_tailoring_tomorrow_store;

        $stats_per_postes_today['Spotting']['std'] = $stats_spotting_today_std;
        $stats_per_postes_today['Spotting']['exp'] = $stats_spotting_today_exp;
        $stats_per_postes_today['Spotting']['all'] = $stats_spotting_today_std+$stats_spotting_today_exp;
        $stats_per_postes_today['Spotting']['delivery'] = $stats_spotting_today_delivery;
        $stats_per_postes_today['Spotting']['store'] = $stats_spotting_today_store;
        $stats_per_postes_tomorrow['Spotting']['std'] = $stats_spotting_tomorrow_std;
        $stats_per_postes_tomorrow['Spotting']['exp'] =  $stats_spotting_tomorrow_exp;
        $stats_per_postes_tomorrow['Spotting']['all'] = $stats_spotting_tomorrow_std+$stats_spotting_tomorrow_exp;
        $stats_per_postes_tomorrow['Spotting']['delivery'] = $stats_spotting_tomorrow_delivery;
        $stats_per_postes_tomorrow['Spotting']['store'] =  $stats_spotting_tomorrow_store;


        $stats_per_postes_today['Quality Control 1']['std'] = $stats_qc1_today_std;
        $stats_per_postes_today['Quality Control 1']['exp'] = $stats_qc1_today_exp;
        $stats_per_postes_today['Quality Control 1']['all'] =  $stats_qc1_today_std+$stats_qc1_today_exp;
        $stats_per_postes_today['Quality Control 1']['delivery'] = $stats_qc1_today_delivery;
        $stats_per_postes_today['Quality Control 1']['store'] = $stats_qc1_today_store;
        $stats_per_postes_tomorrow['Quality Control 1']['std'] = $stats_qc1_tomorrow_std;
        $stats_per_postes_tomorrow['Quality Control 1']['exp'] =  $stats_qc1_tomorrow_exp;
        $stats_per_postes_tomorrow['Quality Control 1']['all'] = $stats_qc1_tomorrow_std+$stats_qc1_tomorrow_exp;
        $stats_per_postes_tomorrow['Quality Control 1']['delivery'] = $stats_qc1_tomorrow_delivery;
        $stats_per_postes_tomorrow['Quality Control 1']['store'] =  $stats_qc1_tomorrow_store;

        $stats_per_postes_today['Pressing']['std'] = $stats_pressing_today_std;
        $stats_per_postes_today['Pressing']['exp'] = $stats_pressing_today_exp;
        $stats_per_postes_today['Pressing']['all'] = $stats_pressing_today_std+$stats_pressing_today_exp;
        $stats_per_postes_today['Pressing']['delivery'] = $stats_pressing_today_delivery;
        $stats_per_postes_today['Pressing']['store'] = $stats_pressing_today_store;
        $stats_per_postes_tomorrow['Pressing']['std'] = $stats_pressing_tomorrow_std;
        $stats_per_postes_tomorrow['Pressing']['exp'] =  $stats_pressing_tomorrow_exp;
        $stats_per_postes_tomorrow['Pressing']['all'] = $stats_pressing_tomorrow_std+$stats_pressing_tomorrow_exp;
        $stats_per_postes_tomorrow['Pressing']['delivery'] = $stats_pressing_tomorrow_delivery;
        $stats_per_postes_tomorrow['Pressing']['store'] =  $stats_pressing_tomorrow_store;

        $stats_per_postes_today['Quality Control 2']['std'] = $stats_qc2_today_std;
        $stats_per_postes_today['Quality Control 2']['exp'] = $stats_qc2_today_exp;
        $stats_per_postes_today['Quality Control 2']['all'] = $stats_qc2_today_std+$stats_qc2_today_exp;
        $stats_per_postes_today['Quality Control 2']['delivery'] = $stats_qc2_today_delivery;
        $stats_per_postes_today['Quality Control 2']['store'] = $stats_qc2_today_store;
        $stats_per_postes_tomorrow['Quality Control 2']['std'] = $stats_qc2_tomorrow_std;
        $stats_per_postes_tomorrow['Quality Control 2']['exp'] =  $stats_qc2_tomorrow_exp;
        $stats_per_postes_tomorrow['Quality Control 2']['all'] = $stats_qc2_tomorrow_std+$stats_qc2_tomorrow_exp;
        $stats_per_postes_tomorrow['Quality Control 2']['delivery'] = $stats_qc2_tomorrow_delivery;
        $stats_per_postes_tomorrow['Quality Control 2']['store'] =  $stats_qc2_tomorrow_store;

        $stats_per_postes_today['Loading Station']['std'] = $stats_loading_station_today_std;
        $stats_per_postes_today['Loading Station']['exp'] = $stats_loading_station_today_exp;
        $stats_per_postes_today['Loading Station']['delivery'] = $stats_loading_station_today_delivery;
        $stats_per_postes_today['Loading Station']['store'] = $stats_loading_station_today_store;
        $stats_per_postes_today['Loading Station']['all'] = $stats_loading_station_today_std+$stats_loading_station_today_exp;
        $stats_per_postes_tomorrow['Loading Station']['std'] = $stats_loading_station_tomorrow_std;
        $stats_per_postes_tomorrow['Loading Station']['exp'] =  $stats_loading_station_tomorrow_exp;
        $stats_per_postes_tomorrow['Loading Station']['all'] = $stats_loading_station_tomorrow_std+$stats_loading_station_tomorrow_exp;
        $stats_per_postes_tomorrow['Loading Station']['delivery'] = $stats_loading_station_tomorrow_delivery;
        $stats_per_postes_tomorrow['Loading Station']['store'] =  $stats_loading_station_tomorrow_store;

        $stats_per_postes_today['Conveyor']['std'] = $stats_conveyor_today_std;
        $stats_per_postes_today['Conveyor']['exp'] = $stats_conveyor_today_exp;
        $stats_per_postes_today['Conveyor']['delivery'] = $stats_conveyor_today_delivery;
        $stats_per_postes_today['Conveyor']['store'] = $stats_conveyor_today_store;
        $stats_per_postes_today['Conveyor']['all'] = $stats_conveyor_today_std+$stats_conveyor_today_exp;
        $stats_per_postes_tomorrow['Conveyor']['std'] = $stats_conveyor_tomorrow_std;
        $stats_per_postes_tomorrow['Conveyor']['exp'] =  $stats_conveyor_tomorrow_exp;
        $stats_per_postes_tomorrow['Conveyor']['all'] = $stats_conveyor_tomorrow_std+$stats_conveyor_tomorrow_exp;
        $stats_per_postes_tomorrow['Conveyor']['delivery'] = $stats_conveyor_tomorrow_delivery;
        $stats_per_postes_tomorrow['Conveyor']['store'] =  $stats_conveyor_tomorrow_store;

        $stats_per_postes_today['Storage']['std'] = $stats_storage_today_std;
        $stats_per_postes_today['Storage']['exp'] = $stats_storage_today_exp;
        $stats_per_postes_today['Storage']['all'] = $stats_storage_today_std+$stats_storage_today_exp;
        $stats_per_postes_today['Storage']['delivery'] = $stats_storage_today_delivery;
        $stats_per_postes_today['Storage']['store'] = $stats_storage_today_store;
        $stats_per_postes_tomorrow['Storage']['std'] = $stats_storage_tomorrow_std;
        $stats_per_postes_tomorrow['Storage']['exp'] =  $stats_storage_tomorrow_exp;
        $stats_per_postes_tomorrow['Storage']['all'] = $stats_storage_tomorrow_std+$stats_storage_tomorrow_exp;
        $stats_per_postes_tomorrow['Storage']['delivery'] = $stats_storage_tomorrow_delivery;
        $stats_per_postes_tomorrow['Storage']['store'] =  $stats_storage_tomorrow_store;

        $stats_per_postes_today['CustomerCare']['std'] = $stats_ccin_today_std;
        $stats_per_postes_today['CustomerCare']['exp'] = $stats_ccin_today_exp;
        $stats_per_postes_today['CustomerCare']['all'] = $stats_ccin_today_std+$stats_ccin_today_exp;
        $stats_per_postes_today['CustomerCare']['delivery'] = $stats_ccin_today_delivery;
        $stats_per_postes_today['CustomerCare']['store'] = $stats_ccin_today_store;
        $stats_per_postes_tomorrow['CustomerCare']['std'] = $stats_ccin_tomorrow_std;
        $stats_per_postes_tomorrow['CustomerCare']['exp'] =  $stats_ccin_tomorrow_exp;
        $stats_per_postes_tomorrow['CustomerCare']['all'] = $stats_ccin_tomorrow_std+$stats_ccin_tomorrow_exp;
        $stats_per_postes_tomorrow['CustomerCare']['delivery'] = $stats_ccin_tomorrow_delivery;
        $stats_per_postes_tomorrow['CustomerCare']['store'] =  $stats_ccin_tomorrow_store;

        $stats_partnerin_overdue_std = 0;
        $stats_partnerin_overdue_exp = 0;
        $stats_partnerin_overdue_delivery = 0;
        $stats_partnerin_overdue_store = 0;
        $stats_partnerin_later_std = 0;
        $stats_partnerin_later_exp = 0;
        $stats_partnerin_later_delivery = 0;
        $stats_partnerin_later_store = 0;

        $stats_shelving_overdue_std = 0;
        $stats_shelving_overdue_exp = 0;
        $stats_shelving_overdue_delivery = 0;
        $stats_shelving_overdue_store = 0;
        $stats_shelving_later_std = 0;
        $stats_shelving_later_exp = 0;
        $stats_shelving_later_delivery = 0;
        $stats_shelving_later_store = 0;

        $stats_tailoring_overdue_std = 0;
        $stats_tailoring_overdue_exp = 0;
        $stats_tailoring_overdue_delivery = 0;
        $stats_tailoring_overdue_store = 0;
        $stats_tailoring_later_std = 0;
        $stats_tailoring_later_exp = 0;
        $stats_tailoring_later_delivery = 0;
        $stats_tailoring_later_store = 0;

        $stats_spotting_overdue_std = 0;
        $stats_spotting_overdue_exp = 0;
        $stats_spotting_overdue_delivery = 0;
        $stats_spotting_overdue_store = 0;
        $stats_spotting_later_std = 0;
        $stats_spotting_later_exp = 0;
        $stats_spotting_later_delivery = 0;
        $stats_spotting_later_store = 0;

        $stats_qc1_overdue_std = 0;
        $stats_qc1_overdue_exp = 0;
        $stats_qc1_overdue_delivery = 0;
        $stats_qc1_overdue_store = 0;
        $stats_qc1_later_std = 0;
        $stats_qc1_later_exp = 0;
        $stats_qc1_later_delivery = 0;
        $stats_qc1_later_store = 0;

        $stats_pressing_overdue_std = 0;
        $stats_pressing_overdue_exp = 0;
        $stats_pressing_overdue_delivery = 0;
        $stats_pressing_overdue_store = 0;
        $stats_pressing_later_std = 0;
        $stats_pressing_later_exp = 0;
        $stats_pressing_later_delivery = 0;
        $stats_pressing_later_store = 0;

        $stats_qc2_overdue_std = 0;
        $stats_qc2_overdue_exp = 0;
        $stats_qc2_overdue_delivery = 0;
        $stats_qc2_overdue_store = 0;
        $stats_qc2_later_std = 0;
        $stats_qc2_later_exp = 0;
        $stats_qc2_later_delivery = 0;
        $stats_qc2_later_store = 0;

        $stats_loading_station_overdue_std = 0;
        $stats_loading_station_overdue_exp = 0;
        $stats_loading_station_overdue_delivery = 0;
        $stats_loading_station_overdue_store = 0;
        $stats_loading_station_later_std = 0;
        $stats_loading_station_later_exp = 0;
        $stats_loading_station_later_delivery = 0;
        $stats_loading_station_later_store = 0;

        $stats_conveyor_overdue_std = 0;
        $stats_conveyor_overdue_exp = 0;
        $stats_conveyor_overdue_delivery = 0;
        $stats_conveyor_overdue_store = 0;
        $stats_conveyor_later_std = 0;
        $stats_conveyor_later_exp = 0;
        $stats_conveyor_later_delivery = 0;
        $stats_conveyor_later_store = 0;

        $stats_storage_overdue_std = 0;
        $stats_storage_overdue_exp = 0;
        $stats_storage_overdue_delivery = 0;
        $stats_storage_overdue_store = 0;
        $stats_storage_later_std = 0;
        $stats_storage_later_exp = 0;
        $stats_storage_later_delivery = 0;
        $stats_storage_later_store = 0;

        $stats_ccin_overdue_std = 0;
        $stats_ccin_overdue_exp = 0;
        $stats_ccin_overdue_delivery = 0;
        $stats_ccin_overdue_store = 0;
        $stats_ccin_later_std = 0;
        $stats_ccin_later_exp = 0;
        $stats_ccin_later_delivery = 0;
        $stats_ccin_later_store = 0;


        if(!empty($other_stats)) {

            foreach ($other_stats as $k => $v) {

                if($v->PromisedDate < $today) {

                     if($v->PartnerINOUT==1||in_array($v->nextpost,$allpost)){

                        if (in_array($v->express, $exp_index)) {
                            $overdue_exp += 1;
                        } else {
                            $overdue_std += 1;
                        }
                     }

                     if($v->PartnerINOUT==1) { //Partner IN
                        if($v->store=='DELIVERY'){
                            $stats_partnerin_overdue_delivery+=1;
                        }else{
                            $stats_partnerin_overdue_store+=1;
                        }
                    }else{
                        if(in_array($v->nextpost,$post_shelving)){//shelving
                            if($v->store=='DELIVERY'){
                                $stats_shelving_overdue_delivery+=1;
                            }else{
                                $stats_shelving_overdue_store+=1;
                            }
                        }
                        if(in_array($v->nextpost,$post_tailoring)){//taloring
                            if($v->store=='DELIVERY'){
                                $stats_tailoring_overdue_delivery+=1;
                            }else{
                                $stats_tailoring_overdue_store+=1;
                            }
                        }
                        if(in_array($v->nextpost,$post_spotting)){//cleaning
                            if($v->store=='DELIVERY'){
                                $stats_spotting_overdue_delivery+=1;
                            }else{
                                $stats_spotting_overdue_store+=1;
                            }
                        }
                        if(in_array($v->nextpost,$post_qc1)){//qc1
                            if($v->store=='DELIVERY'){
                                $stats_qc1_overdue_delivery+=1;
                            }else{
                                $stats_qc1_overdue_store+=1;
                            }
                        }
                        if(in_array($v->nextpost,$post_pressing)){//pressing
                            if($v->store=='DELIVERY'){
                                $stats_pressing_overdue_delivery+=1;
                            }else{
                                $stats_pressing_overdue_store+=1;
                            }
                        }
                        if(in_array($v->nextpost,$post_qc2)){//qc2
                            if($v->store=='DELIVERY'){
                                $stats_qc2_overdue_delivery+=1;
                            }else{
                                $stats_qc2_overdue_store+=1;
                            }
                        }
                        if(in_array($v->nextpost,$post_loading_station)){//loading station
                            if($v->store=='DELIVERY'){
                                $stats_loading_station_overdue_delivery+=1;
                            }else{
                                $stats_loading_station_overdue_store+=1;
                            }
                        }
                        if(in_array($v->nextpost,$post_conveyor)){//conveyor
                            if($v->store=='DELIVERY'){
                                $stats_conveyor_overdue_delivery+=1;
                            }else{
                                $stats_conveyor_overdue_store+=1;
                            }
                        }
                        if(in_array($v->nextpost,$post_storage)){//storage
                            if($v->store=='DELIVERY'){
                                $stats_storage_overdue_delivery+=1;
                            }else{
                                $stats_storage_overdue_store+=1;
                            }
                        }
                        if(in_array($v->nextpost,$post_ccin)){//ccin
                            if($v->store=='DELIVERY'){
                                $stats_ccin_overdue_delivery+=1;
                            }else{
                                $stats_ccin_overdue_store+=1;
                            }
                        }
                    }

                        if (in_array($v->express, $exp_index)) {
                            if($v->PartnerINOUT==1) { //Partner IN
                                $stats_partnerin_overdue_exp += 1;

                            }else{

                                if(in_array($v->nextpost,$post_shelving)){ //shelving
                                    $stats_shelving_overdue_exp+=1;
                                }
                                if(in_array($v->nextpost,$post_tailoring)){ //tailoring
                                    $stats_tailoring_overdue_exp+=1;
                                }

                                if(in_array($v->nextpost,$post_spotting)){ //cleaning
                                    $stats_spotting_overdue_exp+=1;
                                }
                                if(in_array($v->nextpost,$post_qc1)){ //qc1
                                    $stats_qc1_overdue_exp+=1;
                                }
                                if(in_array($v->nextpost,$post_pressing)){ //pressing
                                    $stats_pressing_overdue_exp+=1;
                                }
                                if(in_array($v->nextpost,$post_qc2)){ //qc2
                                    $stats_qc2_overdue_exp+=1;
                                }
                                if(in_array($v->nextpost,$post_loading_station)){ //loading station
                                    $stats_loading_station_overdue_exp+=1;
                                }
                                if(in_array($v->nextpost,$post_conveyor)){ //conveyor
                                    $stats_conveyor_overdue_exp+=1;
                                }
                                if(in_array($v->nextpost,$post_storage)){ //storage
                                    $stats_storage_overdue_exp+=1;
                                }
                                if(in_array($v->nextpost,$post_ccin)){ //ccin
                                    $stats_ccin_overdue_exp+=1;
                                }
                            }
                        } else {
                            if($v->PartnerINOUT==1) { //Partner IN

                                $stats_partnerin_overdue_std += 1;
                            }else{
                                if(in_array($v->nextpost,$post_shelving)){//shelving
                                    $stats_shelving_overdue_std+=1;
                                }

                                if(in_array($v->nextpost,$post_tailoring)){//tailoring
                                    $stats_tailoring_overdue_std+=1;
                                }
                                if(in_array($v->nextpost,$post_spotting)){//cleaning
                                    $stats_spotting_overdue_std+=1;
                                }
                                if(in_array($v->nextpost,$post_qc1)){//qc1
                                    $stats_qc1_overdue_std+=1;
                                }
                                if(in_array($v->nextpost,$post_pressing)){//pressing
                                    $stats_pressing_overdue_std+=1;
                                }
                                if(in_array($v->nextpost,$post_qc2)){//qc2
                                    $stats_qc2_overdue_std+=1;
                                }
                                if(in_array($v->nextpost,$post_loading_station)){//loading station
                                    $stats_loading_station_overdue_std+=1;
                                }
                                if(in_array($v->nextpost,$post_conveyor)){//conveyor
                                    $stats_conveyor_overdue_std+=1;
                                }
                                if(in_array($v->nextpost,$post_storage)){//storage
                                    $stats_storage_overdue_std+=1;
                                }
                                if(in_array($v->nextpost,$post_ccin)){//ccin
                                    $stats_ccin_overdue_std+=1;
                                }
                            }
                        }

                }
                elseif($v->PromisedDate > $tomorrow) {

                    if($v->PartnerINOUT==1||in_array($v->nextpost,$allpost))
                        if (in_array($v->express, $exp_index)) {
                            $later_exp += 1;
                        } else {
                            $later_std += 1;
                        }
                        if($v->PartnerINOUT==1) { //Partner IN
                            if($v->store=='DELIVERY'){
                                $stats_partnerin_later_delivery+=1;
                            }else{
                                $stats_partnerin_later_store+=1;
                            }
                        }else{
                            if(in_array($v->nextpost,$post_shelving)){//shelving
                                if($v->store=='DELIVERY'){
                                    $stats_shelving_later_delivery+=1;
                                }else{
                                    $stats_shelving_later_store+=1;
                                }
                            }
                            if(in_array($v->nextpost,$post_tailoring)){//taloring
                                if($v->store=='DELIVERY'){
                                    $stats_tailoring_later_delivery+=1;
                                }else{
                                    $stats_tailoring_later_store+=1;
                                }
                            }
                            if(in_array($v->nextpost,$post_spotting)){//cleaning
                                if($v->store=='DELIVERY'){
                                    $stats_spotting_later_delivery+=1;
                                }else{
                                    $stats_spotting_later_store+=1;
                                }
                            }
                            if(in_array($v->nextpost,$post_qc1)){//qc1
                                if($v->store=='DELIVERY'){
                                    $stats_qc1_later_delivery+=1;
                                }else{
                                    $stats_qc1_later_store+=1;
                                }
                            }
                            if(in_array($v->nextpost,$post_pressing)){//pressing
                                if($v->store=='DELIVERY'){
                                    $stats_pressing_later_delivery+=1;
                                }else{
                                    $stats_pressing_later_store+=1;
                                }
                            }
                            if(in_array($v->nextpost,$post_qc2)){//qc2
                                if($v->store=='DELIVERY'){
                                    $stats_qc2_later_delivery+=1;
                                }else{
                                    $stats_qc2_later_store+=1;
                                }
                            }
                            if(in_array($v->nextpost,$post_loading_station)){//loading station
                                if($v->store=='DELIVERY'){
                                    $stats_loading_station_later_delivery+=1;
                                }else{
                                    $stats_loading_station_later_store+=1;
                                }
                            }
                            if(in_array($v->nextpost,$post_conveyor)){//conveyor
                                if($v->store=='DELIVERY'){
                                    $stats_conveyor_later_delivery+=1;
                                }else{
                                    $stats_conveyor_later_store+=1;
                                }
                            }
                            if(in_array($v->nextpost,$post_storage)){//storage
                                if($v->store=='DELIVERY'){
                                    $stats_storage_later_delivery+=1;
                                }else{
                                    $stats_storage_later_store+=1;
                                }
                            }
                            if(in_array($v->nextpost,$post_ccin)){//ccin
                                if($v->store=='DELIVERY'){
                                    $stats_ccin_later_delivery+=1;
                                }else{
                                    $stats_ccin_later_store+=1;
                                }
                            }
                        }


                        if (in_array($v->express, $exp_index)) {
                            if($v->PartnerINOUT==1) { //Partner IN
                                $stats_partnerin_later_exp += 1;
                            }else{
                                if(in_array($v->nextpost,$post_shelving)){//shelving
                                    $stats_shelving_later_exp+=1;
                                }

                                if(in_array($v->nextpost,$post_tailoring)){//tailoring
                                    $stats_tailoring_later_exp+=1;
                                }

                                if(in_array($v->nextpost,$post_spotting)){//cleaning
                                    $stats_spotting_later_exp+=1;
                                }

                                if(in_array($v->nextpost,$post_qc1)){//qc1
                                    $stats_qc1_later_exp+=1;
                                }
                                if(in_array($v->nextpost,$post_pressing)){//pressing
                                    $stats_pressing_later_exp+=1;
                                }

                                if(in_array($v->nextpost,$post_qc2)){//qc2
                                    $stats_qc2_later_exp+=1;
                                }

                                if(in_array($v->nextpost,$post_loading_station)){//loading station
                                    $stats_loading_station_later_exp+=1;
                                }

                                if(in_array($v->nextpost,$post_conveyor)){//conveyor
                                    $stats_conveyor_later_exp+=1;
                                }

                                if(in_array($v->nextpost,$post_storage)){//storage
                                    $stats_storage_later_exp+=1;
                                }
                                if(in_array($v->nextpost,$post_ccin)){//ccin
                                    $stats_ccin_later_exp+=1;
                                }
                            }
                        } else {
                            if($v->PartnerINOUT==1) { //Partner IN
                                $stats_partnerin_later_std += 1;
                            }else{
                                if(in_array($v->nextpost,$post_shelving)){//shelving
                                    $stats_shelving_later_std+=1;
                                }

                                if(in_array($v->nextpost,$post_tailoring)){//tailoring
                                    $stats_tailoring_later_std+=1;
                                }

                                if(in_array($v->nextpost,$post_spotting)){//cleaning
                                    $stats_spotting_later_std+=1;
                                }
                                if(in_array($v->nextpost,$post_qc1)){//qc1
                                    $stats_qc1_later_std+=1;
                                }
                                if(in_array($v->nextpost,$post_pressing)){//pressing
                                    $stats_pressing_later_std+=1;
                                }
                                if(in_array($v->nextpost,$post_qc2)){//qc2
                                    $stats_qc2_later_std+=1;
                                }
                                if(in_array($v->nextpost,$post_loading_station)){//loading station
                                    $stats_loading_station_later_std+=1;
                                }
                                if(in_array($v->nextpost,$post_conveyor)){//conveyor
                                    $stats_conveyor_later_std+=1;
                                }
                                if(in_array($v->nextpost,$post_storage)){//storage
                                    $stats_storage_later_std+=1;
                                }
                                if(in_array($v->nextpost,$post_ccin)){//ccin
                                    $stats_ccin_later_std+=1;
                                }
                            }
                        }

                }

            }
        }

        $stats_per_postes_overdue['Partner']['std'] =  $stats_partnerin_overdue_std;
        $stats_per_postes_overdue['Partner']['exp'] = $stats_partnerin_overdue_exp;
        $stats_per_postes_overdue['Partner']['all'] =  $stats_partnerin_overdue_std+$stats_partnerin_overdue_exp;
        $stats_per_postes_overdue['Partner']['delivery'] =  $stats_partnerin_overdue_delivery;
        $stats_per_postes_overdue['Partner']['store'] = $stats_partnerin_overdue_store;
        $stats_per_postes_later['Partner']['std'] = $stats_partnerin_later_std;
        $stats_per_postes_later['Partner']['exp'] =$stats_partnerin_later_exp;
        $stats_per_postes_later['Partner']['all'] = $stats_partnerin_later_std+$stats_partnerin_later_exp;
        $stats_per_postes_later['Partner']['delivery'] = $stats_partnerin_later_delivery;
        $stats_per_postes_later['Partner']['store'] =$stats_partnerin_later_store;


        $stats_per_postes_overdue['shelving']['std'] =  $stats_shelving_overdue_std;
        $stats_per_postes_overdue['shelving']['exp'] =  $stats_shelving_overdue_exp;
        $stats_per_postes_overdue['shelving']['all'] =  $stats_shelving_overdue_std+$stats_shelving_overdue_exp;
        $stats_per_postes_overdue['shelving']['delivery'] =  $stats_shelving_overdue_delivery;
        $stats_per_postes_overdue['shelving']['store'] =  $stats_shelving_overdue_store;
        $stats_per_postes_later['shelving']['std'] = $stats_shelving_later_std;
        $stats_per_postes_later['shelving']['exp'] =  $stats_shelving_later_exp;
        $stats_per_postes_later['shelving']['all'] = $stats_shelving_later_std+$stats_shelving_later_exp;
        $stats_per_postes_later['shelving']['delivery'] = $stats_shelving_later_delivery;
        $stats_per_postes_later['shelving']['store'] =  $stats_shelving_later_store;


        $stats_per_postes_overdue['Tailoring']['std'] =  $stats_tailoring_overdue_std;
        $stats_per_postes_overdue['Tailoring']['exp'] =  $stats_tailoring_overdue_exp;
        $stats_per_postes_overdue['Tailoring']['all'] =  $stats_tailoring_overdue_std+ $stats_tailoring_overdue_exp;
        $stats_per_postes_overdue['Tailoring']['delivery'] =  $stats_tailoring_overdue_delivery;
        $stats_per_postes_overdue['Tailoring']['store'] =  $stats_tailoring_overdue_store;
        $stats_per_postes_later['Tailoring']['std'] = $stats_tailoring_later_std;
        $stats_per_postes_later['Tailoring']['exp'] =  $stats_tailoring_later_exp;
        $stats_per_postes_later['Tailoring']['all'] = $stats_tailoring_later_std+$stats_tailoring_later_exp;
        $stats_per_postes_later['Tailoring']['delivery'] = $stats_tailoring_later_delivery;
        $stats_per_postes_later['Tailoring']['store'] =  $stats_tailoring_later_store;

        $stats_per_postes_overdue['Spotting']['std'] =  $stats_spotting_overdue_std;
        $stats_per_postes_overdue['Spotting']['exp'] =  $stats_spotting_overdue_exp;
        $stats_per_postes_overdue['Spotting']['all'] =  $stats_spotting_overdue_std+$stats_spotting_overdue_exp;
        $stats_per_postes_overdue['Spotting']['delivery'] =  $stats_spotting_overdue_delivery;
        $stats_per_postes_overdue['Spotting']['store'] =  $stats_spotting_overdue_store;
        $stats_per_postes_later['Spotting']['std'] = $stats_spotting_later_std;
        $stats_per_postes_later['Spotting']['exp'] =  $stats_spotting_later_exp;
        $stats_per_postes_later['Spotting']['all'] = $stats_spotting_later_std+$stats_spotting_later_exp;
        $stats_per_postes_later['Spotting']['delivery'] = $stats_spotting_later_delivery;
        $stats_per_postes_later['Spotting']['store'] =  $stats_spotting_later_store;

        $stats_per_postes_overdue['Quality Control 1']['std'] =  $stats_qc1_overdue_std;
        $stats_per_postes_overdue['Quality Control 1']['exp'] =  $stats_qc1_overdue_exp;
        $stats_per_postes_overdue['Quality Control 1']['all'] =  $stats_qc1_overdue_std+$stats_qc1_overdue_exp;
        $stats_per_postes_overdue['Quality Control 1']['delivery'] =  $stats_qc1_overdue_delivery;
        $stats_per_postes_overdue['Quality Control 1']['store'] =  $stats_qc1_overdue_store;
        $stats_per_postes_later['Quality Control 1']['std'] = $stats_qc1_later_std;
        $stats_per_postes_later['Quality Control 1']['exp'] =  $stats_qc1_later_exp;
        $stats_per_postes_later['Quality Control 1']['all'] = $stats_qc1_later_std+$stats_qc1_later_exp;
        $stats_per_postes_later['Quality Control 1']['delivery'] = $stats_qc1_later_delivery;
        $stats_per_postes_later['Quality Control 1']['store'] =  $stats_qc1_later_store;


        $stats_per_postes_overdue['Pressing']['std'] =  $stats_pressing_overdue_std;
        $stats_per_postes_overdue['Pressing']['exp'] =  $stats_pressing_overdue_exp;
        $stats_per_postes_overdue['Pressing']['all'] =  $stats_pressing_overdue_std+$stats_pressing_overdue_exp;
        $stats_per_postes_overdue['Pressing']['delivery'] =  $stats_pressing_overdue_delivery;
        $stats_per_postes_overdue['Pressing']['store'] =  $stats_pressing_overdue_store;
        $stats_per_postes_later['Pressing']['std'] = $stats_pressing_later_std;
        $stats_per_postes_later['Pressing']['exp'] =  $stats_pressing_later_exp;
        $stats_per_postes_later['Pressing']['all'] = $stats_pressing_later_std+$stats_pressing_later_exp;
        $stats_per_postes_later['Pressing']['delivery'] = $stats_pressing_later_delivery;
        $stats_per_postes_later['Pressing']['store'] =  $stats_pressing_later_store;

        $stats_per_postes_overdue['Quality Control 2']['std'] =  $stats_qc2_overdue_std;
        $stats_per_postes_overdue['Quality Control 2']['exp'] =  $stats_qc2_overdue_exp;
        $stats_per_postes_overdue['Quality Control 2']['all'] =  $stats_qc2_overdue_std+$stats_qc2_overdue_exp;
        $stats_per_postes_overdue['Quality Control 2']['delivery'] =  $stats_qc2_overdue_delivery;
        $stats_per_postes_overdue['Quality Control 2']['store'] =  $stats_qc2_overdue_store;
        $stats_per_postes_later['Quality Control 2']['std'] = $stats_qc2_later_std;
        $stats_per_postes_later['Quality Control 2']['exp'] =  $stats_qc2_later_exp;
        $stats_per_postes_later['Quality Control 2']['all'] = $stats_qc2_later_std+$stats_qc2_later_exp;
        $stats_per_postes_later['Quality Control 2']['delivery'] = $stats_qc2_later_delivery;
        $stats_per_postes_later['Quality Control 2']['store'] =  $stats_qc2_later_store;


        $stats_per_postes_overdue['Loading Station']['std'] =  $stats_loading_station_overdue_std;
        $stats_per_postes_overdue['Loading Station']['exp'] =  $stats_loading_station_overdue_exp;
        $stats_per_postes_overdue['Loading Station']['all'] =  $stats_loading_station_overdue_std+$stats_loading_station_overdue_exp;
        $stats_per_postes_overdue['Loading Station']['delivery'] =  $stats_loading_station_overdue_delivery;
        $stats_per_postes_overdue['Loading Station']['store'] =  $stats_loading_station_overdue_store;
        $stats_per_postes_later['Loading Station']['std'] = $stats_loading_station_later_std;
        $stats_per_postes_later['Loading Station']['exp'] =  $stats_loading_station_later_exp;
        $stats_per_postes_later['Loading Station']['all'] = $stats_loading_station_later_std+$stats_loading_station_later_exp;
        $stats_per_postes_later['Loading Station']['delivery'] = $stats_loading_station_later_delivery;
        $stats_per_postes_later['Loading Station']['store'] =  $stats_loading_station_later_store;

        $stats_per_postes_overdue['Conveyor']['std'] =  $stats_conveyor_overdue_std;
        $stats_per_postes_overdue['Conveyor']['exp'] =  $stats_conveyor_overdue_exp;
        $stats_per_postes_overdue['Conveyor']['all'] =  $stats_conveyor_overdue_std+$stats_conveyor_overdue_exp;
        $stats_per_postes_overdue['Conveyor']['delivery'] =  $stats_conveyor_overdue_delivery;
        $stats_per_postes_overdue['Conveyor']['store'] =  $stats_conveyor_overdue_store;
        $stats_per_postes_later['Conveyor']['std'] = $stats_conveyor_later_std;
        $stats_per_postes_later['Conveyor']['exp'] =  $stats_conveyor_later_exp;
        $stats_per_postes_later['Conveyor']['all'] = $stats_conveyor_later_std+$stats_conveyor_later_exp;
        $stats_per_postes_later['Conveyor']['delivery'] = $stats_conveyor_later_delivery;
        $stats_per_postes_later['Conveyor']['store'] =  $stats_conveyor_later_store;

        $stats_per_postes_overdue['Storage']['std'] =  $stats_storage_overdue_std;
        $stats_per_postes_overdue['Storage']['exp'] =  $stats_storage_overdue_exp;
        $stats_per_postes_overdue['Storage']['all'] =  $stats_storage_overdue_std+$stats_storage_overdue_exp;
        $stats_per_postes_overdue['Storage']['delivery'] =  $stats_storage_overdue_delivery;
        $stats_per_postes_overdue['Storage']['store'] =  $stats_storage_overdue_store;
        $stats_per_postes_later['Storage']['std'] = $stats_storage_later_std;
        $stats_per_postes_later['Storage']['exp'] =  $stats_storage_later_exp;
        $stats_per_postes_later['Storage']['all'] = $stats_storage_later_std+$stats_storage_later_exp;
        $stats_per_postes_later['Storage']['delivery'] = $stats_storage_later_delivery;
        $stats_per_postes_later['Storage']['store'] =  $stats_storage_later_store;

        $stats_per_postes_overdue['CustomerCare']['std'] =  $stats_ccin_overdue_std;
        $stats_per_postes_overdue['CustomerCare']['exp'] =  $stats_ccin_overdue_exp;
        $stats_per_postes_overdue['CustomerCare']['all'] =  $stats_ccin_overdue_std+$stats_ccin_overdue_exp;
        $stats_per_postes_overdue['CustomerCare']['delivery'] =  $stats_ccin_overdue_delivery;
        $stats_per_postes_overdue['CustomerCare']['store'] =  $stats_ccin_overdue_store;
        $stats_per_postes_later['CustomerCare']['std'] = $stats_ccin_later_std;
        $stats_per_postes_later['CustomerCare']['exp'] =  $stats_ccin_later_exp;
        $stats_per_postes_later['CustomerCare']['all'] = $stats_ccin_later_std+ $stats_ccin_later_exp;
        $stats_per_postes_later['CustomerCare']['delivery'] = $stats_ccin_later_delivery;
        $stats_per_postes_later['CustomerCare']['store'] =  $stats_ccin_later_store;

        $main_stats->due_today_std = $due_today_std;
        $main_stats->due_today_exp = $due_today_exp;
        $main_stats->total_due_today = $due_today_std+$due_today_exp;

        $done_today= $stats_per_postes_today['Storage']['all']+$stats_per_postes_today['shelving']['all'];

        $main_stats->inprocess_today = $main_stats->total_due_today - $done_today;

        $percent_inprocess_today = 0;
        $percent_done_today = 0;
        $percent_std_today = 0;
        $percent_exp_today = 0;

        if($main_stats->total_due_today > 0){
            $percent_std_today = ($due_today_std/$main_stats->total_due_today)*100;
            $percent_exp_today = ($due_today_exp/$main_stats->total_due_today)*100;
        }else{
            $percent_std_today = 50;
            $percent_exp_today = 50;
        }

        $main_stats->percent_std_today =  round($percent_std_today,2);
        $main_stats->percent_exp_today =  round($percent_exp_today,2);


        if($main_stats->total_due_today > 0) {
            $percent_inprocess_today = ($main_stats->inprocess_today / $main_stats->total_due_today) * 100;
            $percent_done_today = ($done_today / $main_stats->total_due_today) * 100;
        }else{

        }

        $main_stats->percent_inprocess_today =  round($percent_inprocess_today,2);
        $main_stats->percent_done_today =  round($percent_done_today,2);



        $main_stats->due_tomorrow_std = $due_tomorrow_std;
        $main_stats->due_tomorrow_exp = $due_tomorrow_exp;
        $main_stats->total_due_tomorrow = $due_tomorrow_std+$due_tomorrow_exp;

        $done_tomorrow= $stats_per_postes_tomorrow['Storage']['all']+$stats_per_postes_tomorrow['shelving']['all'];

        $main_stats->inprocess_tomorrow = $main_stats->total_due_tomorrow - $done_tomorrow;

        $percent_inprocess_tomorrow = 0;
        $percent_done_tomorrow = 0;
        $percent_std_tomorrow = 0;
        $percent_exp_tomorrow = 0;

        if($main_stats->total_due_tomorrow > 0){
            $percent_std_tomorrow = ($due_tomorrow_std/$main_stats->total_due_tomorrow)*100;
            $percent_exp_tomorrow = ($due_tomorrow_exp/$main_stats->total_due_tomorrow)*100;
        }else{
            $percent_std_tomorrow = 50;
            $percent_exp_tomorrow = 50;
        }

        $main_stats->percent_std_tomorrow =  round($percent_std_tomorrow,2);
        $main_stats->percent_exp_tomorrow =  round($percent_exp_tomorrow,2);


        if($main_stats->total_due_tomorrow > 0) {

            $percent_inprocess_tomorrow = ($main_stats->inprocess_tomorrow / $main_stats->total_due_tomorrow) * 100;
            $percent_done_tomorrow = ($done_tomorrow / $main_stats->total_due_tomorrow) * 100;
        }else{

        }


        $main_stats->percent_inprocess_tomorrow = round($percent_inprocess_tomorrow,2);
        $main_stats->percent_done_tomorrow = round($percent_done_tomorrow,2);

        //overdue
        $main_stats->overdue_std = $overdue_std;
        $main_stats->overdue_exp = $overdue_exp;

        // later
        $main_stats->later_std  = $later_std;
        $main_stats->later_exp = $later_exp;
        $main_stats->total_due_later = $later_std + $later_exp;

        $done_later= $stats_per_postes_later['Storage']['all'] + $stats_per_postes_later['shelving']['all'];

        $main_stats->inprocess_later = $main_stats->total_due_later - $done_later;

        $percent_inprocess_later = 0;
        $percent_done_later = 0;
        $percent_std_later = 0;
        $percent_exp_later = 0;

        if($main_stats->total_due_later > 0){
            $percent_std_later = ($later_std/$main_stats->total_due_later)*100;
            $percent_exp_later = ($later_exp/$main_stats->total_due_later)*100;
        }else{
            $percent_std_later = 50;
            $percent_exp_later = 50;
        }

        $main_stats->percent_std_later =  round($percent_std_later,2);
        $main_stats->percent_exp_later =  round($percent_exp_later,2);


        if($main_stats->total_due_later > 0) {

            $percent_inprocess_later = ($main_stats->inprocess_later / $main_stats->total_due_later) * 100;
            $percent_done_later = ($done_later / $main_stats->total_due_later) * 100;
        }else{

        }


        $main_stats->percent_inprocess_later = round($percent_inprocess_later,2);
        $main_stats->percent_done_later = round($percent_done_later,2);

        // new stats for today
        $main_stats->due_today_deliveries = Db::table('infoitems')->where('PromisedDate',$today)->where('StoreName','ATELIER')->count();
        $main_stats->due_today_stores = Db::table('infoitems')->where('PromisedDate',$today)->where('StoreName','!=','ATELIER')->count();
        $main_stats->total_due_today = $main_stats->due_today_deliveries + $main_stats->due_today_stores;

        $main_stats->percent_today_deliveries=
        $main_stats->total_due_today >0 ? round(($main_stats->due_today_deliveries/$main_stats->total_due_today)*100,2) : 50;
        $main_stats->percent_today_stores=
        $main_stats->total_due_today >0 ? round(($main_stats->due_today_stores/$main_stats->total_due_today)*100,2) : 50;
        if($main_stats->due_today_deliveries > 0) {
        $main_stats->percent_today_done_deliveries = round(
            (Db::table('infoitems')
            ->where('PromisedDate',$today)
            ->where('StoreName','ATELIER')
            ->whereIn('Status',['READY','ON VAN','OFFLOADED', 'DELIVERED', 'SOLD', 'FULFILLED'])->count()
             / $main_stats->due_today_deliveries) * 100,2);
        $main_stats->percent_today_inprocess_deliveries =  round(
             (Db::table('infoitems')
             ->where('PromisedDate',$today)
             ->where('StoreName','ATELIER')
             ->whereNotIn('Status',['READY','ON VAN','OFFLOADED', 'DELIVERED', 'SOLD', 'FULFILLED'])->count()
             / $main_stats->due_today_deliveries) * 100,2);
        }else{
            $main_stats->percent_today_done_deliveries=50;
            $main_stats->percent_today_inprocess_deliveries=50;
        }
        if($main_stats->due_today_stores > 0) {
        $main_stats->percent_today_done_stores =  round(
            (Db::table('infoitems')
            ->where('PromisedDate',$today)
            ->where('StoreName','!=','ATELIER')
            ->whereIn('Status',['READY','ON VAN','OFFLOADED', 'DELIVERED', 'SOLD', 'FULFILLED'])->count()
            / $main_stats->due_today_stores) * 100,2);
        $main_stats->percent_today_inprocess_stores =  round(
            (Db::table('infoitems')
            ->where('PromisedDate',$today)
            ->where('StoreName','!=','ATELIER')
            ->whereNotIn('Status',['READY','ON VAN','OFFLOADED', 'DELIVERED', 'SOLD', 'FULFILLED'])->count()
            / $main_stats->due_today_stores) * 100,2);
        }else{
            $main_stats->percent_today_done_stores=50;
            $main_stats->percent_today_inprocess_stores=50;
        }
        // new stats for tomorrow

        $main_stats->due_tomorrow_deliveries = Db::table('infoitems')->where('PromisedDate',$tomorrow)->where('StoreName','ATELIER')->count();
        $main_stats->due_tomorrow_stores = Db::table('infoitems')->where('PromisedDate',$tomorrow)->where('StoreName','!=','ATELIER')->count();
        $main_stats->total_due_tomorrow = $main_stats->due_tomorrow_deliveries + $main_stats->due_tomorrow_stores;

        $main_stats->percent_tomorrow_deliveries=
        $main_stats->total_due_tomorrow > 0? round(($main_stats->due_tomorrow_deliveries/$main_stats->total_due_tomorrow)*100,2): 50;
        $main_stats->percent_tomorrow_stores=
        $main_stats->total_due_tomorrow > 0? round(($main_stats->due_tomorrow_stores/$main_stats->total_due_tomorrow)*100,2) : 50;

        if($main_stats->due_tomorrow_deliveries > 0) {
        $main_stats->percent_tomorrow_done_deliveries = round(
            (Db::table('infoitems')
            ->where('PromisedDate',$tomorrow)
            ->where('StoreName','ATELIER')
            ->whereIn('Status',['READY','ON VAN','OFFLOADED', 'DELIVERED', 'SOLD', 'FULFILLED'])->count()
             / $main_stats->due_tomorrow_deliveries) * 100,2);
        $main_stats->percent_tomorrow_inprocess_deliveries =  round(
             (Db::table('infoitems')
             ->where('PromisedDate',$tomorrow)
             ->where('StoreName','ATELIER')
             ->whereNotIn('Status',['READY','ON VAN','OFFLOADED', 'DELIVERED', 'SOLD', 'FULFILLED'])->count()
             / $main_stats->due_tomorrow_deliveries) * 100,2);
        } else{
            $main_stats->percent_tomorrow_done_deliveries=50;
            $main_stats->percent_tomorrow_inprocess_deliveries=50;
        }
        if($main_stats->due_tomorrow_stores > 0) {
            $main_stats->percent_tomorrow_done_stores =  round(
                (Db::table('infoitems')
                ->where('PromisedDate',$tomorrow)
                ->where('StoreName','!=','ATELIER')
                ->whereIn('Status',['READY','ON VAN','OFFLOADED', 'DELIVERED', 'SOLD', 'FULFILLED'])->count()
                / $main_stats->due_tomorrow_stores) * 100,2);
            $main_stats->percent_tomorrow_inprocess_stores =  round(
                (Db::table('infoitems')
                ->where('PromisedDate',$tomorrow)
                ->where('StoreName','!=','ATELIER')
                ->whereNotIn('Status',['READY','ON VAN','OFFLOADED', 'DELIVERED', 'SOLD', 'FULFILLED'])->count()
                / $main_stats->due_tomorrow_stores) * 100,2);
        }else{
            $main_stats->percent_tomorrow_done_stores=50;
            $main_stats->percent_tomorrow_inprocess_stores=50;
        }
        // main stats for later
        
        $main_stats->due_later_deliveries = Db::table('infoitems')->where('PromisedDate', '>',$tomorrow)->where('StoreName','ATELIER')->count();
        $main_stats->due_later_stores = Db::table('infoitems')->where('PromisedDate', '>',$tomorrow)->where('StoreName','!=','ATELIER')->count();
        $main_stats->total_due_later = $main_stats->due_later_deliveries + $main_stats->due_later_stores;

        $main_stats->percent_later_deliveries=
        $main_stats->total_due_later > 0 ? round(($main_stats->due_later_deliveries/$main_stats->total_due_later)*100,2): 50;
        $main_stats->percent_later_stores =
        $main_stats->total_due_later > 0 ? round(($main_stats->due_later_stores/$main_stats->total_due_later)*100,2) : 50;

        if($main_stats->due_later_deliveries > 0) {
            $main_stats->percent_later_done_deliveries = round(
                (Db::table('infoitems')
                ->where('PromisedDate', '>', $tomorrow)
                ->where('StoreName','ATELIER')
                ->whereIn('Status',['READY','ON VAN','OFFLOADED', 'DELIVERED', 'SOLD', 'FULFILLED'])->count()
                / $main_stats->due_later_deliveries) * 100,2);
            $main_stats->percent_later_inprocess_deliveries = round(
                (Db::table('infoitems')
                ->where('PromisedDate', '>', $tomorrow)
                ->where('StoreName','ATELIER')
                ->whereNotIn('Status',['READY','ON VAN','OFFLOADED', 'DELIVERED', 'SOLD', 'FULFILLED'])->count()
                / $main_stats->due_later_deliveries) * 100,2);
        } else{
            $main_stats->percent_later_done_deliveries=50;
            $main_stats->percent_later_inprocess_deliveries=50;
        }
        if($main_stats->due_later_stores > 0) {
            $main_stats->percent_later_done_stores =  round(
                (Db::table('infoitems')
                ->where('PromisedDate', '>', $tomorrow)
                ->where('StoreName','!=','ATELIER')
                ->whereIn('Status',['READY','ON VAN','OFFLOADED', 'DELIVERED', 'SOLD', 'FULFILLED'])->count()
                / $main_stats->due_later_stores) * 100,2);
            $main_stats->percent_later_inprocess_stores =  round(
                (Db::table('infoitems')
                ->where('PromisedDate', '>', $tomorrow)
                ->where('StoreName','!=','ATELIER')
                ->whereNotIn('Status',['READY','ON VAN','OFFLOADED', 'DELIVERED', 'SOLD', 'FULFILLED'])->count()
                / $main_stats->due_later_stores) * 100,2);
        }else{
            $main_stats->percent_later_done_stores=50;
            $main_stats->percent_later_inprocess_stores=50;
        }

        // new stats for total row
        $stats_total['Loading Station']=
            $stats_per_postes_today['Loading Station']['all']+
            $stats_per_postes_tomorrow['Loading Station']['all']+
            $stats_per_postes_overdue['Loading Station']['all']+
            $stats_per_postes_later['Loading Station']['all'];
        $stats_total['Conveyor']=
            $stats_per_postes_today['Conveyor']['all']+
            $stats_per_postes_tomorrow['Conveyor']['all']+
            $stats_per_postes_overdue['Conveyor']['all']+
            $stats_per_postes_later['Conveyor']['all'];
        $stats_total['CustomerCare']=
            $stats_per_postes_today['CustomerCare']['all']+
            $stats_per_postes_tomorrow['CustomerCare']['all']+
            $stats_per_postes_overdue['CustomerCare']['all']+
            $stats_per_postes_later['CustomerCare']['all'];
        $stats_total['Partner']=
            $stats_per_postes_today['Partner']['all']+
            $stats_per_postes_tomorrow['Partner']['all']+
            $stats_per_postes_overdue['Partner']['all']+
            $stats_per_postes_later['Partner']['all'];
        $stats_total['Pressing']=
            $stats_per_postes_today['Pressing']['all']+
            $stats_per_postes_tomorrow['Pressing']['all']+
            $stats_per_postes_overdue['Pressing']['all']+
            $stats_per_postes_later['Pressing']['all'];
        $stats_total['Quality Control 1']=
            $stats_per_postes_today['Quality Control 1']['all']+
            $stats_per_postes_tomorrow['Quality Control 1']['all']+
            $stats_per_postes_overdue['Quality Control 1']['all']+
            $stats_per_postes_later['Quality Control 1']['all'];
        $stats_total['Quality Control 2']=
            $stats_per_postes_today['Quality Control 2']['all']+
            $stats_per_postes_tomorrow['Quality Control 2']['all']+
            $stats_per_postes_overdue['Quality Control 2']['all']+
            $stats_per_postes_later['Quality Control 2']['all'];
        $stats_total['Spotting']=
            $stats_per_postes_today['Spotting']['all']+
            $stats_per_postes_tomorrow['Spotting']['all']+
            $stats_per_postes_overdue['Spotting']['all']+
            $stats_per_postes_later['Spotting']['all'];
        $stats_total['Storage']=
            $stats_per_postes_today['Storage']['all']+
            $stats_per_postes_tomorrow['Storage']['all']+
            $stats_per_postes_overdue['Storage']['all']+
            $stats_per_postes_later['Storage']['all'];
        $stats_total['Tailoring']=
            $stats_per_postes_today['Tailoring']['all']+
            $stats_per_postes_tomorrow['Tailoring']['all']+
            $stats_per_postes_overdue['Tailoring']['all']+
            $stats_per_postes_later['Tailoring']['all'];
        $stats_total['shelving']=
            $stats_per_postes_today['shelving']['all']+
            $stats_per_postes_tomorrow['shelving']['all']+
            $stats_per_postes_overdue['shelving']['all']+
            $stats_per_postes_later['shelving']['all'];


        return response()->json([
            'grouped_postes'=>$grouped_postes,
            'main_stats'=>$main_stats,
            'stats_today'=>$stats_per_postes_today,
            'stats_tomorrow'=>$stats_per_postes_tomorrow,
            'stats_overdue'=>$stats_per_postes_overdue,
            'stats_later'=>$stats_per_postes_later,
            'stats_total'=>$stats_total,
            [$today,$tomorrow]
        ]);
    }   
    
    public function getPartnerDetails(Request $request){
        $day = $request->post('day');
        $poste_id = $request->post('poste');
        $type = $request->post('type');
        $typepost = $request->post('typepost');

        // $tableprops = json_decode($request->post('tableprops'));

        // $orderBy = $tableprops->column;
        // $orderSort = $tableprops->dir;
        // $limit = $tableprops->length;

         $operator = "=";

         if($day == 'overdue'){
             $day = 'today';
             $operator = "<";
         }elseif ( $day=='later' ){
             $day = 'tomorrow';
             $operator = ">";
         }

        $date_stats = date('Y-m-d', strtotime($day));

        $arr = [];

        $all_services = Item::getTypeService();
        foreach($all_services as $k=>$v){
            $arr[] = $k;
        }

        if($type=='exp') {
            $arr = [1, 3, 4, 5, 6];
        }else if($type=='std'){
            $arr = [0,2];
        }

        $postes = Poste::all();
        $postes_id = [];

        $partners = [];

        $all_partners = Db::table('partner')->get();
        if(!empty($all_partners)){
            foreach($all_partners as $k=>$v){
                $partners[$v->id] = $v->Name;
            }
        }


        $invoices = Db::table('infoitems')
            ->select( 
                'infoInvoice.CustomerID', 'infoInvoice.NumInvoice AS sub_order', 'infoitems.ItemTrackingKey as barcode', 
                'infoitems.typeitem as iteminfo', DB::raw('DATE_FORMAT(infoitems.PromisedDate,"%m/%d") as prod'), 'infoitems.id AS item_id',
                'infoitems.nextpost', 'infoitems.store', 'infoCustomer.Name as customer_name', 'postes.nom as location',
                'infoitems.idPartner', 'TypePost.bg_color as location_color', 'TypePost.process','TypePost.circle_color',
                DB::raw('DATE_FORMAT(infoitems.PromisedDate,"%m/%d") as deliv'),                    
                // DB::raw('IF(infoitems.PromisedDate > CURRENT_DATE(), IF(pickup.date > deliveryask.date, DATE_FORMAT(deliveryask.date, "%d/%m"), DATE_FORMAT(pickup.date, "%d/%m")), DATE_FORMAT(infoitems.PromisedDate, "%d/%m")) as deliv')
                )
            ->where('infoitems.PromisedDate', $operator, $date_stats);
    

        if($typepost == 'Partner'){   
            $invoices = $invoices->where('infoitems.PartnerINOUT','=',1);
        }else{
            $invoices = $invoices->where('infoitems.PartnerINOUT','!=',1);
        }
        if($typepost == 'shelving'){
            $invoices = $invoices->whereIn('infoitems.nextpost', [30,31,32]);
        }

        if($typepost == 'Tailoring'){
            $invoices=$invoices->whereIn('infoitems.nextpost', [1]);
        }

        if($typepost == 'Spotting'){
            $invoices = $invoices->whereIn('infoitems.nextpost', [2,3,4,5,27]);
        }

        if($typepost == 'Quality Control 1'){
            $invoices = $invoices->whereIn('infoitems.nextpost', [6,7]);
        }

        if($typepost=='Pressing'){
            $invoices=$invoices->whereIn('infoitems.nextpost', [8,9,10,11,35]);
        }
        if($typepost=='Quality Control 2'){
            $invoices=$invoices->whereIn('infoitems.nextpost', [12,13,25]);
        }
        if($typepost == 'Conveyor'){
            $invoices = $invoices->where('infoitems.nextpost', 24); //Assembly conveyor
        }
        if($typepost == 'Loading Station'){
            $invoices = $invoices->where('infoitems.nextpost', 23);
        }

        if($typepost == 'Storage'){
            $invoices=$invoices->whereIn('infoitems.nextpost', [36]); //Storage conveyor
        }
        if($typepost == 'CustomerCare'){
            $invoices = $invoices->whereIn('infoitems.nextpost', [33,40]);
        }
        if($type =='delivery'){
            $invoices = $invoices->where('infoitems.store','=','DELIVERY');
        }    
        if($type=='store'){
            $invoices=$invoices->where('infoitems.store','=','STORES');
        }
        $invoices   =   $invoices->where('infoitems.Actif',1)
                                ->whereIn('infoitems.express', $arr)
                                ->join('infoInvoice', 'infoitems.SubOrderID', '=', 'infoInvoice.SubOrderID')
                                // ->join('infoOrder', 'infoOrder.OrderID', '=', 'infoInvoice.OrderID')
                                // ->join('pickup', 'infoOrder.PickupID', '=', 'pickup.PickupID')
                                // ->join('deliveryask', 'infoOrder.DeliveryaskID', '=', 'deliveryask.DeliveryaskID')                                
                                ->join('infoCustomer', 'infoInvoice.CustomerID', '=', 'infoCustomer.CustomerID')
                                ->join('postes', 'infoitems.nextpost', '=', 'postes.id')
                                ->join('TypePost', 'TypePost.id', '=', 'postes.TypePost')
                                ->where('infoitems.SubOrderID','!=','')
                                // ->whereIN('deliveryask.status', ['NEW','API','PMS','DONE', 'PMS-DONE', 'API-DONE','REC','REC-DONE','REC-NOK','PMS-NOK','API-NOK'])
                                // ->whereIN('pickup.status', ['NEW', 'API', 'PMS', 'DONE', 'PMS-DONE', 'API-DONE', 'REC', 'REC-DONE', 'REC-NOK', 'PMS-NOK', 'API-NOK','OP'])
                                // ->whereNotIn('infoOrder.Status',['VOID', 'DELETE'])                                
                                ->orderBy('item_id');

        $total_count=  $invoices->count();
        $invoices   = $invoices->skip($request->skip ? $request->skip : 0)
                            ->take(10)
                            ->get();
        return response()->json([
            'invoices'  =>$invoices,
            'count_data'=>$total_count,
            'data'=> [
                'invoices'      =>  $invoices,
                'total_count'   =>  $total_count
            ],
            "payload"   =>  array("search"=>"","dir"=>"asc","column"=>"id","length"=>"100000","draw"=>"0"),
            "poste_id"  =>  $postes_id,
        ]);
    }

    /**
     * return all invoices
     */
    public function getAllInvoices(Request $request){
        $invoices = Db::table('infoitems')
                        ->select( 
                            'infoInvoice.CustomerID', 'infoInvoice.NumInvoice AS sub_order', 'infoitems.ItemTrackingKey as barcode', 
                            'infoitems.typeitem as iteminfo', DB::raw('DATE_FORMAT(infoitems.PromisedDate,"%m/%d") as prod'), 'infoitems.id AS item_id',
                            'infoitems.nextpost', 'infoitems.store', 'infoCustomer.Name as customer_name', 'postes.nom as location',
                            'infoitems.idPartner', 'TypePost.bg_color as location_color',  'TypePost.process', 'TypePost.circle_color',
                            // DB::raw('DATE_FORMAT(infoitems.PromisedDate,"%m/%d") as deliv'),
                            DB::raw('IF(infoitems.PromisedDate > CURRENT_DATE(), IF(pickup.date > deliveryask.date, DATE_FORMAT(deliveryask.date, "%d/%m"), DATE_FORMAT(pickup.date, "%d/%m")), DATE_FORMAT(infoitems.PromisedDate, "%d/%m")) as deliv')
                            /* DB::raw('DATE_FORMAT(pickup.date,"%m/%d") as pickup_date'), */
                            /* DB::raw('DATE_FORMAT(deliveryask.date, "%m/%d") as deliveryask_date') */
                        );
        $arr = [0, 1, 2, 3, 4, 5, 6];
        $invoices   =  $invoices->where('infoitems.Actif',1)
                                ->whereIn('infoitems.express', $arr)
                                ->join('infoInvoice', 'infoitems.SubOrderID', '=', 'infoInvoice.SubOrderID')
                                ->join('infoOrder', 'infoOrder.OrderID', '=', 'infoInvoice.OrderID')
                                ->join('pickup', 'infoOrder.PickupID', '=', 'pickup.PickupID')
                                ->join('deliveryask', 'infoOrder.DeliveryaskID', '=', 'deliveryask.DeliveryaskID')
                                ->join('infoCustomer', 'infoInvoice.CustomerID', '=', 'infoCustomer.CustomerID')
                                ->join('postes', 'infoitems.nextpost', '=', 'postes.id')
                                ->join('TypePost', 'TypePost.id', '=', 'postes.TypePost')
                                ->where('infoitems.SubOrderID','!=','')
                                ->whereIN('deliveryask.status', ['NEW','API','PMS','DONE', 'PMS-DONE', 'API-DONE','REC','REC-DONE','REC-NOK','PMS-NOK','API-NOK'])
                                ->whereIN('pickup.status', ['NEW', 'API', 'PMS', 'DONE', 'PMS-DONE', 'API-DONE', 'REC', 'REC-DONE', 'REC-NOK', 'PMS-NOK', 'API-NOK','OP'])
                                ->whereNotIn('infoOrder.Status',['VOID', 'DELETE'])
                                ->orderBy('item_id');
        if($request->status != ''){
            $invoices   = $invoices->where('infoitems.Status', $request->status);
        }
        if($request->dest != ''){
            $invoices   = $invoices->whereIn('infoitems.store', explode(',', $request->dest));
        }
        if($request->location != ''){
            $invoices   = $invoices->whereIn('postes.nom', explode(',', $request->location));
        }
        if($request->prod_date_from != '' && $request->prod_date_to != ''){
            $invoices   = $invoices->whereBetween('infoitems.PromisedDate', 
                                    [ 
                                        Carbon::parse($request->prod_date_from)->toDateTimeString(),
                                        Carbon::parse($request->prod_date_to)->toDateTimeString(),
                                    ]
                                );
        }else if( $request->prod_date_from != '' && $request->prod_date_to == '' ){
            $invoices   = $invoices->whereDate('infoitems.PromisedDate', '>=', Carbon::parse($request->prod_date_from)->toDateTimeString());
        } else if( $request->prod_date_from == '' && $request->prod_date_to != '' ){
            $invoices   = $invoices->whereDate('infoitems.PromisedDate', '<=', Carbon::parse($request->prod_date_to)->toDateTimeString());
        }

        if($request->deliv_date_from != '' && $request->deliv_date_to != ''){
            $invoices   = $invoices->whereBetween('infoitems.PromisedDate', 
                                    [ 
                                        Carbon::parse($request->deliv_date_from)->toDateTimeString(),
                                        Carbon::parse($request->deliv_date_to)->toDateTimeString(),
                                    ]
                                );
        }else if( $request->deliv_date_from != '' && $request->deliv_date_to == '' ){
            $invoices   = $invoices->whereDate('infoitems.PromisedDate', '>=', Carbon::parse($request->prod_date_from)->toDateTimeString());
        } else if( $request->deliv_date_from == '' && $request->deliv_date_to != '' ){
            $invoices   = $invoices->whereDate('infoitems.PromisedDate', '<=', Carbon::parse($request->prod_date_to)->toDateTimeString());
        }
        $total_invoice_count = $invoices->count();
        $invoices   =  $invoices->skip($request->skip ? $request->skip : 0)
                                ->take(20)
                                ->get();

        // $data = [];
        // if(!empty($invoices)) {
        //     foreach ($invoices as $k =>$v) {
        //         $data[$k]['item_id'] = $v->item_id;
        //         $data[$k]['customer_name'] = $v->customer_name;
        //         $data[$k]['store'] = $v->store;
        //         $data[$k]['sub_order'] = $v->sub_order;
        //         $data[$k]['iteminfo'] = $v->typeitem;
        //         $data[$k]['location'] = $v->location;
        //         $data[$k]['location_color'] = $v->location_color;
        //         // $data[$k]['prod'] = Carbon::parse($v->prod)->format('d/m');
        //         $data[$k]['barcode'] = $v->barcode;

        //         // $pick_up_status_to_include = ['NEW', 'API', 'PMS', 'DONE', 'PMS-DONE', 'API-DONE', 'REC', 'REC-DONE', 'REC-NOK', 'PMS-NOK', 'API-NOK','OP'];
        //         // $pick_up_date = DB::table('pickup')
        //         //                     ->where('CustomerID', $v->CustomerID)
        //         //                     ->whereIn('status', $pick_up_status_to_include)->value('date');
                
        //         // $deliveryask_status_to_include = ['NEW','API','PMS','DONE', 'PMS-DONE', 'API-DONE','REC','REC-DONE','REC-NOK','PMS-NOK','API-NOK'];
        //         // $deliveryask_date = DB::table('deliveryask')
        //         //                     ->where('CustomerID', $v->CustomerID)
        //         //                     ->whereIn('status', $deliveryask_status_to_include)->value('date');
        //         // if($deliveryask_date && $pick_up_date){
        //         //     if(Carbon::parse($deliveryask_date)->gt(Carbon::parse($pick_up_date)))
        //         //         $data[$k]['deliv'] = Carbon::parse($deliveryask_date)->format('d/m');
        //         //     else
        //         //         $data[$k]['deliv'] = Carbon::parse($pick_up_date)->format('d/m');
        //         // }else if( $deliveryask_date && $pick_up_date == ''){
        //         //     $data[$k]['deliv'] = Carbon::parse($deliveryask_date)->format('d/m');
        //         // }else{
        //         //     $data[$k]['deliv'] = Carbon::parse($pick_up_date)->format('d/m');
        //         // }
        //     }
        // }

        return response()->json([
            'invoices'      =>  $invoices,
            'total_count'   =>  $total_invoice_count
        ]);
    }
}
