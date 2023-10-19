<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class MasterController extends Controller
{
    public function week(Request $request)
    {
        if ($request->method == 'get') {

            $data = DB::select('SELECT m.*, IF( b.week_id IS  NULL, "false", "true") as week, b.week_id from master m LEFT JOIN business_week b ON b.week_id=m.cat AND b.business_id="'.$request->business_id.'" WHERE m.type="week"');
            // $data = DB::table('master')->selectRaw('master.*, IF( business_week.week_id IS  NULL, "false", "true") as week')
            // ->leftjoin('business_week', 'business_week.week_id', '=', 'master.cat')
            // ->where('master.type', '=', 'week')->get();
            return $data;
        }
        if ($request->method == 'update') {
            DB::table('business_week')->where('business_id', $request->business_id)->delete();
            for ($i = 0; $i < count($request->week); $i++) {
                do {
                    $bw_id = Str::random(10);
                } while (DB::table('business_week')->where("id", "=", $bw_id)->first());
                $weeks['id'] = $bw_id;
                $weeks['business_id'] = $request->business_id;
                $weeks['week_id'] = $request->week[$i];
                $weeks['type'] = $request->type;

                DB::table('business_week')->insert($weeks);
                
            }
        }

    }
    public function slot(Request $request)
    {
        if ($request->method == 'create') {
            do {
                $slots_id = Str::random(10);
            } while (DB::table('slots')->where("id", "=", $slots_id)->first());
            $values = array(
                'id' => $slots_id,
                'business_id' => $request->business_id,
                'from' => $request->fromdate,
                'to' => $request->todate,
                'status' =>  $request->status,
                'status' =>  $request->status
            );

            $data = DB::table('slots')->insert($values);
            if ($data) {
                return ["message" => "success"];
            } else {
                return ["message" => "faild"];
            }
        }
        if ($request->method == 'get') {
            $data = DB::table('slots')->where('business_id', '=', $request->business_id)->get();
            return $data;
        }
        if ($request->method == 'appointment') {
            $data = DB::select('SELECT s.id as slotid, s.from, s.to, s.status, s.business_id, IF( a.id IS NOT NULL, "blocked", null) as blockstatus   from slots  s
            LEFT JOIN business_week bw ON bw.business_id=s.business_id 
            LEFT JOIN  appointments a ON a.sloat_id=s.id AND DATE(a.appointments_date)="'.$request->appointment.'" AND a.asign_to="'.$request->agent_id.'"
             where s.business_id="'.$request->business_id.'" and bw.week_id="'.$request->day.'" ');
            return $data;
        }

    }
    public function userlinks(Request $request){
        
           
        if($request->user_type=="s_admin"){
            $menu=DB::select('SELECT DISTINCT l.* from master m LEFT JOIN  links l ON l.id=m.title and m.rel_id="s_admin"  where m.type="linkmapping" AND m.rel_id="s_admin" AND l.link_type="1" AND  m.cat="'.$request->business_type.'" AND l.status="active" order by l.orderby ASC');
        }else{
            $menu= DB::select('SELECT DISTINCT * from links where link_type=1 AND  status IN ("active") and id IN (SELECT link_id from link_permission where user_id="'.$request->user_id.'" ) order by orderby ASC');
        }
           
            $data = array();
            foreach ($menu as $f => $mainmenu) {
                $data[$f]['id'] = $mainmenu->id;
                $data[$f]['link_name'] = $mainmenu->link_name; 
                $data[$f]['path'] = $mainmenu->path;
                $data[$f]['image'] = $mainmenu->image;
                if($request->user_type=="s_admin"){
                    $data[$f]['submenu']=DB::select('SELECT DISTINCT l.* from master m INNER JOIN  links l ON l.id=m.title   where (m.type="linkmapping" AND m.rel_id="s_admin"  AND l.link_id="'. @$mainmenu->id.'" ) AND l.link_type="2" AND l.status="active" order by orderby ASC');
                }else{
                    $data[$f]['submenu'] =  DB::select('SELECT DISTINCT * from links where (link_id ="'. @$mainmenu->id.'" AND  id IN (SELECT link_id from link_permission where link_type=2 and user_id="'.$request->user_id.'" )) AND  status IN ("active")  order by orderby ASC');  
                }
                
            }
            return  $data;

        
    }
    public function ifscode($ifsccode)
    {
            $data = Http::get('https://ifsc.razorpay.com/'.$ifsccode);
            return $data;
    }
}
