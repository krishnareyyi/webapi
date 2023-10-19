<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    public function payout(Request $request)
    {
        if ($request->method == "create") {
            do {
                $payout_id = Str::random(10);
            } while (DB::table('payout_master')->where("id", "=", $payout_id)->first());
            $values = array(
                'id' => $payout_id,
                'user_id' => $request->user_id,
                'business_id' => $request->business_id,
                'user_type' => $request->user_type,
                'per' => $request->payout_calculate,
                'discount_type' => $request->discout_type,
                'payout_value' => $request->payoutvalue,
                'status' => 'active',
                'update_date' => date("Y-m-d h:i:s"),
                'create_date' => date("Y-m-d h:i:s"),
            );

            $data = DB::table('payout_master')->insert($values);
            if ($data) {
                return ["message" => "success"];
            } else {
                return ["message" => "faild"];
            }
        }
        if ($request->method == "get") {
            $data = DB::select("SELECT * from payout_master where business_id='" . $request->business_id . "'");
            return $data;
        }
    }
    public function securitypassword(Request $request)
    {
        if ($request->method == "update") {

            if ($request->avg == "false") {

                if ($request->Secqurity1 != '') {
                    $Secqurity1['code'] = $request->Secqurity1;
                    DB::table('hidden_accounts')->where("user_id", "=", $request->user_id)->where("account_type", "=", "avg")->update($Secqurity1);
                }
                if ($request->Secqurity2 != '') {
                    $Secqurity2['code'] = $request->Secqurity2;
                    DB::table('hidden_accounts')->where("user_id", "=", $request->user_id)->where("account_type", "=", "org")->update($Secqurity2);
                }
            }
            $securitypassword['account_type'] = "org";
            $securitypassword['account_type'] = $request->Secqurity2;
        }
        if ($request->method == "get") {
            $data = DB::table('hidden_accounts')->where("user_id", "=", $request->user_id)->get();
            return $data;
        }
    }
    public function smtp(Request $request)
    {
        if ($request->method == "create") {
            do {
                $smtp_id = Str::random(10);
            } while (DB::table('smtpservers')->where("id", "=", $smtp_id)->first());

            $values['business_id'] = $request->business_id;
            $values['user_id'] = $request->user_id;
            $values['host'] = $request->host;
            $values['port'] = $request->port;
            $values['username'] = $request->username;
            $values['password'] = $request->password;
            $values['encryption'] = $request->Encryption;
            $values['from'] = $request->from;
            $checksmtp = DB::table('smtpservers')->where('business_id', '=', $request->business_id)->get();
            if (count($checksmtp) == 0) {
                $values['id'] = $smtp_id;
                $data = DB::table('smtpservers')->insert($values);
            } else {
                $data = DB::table('smtpservers')->where('business_id', '=', $request->business_id)->update($values);
            }

            if ($data) {
                return ["message" => "success"];
            } else {
                return ["message" => "faild"];
            }
        }
        if ($request->method == "get") {
            $data = DB::select("SELECT * from smtpservers where business_id='" . $request->business_id . "'");
            return $data;
        }
    }
    public function pdftest(Request $request)
    {

        $pdf = Pdf::loadView('PDF.test');
        $p = "a4";
        $pdf->setOptions(['defaultPaperSize' => $p, 'dpi' => 150, 'adminPassword' => '123']);
        // $customPaper = array(0,0,600,360);
        // $pdf->set_paper($customPaper);
        return $pdf->download('invoice.pdf');
    }
   
}
