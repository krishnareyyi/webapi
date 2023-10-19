<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use App\Mail\ResetPassword;
use App\Models\User;
use App\Providers\MailConfigProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Mail;

class AccountsController extends Controller
{
    public function login(Request $request)
    {

        $user = User::where('email', $request->email)->whereNotIn('usertype', ['org'])->first();
        // print_r($data);
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => 'These credentials do not match our records.',
                'status' => 'faild',
            ]);
        }
        $token = $user->createToken('my-app-token')->plainTextToken;
        $business_type = DB::table('business')->where('id', '=', $user->business_id)->first();

        $user['business_type'] = @$business_type->type;
        $user['business_name'] = @$business_type->business_name;
        $user['avg'] = (@$business_type->avg == 0) ? "false" : "true";
        $user['access'] = "avg";

        $response = [
            'status' => 'success',
            'user' => $user,
            'token' => $token,
        ];
        return response($response);
    }
    public function register(Request $request)
    {
        if ($request->method == "create") {

            $values = array(
                'name' => $request->name,
                'usertype' => "register",
                'email' => $request->email,
                'mobile' => $request->mobile_no,
                'status' => 0,
                'password' => Hash::make($request->password),
            );
            // $emaildata = [
            //     'name' => $request->name,
            //     'veriication_link' => $request->verifylink . "EmailConfirmation/" . $code,
            // ];
            // Mail::to($request->email)->send(new EmailConfirmation($emaildata));

            $data = DB::table('users')->insert($values);

            $userdata = DB::table('users')->where('email', '=', $request->email)->first();
            do {
                $hidenid = Str::random(10);
            } while (DB::table('hidden_accounts')->where("id", "=", $hidenid)->first());

            $secpin['id'] = $hidenid;
            $secpin['user_id'] = $userdata->id;
            $secpin['business_id'] = $request->business_id;
            $secpin['account_type'] = "avg";
            $secpin['code'] = "avg";

            DB::table('hidden_accounts')->insert($secpin);

            if ($data) {
                return ["message" => "success"];
            } else {
                return ["message" => "faild"];
            }
        }
    }
    public function checkcode(Request $request)
    {

        if ($request->method == "checkcode") {
            $data = DB::table('hidden_accounts')->where('user_id', '=', $request->user_id)->where('code', '=', $request->code)->get();
            if (count($data) == 0) {
                return [
                    "status" => "faild",
                ];
            } else {
                return [
                    "status" => "success",
                    "account_type" => @$data[0]->account_type,
                ];
            }

        }

    }
    public function checkuser(Request $request)
    {
        if ($request->method == "duplicateemail") {
            $checkuser = DB::table('users')->where("email", '=', $request->checkvalue)->first();

            if($checkuser){
                return [
                    "status" => "true",
                ];
            }else{
                return [
                    "status" => "false",
                ];
            }
        }
        if ($request->method == "duplicatemobileno") {
            $checkuser = DB::table('users')->where("mobile", '=', $request->checkvalue)->first();

            if($checkuser){
                return [
                    "status" => "true",
                ];
            }else{
                return [
                    "status" => "false",
                ];
            }
        }
    }
    public function changepassword(Request $request)
    {
        if ($request->method == "checkoldpassword") {
            $user = User::where('email', $request->email)->first();
            if (!$user || !Hash::check($request->password, $user->password)) {
                return [
                    'message' => 'Please enter your current password',
                    'status' => 'false',
                ];
            }
        }
        if ($request->method == "newpassword") {
            $update['password'] = Hash::make($request->password);
            $data = DB::table('users')->where('email', $request->email)->update($update);
            if ($data) {
                return ["message" => "success"];
            } else {
                return ["message" => "faild"];
            }
        }

    }
    public function forgotpassword(Request $req)
    {
        if ($req->method == "checkemail") {

            $user = User::where('email', $req->email)->first();

            if (!$user) {
                return [
                    'message' => 'Please enter your current Email',
                    'status' => 'false',
                ];
            } else {
                do {
                    $code = Str::random(40);

                } while (DB::table('custom_token')->where("token", "=", $code)->first());

                $emaildata = [
                    'name' => $user->name,
                    'veriication_link' => $req->verifylink . "crm/resetpassword/" . $code,
                ];

                $value = array(
                    "token" => $code,
                    "user_id" => $user->id,
                    "type" => "forgotpassword",
                    "status" => "requested",
                );
                new MailConfigProvider($user);
                Mail::to($user->email)->send(new ResetPassword($emaildata));
                DB::table('custom_token')->insert($value);
                return [
                    'status' => 'true',
                ];
            }
        }
        if ($req->method == "findemail") {
            $data = DB::table('users')
                ->select('users.*')
                ->join('custom_token', 'custom_token.user_id', '=', 'users.id')
                ->where('custom_token.token', '=', $req->token)->first();
            return $data;
        }
        if ($req->method == "newpassword") {
            $updatepass['password'] = Hash::make($req->password);
            $data = DB::table('users')->where('email', $req->email)->update($updatepass);
            if ($data) {
                return ["message" => "success"];
            } else {
                return ["message" => "faild"];
            }
        }
    }
    public function business_setup(Request $request)
    {
        do {

            $business_id = '';
            $keys = array_merge(range(0, 9), range('A', 'Z'));

            for ($i = 0; $i < 10; $i++) {
                $business_id .= $keys[array_rand($keys)];
            }

        } while (DB::table('business')->where("id", "=", $business_id)->first());

        $values = array(
            'id' => $business_id,
            'user_id' => $request->user_id,
            'business_name' => $request->name,
            'type' => $request->type,
            'pincode' => $request->pincode,
            'state' => $request->state_id,
            'district' => $request->dist_id,
            'post_office' => $request->area,
            'status' => 0,
            'create_date' => date("Y-m-d h:i:s"),
            'update_date' => date("Y-m-d h:i:s"),
        );

        $user['business_id'] = $business_id;
        $user['usertype'] = 's_admin';
        if ($request->get('busnessid') == null) {
            $data = DB::table('business')->insert($values);
            DB::table('users')->where('id', '=', $request->user_id)->update($user);
            // $link_permission = DB::table('master')->select('title')->where('cat', '=', $request->type)->where('rel_id', '=', 's_admin')->get();
            // foreach ($link_permission as $links) {
            //     do {
            //         $permission_id = Str::random(10);
            //     } while (DB::table('link_permission')->where("id", "=", $permission_id)->first());

            //     $permission['id'] = $permission_id;
            //     $permission['user_id'] = $request->user_id;
            //     $permission['link_id'] = $links->title;
            //     DB::table('link_permission')->insert($permission);

            // }
            // DB::table('users')->where('id', '=', $request->user_id)->update($user);
        } else {

            $data = DB::table('business')->where('id', '=', $request->get('busnessid'))->update($values);
        }
        if ($data) {
            return ["message" => "success"];
        } else {
            return ["message" => "faild"];
        }

    }
    public function userlist(Request $request)
    {
        if ($request->method == "Telicaller" || $request->method == "Sales" || $request->method == "Agent") {
            $data = DB::select("SELECT id, name from users where business_id='" . $request->business_id . "' and   usertype ='".$request->usertype."'");
            return $data;
        }

    }

    public function profile(Request $request)
    {
        if ($request->get('method') == "update") {
            $file = $request->file('file');
            if ($file != '') {
                $filename = $file->getClientOriginalName();
                $picture = 'businesslogo-' . date('dmYHis') . '.' . $file->getClientOriginalExtension();
                $isupload = $file->storeAs('logos', $picture);
            } else {
                $picture = '';
            }

            $values = array(
                'user_id' => $request->get('user_id'),
                'business_name' => $request->get('name'),
                'type' => $request->get('type'),
                'pincode' => $request->get('pincode'),
                'state' => $request->get('state_id'),
                'district' => $request->get('dist_id'),
                'post_office' => $request->get('area'),
                'logo' => $picture,
                'status' => 0,
                'create_date' => date("Y-m-d h:i:s"),
                'update_date' => date("Y-m-d h:i:s"),
            );
            $data = DB::table('business')->where('id', '=', $request->get('busnessid'))->update($values);
            if ($data) {
                return ["message" => "success"];
            } else {
                return ["message" => "faild"];
            }
        }
        if ($request->method == "get") {
            $data = DB::table('business')->where('id', '=', $request->business_id)->first();
            return $data;
        }
    }
    public function ledgerbook(Request $request)
    {

        // $data = DB::table('ledgerbook')->where('business_id', '=', $request->business_id)->orderBy('create_date', 'ASC')->get();
        DB::statement(DB::raw("SET @access='" . $request->access . "'"));
        $data = DB::select('SELECT * from ledgerbook

         WHERE
         case
                    WHEN (@access="avg") THEN

                        flag ="' . $request->access . '"
                    ELSE
                        flag IN ("avg", "org")
                    END

       AND   business_id="' . $request->business_id . '" AND (YEAR(date)="' . date('Y', strtotime($request->yearmonth)) . '" AND MONTH(date)="' . date('m', strtotime($request->yearmonth)) . '") ORDER BY create_date ASC');
        return $data;

    }
    public function payout(Request $request)
    {

        if ($request->method == "get") {
            if ($request->usertype == "s_admin" || $request->usertype == "Accounts") {
                DB::statement(DB::raw("SET @project='" . $request->project . "'"));
                DB::statement(DB::raw("SET @user='" . $request->user_id . "'"));
                DB::statement(DB::raw("SET @status='" . $request->status . "'"));

                $data = DB::select('

            SELECT   p.id as payout_id, u.id as user_id, u.name, u.usertype, p.status, p.payout_value, p.property_id,  p.amount, prj.project_name, p.lead_id, p.date from payout p
			INNER JOIN users u ON u.id = p.user_id
            INNER JOIN prpty_units pu ON pu.id = p.property_id
            INNER JOIN prpty_block_towers pbt ON pbt.id = pu.block_id
            INNER JOIN projects prj ON prj.id = pbt.prop_id
            WHERE
            p.business_id="' . $request->business_id . '" and
            if(@status="All",  p.status IN ("paid", "Request"),  p.status ="' . $request->status . '")
            and
            if(@user="All",  p.user_id IN (SELECT id from users where business_id="' . $request->business_id . '" ),  p.user_id ="' . $request->user_id . '")
            and
            if(@project="All",  prj.id IN (SELECT id from projects where business_id="' . $request->business_id . '"),  prj.id ="' . $request->project . '") 
            ORDER BY p.created_date DESC');
                return $data;
            }
             if ($request->usertype == "Sales" || $request->usertype == "Telicaller" || $request->usertype == "Agent") {
                DB::statement(DB::raw("SET @project='" . $request->project . "'"));
                DB::statement(DB::raw("SET @user='" . $request->user_id . "'"));
                DB::statement(DB::raw("SET @status='" . $request->status . "'"));

                $data = DB::select('

            SELECT p.id as payout_id, u.id  as user_id, u.name, u.usertype, p.status, p.payout_value,  p.property_id, p.property_value, prj.project_name, p.lead_id, p.date from payout p
			INNER JOIN users u ON u.id = p.user_id
            INNER JOIN prpty_units pu ON pu.id = p.property_id
            INNER JOIN prpty_block_towers pbt ON pbt.id = pu.block_id
            INNER JOIN projects prj ON prj.id = pbt.prop_id
            WHERE
            p.business_id="' . $request->business_id . '" and p.user_id ="' . $request->user . '" ORDER BY p.created_date DESC');
            return $data;
            }

        }
        if ($request->method == "pay") {
            // $data = DB::select('SELECT  pu.unit_no, p.payout_value, p.property_value, prj.project_name  from payout p INNER JOIN prpty_units pu ON pu.id=p.property_id
            // INNER JOIN prpty_block_towers pbt ON pbt.id=pu.block_id
            // INNER JOIN projects prj ON prj.id=pbt.prop_id
            // where p.business_id="' . $request->business_id . '" and (p.user_id="' . $request->user_id . '" AND p.status="' . $request->status . '")');
            // return $data;

            $payoutdata['payment_mode'] =$request->paymentmode; 
            $payoutdata['status'] =$request->status;  

            do {
                $ledgerbook_id = Str::random(10);
            } while (DB::table('ledgerbook')->where("id", "=", $ledgerbook_id)->first());

            $ledgerbook['id'] = $ledgerbook_id;
            $ledgerbook['business_id'] = $request->business_id;
            $ledgerbook['user_id'] = $request->payout_user_id;
            $ledgerbook['date'] = date("Y-m-d h:i:s"); ;
            $ledgerbook['lead_id'] = $request->lead_id;
            $ledgerbook['unit_id'] = $request->property_id;
            $ledgerbook['outward'] = $request->payoutamount;
            $ledgerbook['particular'] = "payout";
            $ledgerbook['payment_type'] =$request->paymentmode; 
            $ledgerbook['flag'] ="avg"; 
            $ledgerbook['date'] = date("Y-m-d h:i:s");
            DB::table('ledgerbook')->insert($ledgerbook);

            $data = DB::table('payout')->where('id', '=', $request->payout_id)->update($payoutdata);
            if ($data) {
                return ["message" => "success"];
            } else {
                return ["message" => "faild"];
            }
        }

    }
    public function expensive(Request $request)
    {
        if ($request->method == "create") {
            //    return $request->expensive_id; exit;
            if ($request->expensive_id == 0) {
                do {
                    $expensive_id = Str::random(10);
                } while (DB::table('expensive')->where("id", "=", $expensive_id)->first());

                $values['id'] = $expensive_id;
                $values['user_id'] = $request->user_id;
                $values['business_id'] = $request->business_id;
            }

            $values['pay_user_type'] = $request->pay_type;
            $values['pay_user_id'] = $request->pay_user_id;
            $values['pay_user_name'] = $request->pay_user_name;
            $values['project_id'] = $request->project_id;
            $values['particular'] = $request->Particular;
            $values['payment_mode'] = $request->payment_mode;
            $values['date'] = $request->date;
            $values['amount'] = $request->amount;
            $values['remarks'] = $request->remarks;
            $values['status'] = $request->status;

            if ($request->status == 'paid') {
                do {
                    $ledgerbook_id = Str::random(10);
                } while (DB::table('ledgerbook')->where("id", "=", $ledgerbook_id)->first());

                $ledgerbook['id'] = $ledgerbook_id;
                $ledgerbook['business_id'] = $request->business_id;
                $ledgerbook['user_id'] = $request->user_id;
                $ledgerbook['outward'] = $request->amount;
                $ledgerbook['particular'] = $request->Particular;
                $ledgerbook['payment_type'] = $request->payment_mode;
                $ledgerbook['remark'] = $request->remark;
                $ledgerbook['date'] = $request->date;
                
                $ledgerbook['flag'] = "avg";
                $booking_detailsdata = DB::table('ledgerbook')->insert($ledgerbook);
            }

            if ($request->expensive_id == 0) {

                $data = DB::table('expensive')->insert($values);

            } else {
                $data = DB::table('expensive')->where('id', '=', $request->expensive_id)->update($values);
            }

            if ($data) {
                return ["message" => "success"];
            } else {
                return ["message" => "faild"];
            }

        }
        if ($request->method == "list") {
            DB::statement(DB::raw("SET @usertype='" . $request->usertype . "'"));
            $data = DB::select("SELECT e.id, e.user_id, e.business_id, e.pay_user_type, e.pay_user_id, e.pay_user_name, e.particular, e.payment_mode, e.amount, e.project_id, e.remarks, e.date, e.date, e.status, u.name as pay_to from expensive e
            LEFT JOIN users u ON u.id=e.pay_user_id
                 where
                 case
                    WHEN (@usertype='s_admin') THEN
                        e.business_id ='" . $request->business_id . "'

                    WHEN (@usertype='Telicaller' ||  @usertype='sales'  ||  @usertype='Agent') THEN
                        e.business_id ='" . $request->business_id . "' AND e.pay_user_id='" . $request->user_id . "'
             END
             order by create_date DESC");
            return $data;

        }
    }
}
