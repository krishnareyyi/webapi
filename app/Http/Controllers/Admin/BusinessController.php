<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BusinessController extends Controller
{
    public function business(Request $request)
    {
        if ($request->method == "get") {
            $data = DB::table('business as b')->selectRaw('b.id as business_id, b.logo, b.type, b.business_name, b.create_date, b.status, s.StateName, d.district_name, b.post_office, b.pincode')
            ->join('states as s', 's.state_id', '=', 'b.state')
            ->join('districts as d', 'd.districts_id', '=', 'b.district')
            ->get();
            return $data;
        }
        if ($request->method == "details") {
            $data = DB::table('business as b')->selectRaw('b.id as business_id, b.user_id, b.logo, b.type, b.business_name, b.create_date, b.status, b.state, b.district, s.StateName, d.district_name, b.post_office, b.pincode, b.exp_date, b.contact_person, b.contact_no, b.address, IF(b.avg=1, "true", "false") as avg')
            ->join('states as s', 's.state_id', '=', 'b.state')
            ->join('districts as d', 'd.districts_id', '=', 'b.district')
            ->where('b.id', '=', $request->bisiness_id)
            ->get();
            return $data;
        }
        if ($request->method == "update") {
            $businessinfo['business_name']=$request->business_name;
            $businessinfo['contact_person']=$request->contact_person;
            $businessinfo['contact_no']=$request->contact_no;
            $businessinfo['create_date']=$request->joining_date;
            $businessinfo['avg']=$request->allow_avg_price;
            $businessinfo['pincode']=$request->pincode;
            $businessinfo['address']=$request->address;
            $businessinfo['state']=$request->state_id; 
            $businessinfo['district']=$request->dist_id;
            $businessinfo['post_office']=$request->area;
            $businessinfo['address']=$request->address;
            $businessinfo['status']=$request->status; 

            $data = DB::table('business')->where('id', '=', $request->bisiness_id)->update($businessinfo);
           
            if($request->allow_avg_price==true){
                do {
                    $hidenid = Str::random(10);
                } while (DB::table('hidden_accounts')->where("id", "=", $hidenid)->first());
    
                $secpin['id']=$hidenid;
                $secpin['user_id']=$request->user_id;
                $secpin['business_id']=$request->business_id;
                $secpin['account_type']="org";
                $secpin['code']="org";
    
                DB::table('hidden_accounts')->insert($secpin);
            }else{
                DB::table('hidden_accounts')->where('account_type', '=', 'org')->where('user_id', '=', $request->user_id)->delete();
            }

            if ($data) {
                return ["message" => "success"];
            } else {
                return ["message" => "faild"];
            }
        }
    }
}
