<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mail;
use App\Mail\EmailConfirmation;
use App\Providers\AppServiceProvider;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {

        if ($request->business_type == "Developer") {
            // new  AppServiceProvider($request);

            
            //    $emaildata = [
            //     'name' => "krishna",
            //     'veriication_link' => "sample",
            // ];
            // Mail::to('krishnareyyi@gmail.com')->send(new EmailConfirmation($emaildata));

            if ($request->user_type == "s_admin") {
                DB::statement(DB::raw("SET @access='" . $request->access . "'"));
                $data['accounts'] = DB::select("SELECT
                ( SELECT SUM(payout_value) from payout where business_id='" . $request->business_id . "') as totalpayout,
                ( SELECT SUM(amount) from payment_history  where 
                    case
                        WHEN (@access='avg') THEN
                            business_id='" . $request->business_id . "'  AND  flag ='" . $request->access . "' 
                        ELSE
                            business_id='" . $request->business_id . "'   AND   flag IN ('avg', 'org')
                    END
                ) as saleamount,
                ( SELECT SUM(amount) from expensive where business_id='" . $request->business_id . "') as exensive
                 from business b where   b.id='" . $request->business_id . "'");


                 $data['leads'] = DB::select("SELECT
                ( SELECT count(id) from leads where business_id='" . $request->business_id . "') as totalleads,
                ( SELECT count(id) from leads  where business_id='" . $request->business_id . "' and status IN ('created', 'site visit', 'sales person')) as qualifiedleads,
                ( SELECT count(amount) from expensive where business_id='" . $request->business_id . "') as exensive
                 from business b where   b.id='" . $request->business_id . "'");

            }
            if ($request->user_type == "Sales") {
                $data['leads'] = DB::select("SELECT
                ( SELECT count(id) from leads where business_id=b.id AND user_id='".$request->user_id."' and lead_status='created') as newleads,
                ( SELECT count(id) from leads where business_id=b.id AND lead_status IN ('site visit', 'sales person') AND id IN (SELECT lead_id from appointments where asign_to='".$request->user_id."')) as appointments,
                ( SELECT count(id) from leads where business_id=b.id AND lead_status IN ('Not answering', 'call me later')  AND user_id='".$request->user_id."') as remainders
                 from business b where   b.id='" . $request->business_id . "'");
                 $data['appointment'] = DB::select("SELECT l.id as lead_id, l.name, l.mobile_no, a.appointments_date from appointments a INNER JOIN leads l ON l.id=a.lead_id where a.asign_to='".$request->user_id."' AND l.business_id='".$request->business_id."' AND l.lead_status IN ('site visit', 'sales person') limit 5");
                 $data['remainders'] = DB::select("SELECT l.id as lead_id, l.name, l.mobile_no from leads l  where l.business_id='".$request->business_id."' AND l.lead_status IN ('Not answering', 'call me later') limit 5");
            }
            if ($request->user_type == "Telicaller") {
                $data['leads'] = DB::select("SELECT
                ( SELECT count(id) from leads where business_id=b.id AND user_id='".$request->user_id."' and lead_status='created') as newleads,
                ( SELECT count(id) from leads where business_id=b.id AND lead_status IN ('site visit', 'sales person') AND id IN (SELECT lead_id from appointments where asign_to='".$request->user_id."')) as appointments,
                ( SELECT count(id) from leads where business_id=b.id AND lead_status IN ('Not answering', 'call me later')  AND user_id='".$request->user_id."') as remainders
                 from business b where   b.id='" . $request->business_id . "'");

                 $data['appointment'] = DB::select("SELECT l.id as lead_id, l.name, l.mobile_no, a.appointments_date from appointments a INNER JOIN leads l ON l.id=a.lead_id where (a.asign_to='".$request->user_id."' OR a.user_id='".$request->user_id."') AND l.business_id='".$request->business_id."' AND l.lead_status IN ('site visit', 'sales person') limit 5");

                 $data['remainders'] = DB::select("SELECT l.id as lead_id, l.name, l.mobile_no from leads l  where l.business_id='".$request->business_id."' AND l.lead_status IN ('Not answering', 'call me later') limit 5");
            }
            if ($request->user_type == "Accounts") {
                $data['leads'] = DB::select("SELECT
                ( SELECT count(id) from leads where business_id=b.id AND  lead_status='Booking') as Booking,
                ( SELECT count(id) from leads where business_id=b.id AND  lead_status='Agenement') as Agenement,
                ( SELECT count(id) from leads where business_id=b.id AND  lead_status='Registration') as Registration,
                ( SELECT sum(amount) from expensive where business_id=b.id) as expensive,
                ( SELECT sum(amount) from payment_history where business_id=b.id) as sales
                 from business b where   b.id='" . $request->business_id . "'");

                 $data['sales']  =  DB::table('master as m')->selectRaw('m.title as month,  (SELECT  SUM(p.amount) from payment_history p  where MONTH(p.date)=m.cat and business_id="'.$request->business_id.'") as count') 
                  ->where('m.type', '=', 'month')
                  ->get();

                  $data['expensive']  =  DB::table('master as m')->selectRaw('m.title as month,  (SELECT  SUM(e.amount) from expensive e  where MONTH(e.date)=m.cat and e.business_id="'.$request->business_id.'") as count') 
                  ->where('m.type', '=', 'month')
                  ->get();
            }
            if ($request->user_type == "Agent") {
                $data['leads'] = DB::select("SELECT
                ( SELECT SUM(payout_value) from payout where user_id=u.id AND  status='paid') as totalpayout,
                ( SELECT SUM(ph.amount) from payment_history ph INNER JOIN leads l  ON l.id= ph.lead_id where l.user_id=u.id) as totalsales,
                ( SELECT count(id) from leads where user_id=u.id) as totalleads
                        from users u where id='".$request->user_id."' 
                ");
                $data['payout'] = DB::table('payout as p')
                ->selectRaw('p.*, prj.project_name')
                ->join('leads as l', 'l.id', '=', 'p.lead_id')
                ->join('projects as prj', 'prj.id', '=', 'l.project_id')
                ->where('p.user_id', '=', $request->user_id)->orderby('p.created_date', 'DESC')->get();
 
            }
            return @$data;
        }

    }
    public function salesgrap(Request $request)
    {
        if ($request->method == "salesgraph") {

            // $data = DB::table('master as m')->selectRaw('m.title as month,  (SELECT  SUM(p.amount) from payment_history p  where MONTH(p.date)=m.cat and business_id="'.$request->business_id.'") as count') 
            // ->where('m.type', '=', 'month')
            // ->get();
            DB::statement(DB::raw("SET @access='" . $request->access . "'"));
            $data = DB::select('SELECT m.title as month,  
                    (SELECT  SUM(p.amount) from payment_history p  
                where 

                case
                        WHEN (@access="avg") THEN
                        (MONTH(p.date)=m.cat AND  business_id="' . $request->business_id . '" ) AND  flag ="' . $request->access . '" 
                        ELSE
                        (MONTH(p.date)=m.cat AND  business_id="' . $request->business_id . '" ) AND   flag IN ("avg", "org")
                    END
              ) as count 
                from master m  WHERE
             m.type="month"');
            return $data;

        }
    }
}
