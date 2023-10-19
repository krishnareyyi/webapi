<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Mail;
use App\Mail\EmailConfig;
use App\Providers\AppServiceProvider;

class LeadsController extends Controller
{
    public function leads(Request $request)
    {
        if ($request->method == "create") {
            do {
                // $code = Str::random(40);
                $lead_id = '';
                $keys = array_merge(range(0, 9), range('A', 'Z'));
                for ($i = 0; $i < 10; $i++) {
                    $lead_id .= $keys[array_rand($keys)];
                }

            } while (DB::table('leads')->where("id", "=", $lead_id)->first());
            // new  AppServiceProvider($request);
        //     $emaildata = [
        //      'name' => $request->name,
        //      'business_id' =>$request->business_id,
        //  ];
        //  Mail::to($request->email)->send(new EmailConfig($emaildata));
            $values = array(
                'id' => $lead_id,
                'user_id' => $request->user_id,
                'business_id' => $request->business_id,
                'name' => $request->name,
                'mobile_no' => $request->mobile,
                'email' => $request->email,
                'project_id' => $request->project,
                'source' => $request->sourceby,
                'lead_status' => $request->status,
                'status' => 'active',
                'create_date' => date("Y-m-d h:i:s"),
            );

            $data = DB::table('leads')->insert($values);

            do {
                $lead_history_id = Str::random(10);
            } while (DB::table('lead_history')->where("id", "=", $lead_history_id)->first());
            $values = array(
                'id' => $lead_history_id,
                'lead_id' => $lead_id,
                'user_id' => $request->user_id,
                'status' => $request->status,
            );
            $data = DB::table('lead_history')->insert($values);

            if ($data) {
                return ["message" => "success"];
            } else {
                return ["message" => "faild"];
            }
        }
        if ($request->method == "update") {
              
             $values['name']= $request->name;
             $values['mobile_no']= $request->mobile_no;
             $values['appointment_date']= $request->Appointment_date;
             $values['pincode']= $request->pincode;
             $values['State']= $request->state_id;
             $values['City']= $request->district_id;
             $values['Area']= $request->area;
             $values['full_address']= $request->fulladdress;
             $values['land_mark']= $request->landmark;
             $values['lead_status']=$request->status;
             $values['status']= 'active';
           

            $data = DB::table('leads')->where("id", "=", $request->lead_id)->where("business_id", "=", $request->business_id)->update($values);

            if($request->status=='Not answering' || $request->status=='call me later'){
                do {
                    $remainder_id = Str::random(10);
                } while (DB::table('remanders')->where("id", "=", $remainder_id)->first());
                $values = array(
                    'id' => $remainder_id,
                    'user_id' => $request->user_id,
                    'lead_id' => $request->lead_id,
                    'remainder_date' => $request->Remainder_date,
                    'lead_status' => $request->status,
                    'status' =>$request->status,
                );
                $data = DB::table('remanders')->insert($values);
            }

            do {
                $lead_history_id = Str::random(10);
            } while (DB::table('lead_history')->where("id", "=", $lead_history_id)->first());
            $values = array(
                'id' => $lead_history_id,
                'lead_id' => $request->lead_id,
                'user_id' => $request->user_id,
                'status' => $request->status,
            );
            $data = DB::table('lead_history')->insert($values);


            
            if ($request->slot_id != '') {
                do {
                    $appointments_id = Str::random(10);
                } while (DB::table('appointments')->where("id", "=", $appointments_id)->first());
                $appointments = array(
                    'id' => $appointments_id,
                    'user_id' => $request->user_id,
                    'asign_to' => $request->agent_id,
                    'lead_id' => $request->lead_id,
                    'appointments_date' => $request->Appointment_date,
                    'sloat_id' => $request->slot_id,
                );
                $data = DB::table('appointments')->insert($appointments);
            }
            if ($request->slot_id != '') {
                do {
                    $asign_leads_id = Str::random(10);
                } while (DB::table('asign_leads')->where("id", "=", $asign_leads_id)->first());
                $appointments = array(
                    'id' => $asign_leads_id,
                    'asign_by' => $request->user_id,
                    'asign_to' => $request->agent_id,
                    'lead_id' => $request->lead_id,
                    'apointment_id' =>$appointments_id,
                    'status' => 'created',
                );
                $data = DB::table('asign_leads')->insert($appointments);
            }

           

            if ($data) {
                return ["message" => "success"];
            } else {
                return ["message" => "faild"];
            }
        }
        if ($request->method == "list") {
            DB::statement(DB::raw("SET @usertype='".$request->usertype."'"));
            DB::statement(DB::raw("SET @business_type='".$request->business_type."'"));
            $data = DB::select('SELECT l.id, l.name, l.mobile_no, l.create_date, l.source, l.status, l.project_id,  p.project_name from leads l 
                INNER JOIN projects p ON p.id = l.project_id
            where
           
                case 
                    WHEN (@usertype="s_admin") THEN 
                     ( l.business_id="'.$request->business_id.'" AND l.user_id ="'.$request->user_id.'" AND  l.lead_status IN ("'. implode(',', $request->lead_status).'") )  OR (l.user_id IN (SELECT id from users where business_id= "'.$request->business_id.'" AND usertype IN ("Telicaller", "sales")) AND  l.lead_status IN ("'. implode(',', $request->lead_status).'") )

                    WHEN (@usertype="Telicaller" OR @usertype="sales" OR @usertype="Agent") THEN 
                    l.business_id="'.$request->business_id.'" AND   l.lead_status IN ("'. implode(',', $request->lead_status).'")  
                            AND 
                        l.user_id="'.$request->user_id.'"
                END
            order by l.create_date DESC
            ');
         
            return $data;

        }
        if ($request->method == "sale") {
            DB::statement(DB::raw("SET @usertype='".$request->usertype."'"));
            $data = DB::select('SELECT l.id, l.name, l.mobile_no, l.create_date, l.source, l.status, l.project_id,  p.project_name from leads l 
                INNER JOIN projects p ON p.id = l.project_id
            where
            project_id IN (SELECT id from projects where business_id="'.$request->business_id.'") AND 
                case 
                    WHEN (@usertype="s_admin") THEN 
                        l.lead_status IN ("'. implode(',', $request->lead_status).'")  

                    WHEN (@usertype="Telicaller" OR @usertype="sales"  OR @usertype="Agent") THEN 
                        l.lead_status IN ("'. implode(',', $request->lead_status).'")  
                            AND 
                        l.user_id="'.$request->user_id.'"
                END
            order by l.create_date DESC
            ');
         
            return $data;

        }
        if ($request->method == 'SiteVisit') {
            if ($request->usertype == 's_admin') {
                $data = DB::table('leads as l')->selectRaw('l.id, l.name, l.mobile_no, l.email, l.create_date, l.source, l.status, l.project_id,  p.project_name, a.appointments_date')->where("l.business_id", "=", $request->business_id)
                    ->leftjoin('appointments as a', 'a.lead_id', '=', 'l.id')
                    ->join('projects as p', 'p.id', '=', 'l.project_id')

                    ->whereIn("l.status", $request->status)->orderBy('l.create_date', 'desc')->get();
                return $data;
            }
        }
        if ($request->method == "details") {
            $data = DB::table('leads')->where("business_id", "=", $request->business_id)->where("id", "=", $request->id)->get();
            return $data;
        }
    }

    public function lead_unit_details(Request $request)
    {
//   return $request->unit_id;
        if ($request->method == "lead_unit_details") {
            if($request->unit_id==null){
                $data = DB::table('leads as l')
                ->selectRaw('l.id, l.name, l.email, l.mobile_no, l.user_id, l.project_id')
                
                ->where('l.id', '=', $request->lead_id)->get();
                return $data;
               
            }else{
                $data = DB::table('prpty_units as p')
                ->selectRaw('l.id, l.name, l.email, l.mobile_no, l.user_id, l.project_id, p.payment_mode')
                ->leftjoin('leads as l', 'l.id', '=', 'p.lead_id')
                ->where('p.id', '=', $request->unit_id)->get();
                return $data;
            }
           
        }
    }

    public function search(Request $request)
    {
       $data = DB::select('SELECT * from leads WHERE project_id IN(select id from projects where business_id="'.$request->business_id.'") AND  mobile_no LIKE "%'.$request->search.'%"');
       return  $data;
    }
    public function remainders(Request $request)
    {
        if ($request->method == "Lead Remainders") {
            $data = DB::select('SELECT l.id as lead_id, l.name,l.mobile_no, r.lead_status, r.status as remainderstatus,    r.id as remainder_id, r.remainder_date, r.user_id,  p.project_name, u.name as updatedby, r.create_date as updatedate from remanders r 
                    INNER JOIN leads l ON l.id=r.lead_id 
                    INNER JOIN projects p ON p.id=l.project_id 
                    INNER JOIN users u ON u.id=r.user_id 
                where 
                    l.business_id="'.$request->business_id.'" AND
                    (r.lead_status IN ("Not answering", "call me later") AND r.status IN ("create", "not available", "call me later", "Not answering")) 
                   
                    ORDER BY r.remainder_date ASC
                ');
            return $data;
        }
        if ($request->method == "lead_remainder_update") {
           
            if($request->status=='not available'){
                $remainder['status']=$request->status; 
               
            }
            if($request->status=='call me later'){
                $remainder['status']=$request->status; 
                $remainder['remainder_date']=$request->Remainder_date; 
            }
            if($request->status=='not interested'){
                $remainder['status']=$request->status; 
                $lead['lead_status']=$request->status; 
                $remainder['create_date']= date("Y-m-d h:i:s"); 
                $data = DB::table('leads')->where('id', '=', $request->lead_id)->update($lead);
            }
            if($request->status=='available'){
                $remainder['status']=$request->status; 
            }
            $remainder['create_date']= date("Y-m-d h:i:s"); 
            $data = DB::table('remanders')->where('lead_id', '=', $request->lead_id)->where('user_id', '=', $request->user_id)->where('lead_status', '=', $request->lead_status)->update($remainder);

            if ($data) {
                return ["message" => "success"];
            } else {
                return ["message" => "faild"];
            }

        }
    }

}
