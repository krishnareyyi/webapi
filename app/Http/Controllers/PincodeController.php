<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;  
use Illuminate\Support\Facades\Hash;
class PincodeController extends Controller
{
    function pincode(Request $request)
    {

        // return $pincode;
    $data = DB::select("SELECT DISTINCT p.state_id, s.StateName, p.district_id, d.district_name, (SELECT CONCAT('[',(SELECT GROUP_CONCAT(json_object('pincodeid', po.pincode_id, 'postoffice', po.post_office, 'pincode', po.pincode)) from pincode po where po.pincode='".$request->pincode."'), ']') as postoffice) AS txt from pincode p INNER JOIN  states s ON p.state_id=s.state_id INNER JOIN districts d ON p.district_id=d.districts_id where p.pincode='".$request->pincode."'");
     return  $data;
     
    }
}
