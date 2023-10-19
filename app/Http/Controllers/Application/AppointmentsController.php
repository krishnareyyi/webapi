<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppointmentsController extends Controller
{
    public function apointments(Request $request)
    {

        if ($request->method == 'apointments') {
            if ($request->usertype == 's_admin' && $request->business_type == 'Developer' ) {
                $data = DB::select('SELECT l.id, l.name, l.mobile_no, l.email, l.create_date, l.source,  l.project_id, u.name as agentname,  p.project_name, a.appointments_date, s.from as sloatfrom, s.to  as sloatto, a.id as  appointments_id, al.status as appointments_status from leads l
                    INNER JOIN projects p ON p.id=l.project_id
                   
                    LEFT JOIN appointments a ON a.lead_id=l.id
                    INNER JOIN asign_leads al ON al.apointment_id=a.id
                    LEFT JOIN slots s ON s.id=a.sloat_id
                    LEFT JOIN users u ON u.id=a.asign_to
                where
                    l.lead_status IN("' . implode(',', $request->status) . '")
                    order by l.create_date DESC

                ');
                return $data;
            }
            if ($request->usertype == 'Sales' && $request->business_type == 'Developer' ) {
                $data = DB::select('SELECT l.id, l.name, l.mobile_no, l.email, l.create_date, l.source,  l.project_id, u.name as agentname,  p.project_name, a.appointments_date, s.from as sloatfrom, s.to  as sloatto, a.id as  appointments_id, al.status as appointments_status from leads l
                    INNER JOIN projects p ON p.id=l.project_id
                    LEFT JOIN appointments a ON a.lead_id=l.id
                    INNER JOIN asign_leads al ON al.apointment_id=a.id
                    LEFT JOIN slots s ON s.id=a.sloat_id
                    LEFT JOIN users u ON u.id=a.asign_to
                where
                   ( l.lead_status IN("' . implode(',', $request->status) . '") and l.user_id="'.$request->user_id.'") OR 
                     ( l.lead_status IN("' . implode(',', $request->status) . '") and l.id IN (SELECT lead_id from asign_leads where asign_to="'.$request->user_id.'") )
                    order by l.create_date DESC

                ');
                return $data;
            }
            if ($request->usertype == 'Telicaller' && $request->business_type == 'Developer' ) {
                $data = DB::select('SELECT l.id, l.name, l.mobile_no, l.email, l.create_date, l.source,  l.project_id, u.name as agentname,  p.project_name, a.appointments_date, s.from as sloatfrom, s.to  as sloatto, a.id as  appointments_id, al.status as appointments_status from leads l
                    INNER JOIN projects p ON p.id=l.project_id
                    LEFT JOIN appointments a ON a.lead_id=l.id
                    INNER JOIN asign_leads al ON al.apointment_id=a.id
                    LEFT JOIN slots s ON s.id=a.sloat_id
                    LEFT JOIN users u ON u.id=a.asign_to
                where
                   ( l.lead_status IN("' . implode(',', $request->status) . '") and l.user_id="'.$request->user_id.'") OR 
                     ( l.lead_status IN("' . implode(',', $request->status) . '") and l.id IN (SELECT lead_id from asign_leads where asign_by="'.$request->user_id.'") )
                    order by l.create_date DESC

                ');
                return $data;
            }
        }
        if ($request->method == 'apointments_status_update') {
            $appointment['status'] = $request->status;
            $data = DB::table('asign_leads')->where('apointment_id', '=', $request->apointment_id)->update($appointment);
            if ($data) {
                return ["message" => "success"];
            } else {
                return ["message" => "faild"];
            }
        }
    }
}
