<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReportsController extends Controller
{
    public function paymentreport(Request $request)
    {

        $data = DB::select('SELECT ph.date as payment_date, ph.payment_type, ph.unit_id, ph.id as recept_no, ph.lead_id, p.project_name, ph.amount  from payment_history ph
        INNER JOIN leads l ON l.id= ph.lead_id
        INNER JOIN projects p ON p.id=l.project_id
         where ph.business_id="'.$request->business_id.'" ORDER BY ph.create_date DESC');
        return $data;

    }

    public function customerreport(Request $request)
    {
            $data = DB::select('SELECT pu.id as unit_id, pu.lead_id, l.name, l.mobile_no, p.project_name, m.title as payment_mode 
            from   prpty_units pu
                INNER JOIN leads l ON l.id= pu.lead_id
                INNER JOIN master m ON m.id=pu.payment_mode
                INNER JOIN projects p ON p.id= l.project_id
            where  
              l.business_id="'.$request->business_id.'" AND  pu.payment_mode IS NOT NULL');
            return   $data;
    }
}
