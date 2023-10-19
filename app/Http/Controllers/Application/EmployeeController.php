<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function employee(Request $request)
    {

        if ($request->method == "create") {
            do {
                // $code = Str::random(40);
                $project_id = '';
                $keys = array_merge(range(0, 9), range('A', 'Z'));

                for ($i = 0; $i < 10; $i++) {
                    $project_id .= $keys[array_rand($keys)];
                }

            } while (DB::table('projects')->where("id", "=", $project_id)->first());

            $values['name'] = $request->name;
            $values['business_id'] = $request->business_id;
            $values['email'] = $request->email;
            $values['mobile'] = $request->mobile_no;
            $values['usertype'] = $request->emp_Department;
            $values['rel_id'] = $request->lead_leader;
            $values['password'] = Hash::make("test@123");
            if (($request->emp_Department == "Telicaller" && $request->lead_leader == '') || ($request->emp_Department == "Sales" && $request->lead_leader == '')) {
                $values['rel_type'] = "Team Leader";
            }
            $data = DB::table('users')->insert($values);

            $user = DB::table('users')->where("email", "=", $request->email)->first();
            $link_permission = DB::table('master')->select('title')->where('cat', '=', $request->business_type)->where('rel_id', '=', $request->emp_Department)->get();
            foreach ($link_permission as $links) {
                do {
                    $permission_id = Str::random(10);
                } while (DB::table('link_permission')->where("id", "=", $permission_id)->first());

                $permission['id'] = $permission_id;
                $permission['user_id'] = $user->id;
                $permission['link_id'] = $links->title;
                DB::table('link_permission')->insert($permission);

            }

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
        if ($request->method == "list") {
            $data = DB::table('users')->select('id', 'name', 'email', 'mobile', 'usertype', 'business_id', 'rel_id', 'status')->where("business_id", "=", $request->business_id)->whereIn("usertype", $request->usertype)->get();
            return $data;
        }
        if ($request->method == "details") {
            $data = DB::table('users')->where("id", "=", $request->user_id)->first();
            return $data;
        }
        if ($request->method == "teamlead_role") {
            $data = DB::table('users')->select('id', 'name', 'email', 'mobile', 'usertype', 'business_id', 'rel_id', 'status')->where("business_id", "=", $request->business_id)->where("rel_type", "=", "Team Leader")->where("usertype", "=", $request->usertype)->get();
            return $data;
        }
    }

    public function freelancer(Request $request)
    {

        if ($request->method == "create") {
            $values = array(
                'name' => $request->name,
                'business_id' => $request->business_id,
                'usertype' => "Agent",
                'email' => $request->email,
                'mobile' => $request->mobile_no,
                'status' => 0,
                'password' => Hash::make('test@123'),
            );
            // $emaildata = [
            //     'name' => $request->name,
            //     'veriication_link' => $request->verifylink . "EmailConfirmation/" . $code,
            // ];
            // Mail::to($request->email)->send(new EmailConfirmation($emaildata));

            $data = DB::table('users')->insert($values);

            $bankinfo = DB::table('bank_info')->where('user_id', '=', $request->user_id)->first();

            $bankdetails['user_id'] = $request->user_id;
            $bankdetails['ifsc_code'] = $request->ifsccode;
            $bankdetails['account_no'] = $request->account_no;
            $bankdetails['account_holder'] = $request->account_holder_name;
            $bankdetails['branch_name'] = $request->brachname;
            $bankdetails['bankcode'] = $request->bankcode;
            $bankdetails['bank_name'] = $request->bankname;
            $bankdetails['address'] = $request->lead_id;
            $bankdetails['state'] = $request->bank_state;
            $bankdetails['district'] = $request->bank_district;

            if ($bankinfo) {
                DB::table('bank_info')->where('unit_id', '=', $request->unit_id)->update($bankdetails);
            } else {
                do {
                    $bank_info_id = Str::random(10);
                } while (DB::table('bank_info')->where("id", "=", $bank_info_id)->first());

                $bankdetails['type'] = 'userinfo';
                $bankdetails['id'] = $bank_info_id;
                DB::table('bank_info')->insert($bankdetails);
            }

            $userdata = DB::table('users')->where('email', '=', $request->email)->first();
            $freelancerinfo = DB::table('freelancer')->where('user_id', '=', $userdata->id)->where('business_id', '=', $request->business_id)->first();
            $freelancerdata['commission'] = $request->commission;
            $freelancerdata['auto_upgrade'] = $request->upgrade;
            $freelancerdata['role_id'] = $request->role;
            $freelancerdata['target'] = $request->targetamount;
            $freelancerdata['target_value'] = $request->targetamountvalue;
            $freelancerdata['duration'] = $request->duration;
            $freelancerdata['duration_value'] = $request->durationvalue;
           
            if ($freelancerinfo) {
                DB::table('freelancer')->where('user_id', '=', $userdata->id)->where('business_id', '=', $request->business_id)->update($freelancerdata);
            } else {
                do {
                    $freelancerid = Str::random(10);
                } while (DB::table('freelancer')->where("freelancer_id", "=", $freelancerid)->first());

                $freelancerdata['freelancer_id'] = $freelancerid;
                $freelancerdata['reference_id'] = $request->user_id;
                $freelancerdata['user_id'] = $userdata->id;
                $freelancerdata['business_id'] = $request->business_id;
                DB::table('freelancer')->insert($freelancerdata);
            }

            do {
                $hidenid = Str::random(10);
            } while (DB::table('hidden_accounts')->where("id", "=", $hidenid)->first());

            $secpin['id'] = $hidenid;
            $secpin['user_id'] = $userdata->id;
            $secpin['business_id'] = $request->business_id;
            $secpin['account_type'] = "avg";
            $secpin['code'] = "avg";

            DB::table('hidden_accounts')->insert($secpin);

            // $recentuser = DB::table('users')->where("email", "=", $request->email)->first();
            // do {

            //     $business_id = '';
            //     $keys = array_merge(range(0, 9), range('A', 'Z'));

            //     for ($i = 0; $i < 10; $i++) {
            //         $business_id .= $keys[array_rand($keys)];
            //     }

            // } while (DB::table('business')->where("id", "=", $business_id)->first());

            // $values = array(
            //     'id' => $business_id,
            //     'user_id' => $recentuser->id,
            //     'business_name' => $request->name,
            //     'type' => $request->emp_Department,
            //     'pincode' => $request->pincode,
            //     'state' => $request->state_id,
            //     'district' => $request->dist_id,
            //     'post_office' => $request->area,
            //     'status' => 0,
            //     'create_date' => date("Y-m-d h:i:s"),
            //     'update_date' => date("Y-m-d h:i:s"),
            // );
            // $user['business_id'] = $business_id;
            // $user['usertype'] = 'Agent';

            // $data = DB::table('business')->insert($values);
            // DB::table('users')->where('id', '=', $recentuser->id)->update($user);
            // do {
            //     $freelancer_id = '';
            //     $keys = array_merge(range(0, 9), range('A', 'Z'));
            //     for ($i = 0; $i < 10; $i++) {
            //         $freelancer_id .= $keys[array_rand($keys)];
            //     }

            // } while (DB::table('freelancer')->where("freelancer_id", "=", $freelancer_id)->first());
            // $freelancer = array(
            //     'freelancer_id' => $freelancer_id,
            //     'request_by' => $request->business_id,
            //     'accepted_by' => $business_id,
            //     'status' => "acceptd",
            //     'date' => date("Y-m-d h:i:s"),
            // );
            // DB::table('freelancer')->insert($freelancer);

            $user = DB::table('users')->where("email", "=", $request->email)->first();
            $link_permission = DB::table('master')->select('title')->where('cat', '=', $request->business_type)->where('rel_id', '=', $request->emp_Department)->get();
            foreach ($link_permission as $links) {
                do {
                    $permission_id = Str::random(10);
                } while (DB::table('link_permission')->where("id", "=", $permission_id)->first());

                $permission['id'] = $permission_id;
                $permission['user_id'] = $user->id;
                $permission['link_id'] = $links->title;
                DB::table('link_permission')->insert($permission);

            }

            if ($data) {
                return ["message" => "success"];
            } else {
                return ["message" => "faild"];
            }
        }
        if ($request->method == "get") {
            DB::statement(DB::raw("SET @usertype='" . $request->usertype . "'"));
            $data = DB::select('SELECT u.id, u.name, u.email, u.mobile, u.usertype from users u
            where
            case
                WHEN (@usertype="s_admin") THEN
                    u.business_id="' . $request->business_id . '" and u.usertype IN("Agent")

                    WHEN (@usertype="Agent") THEN
                        u.id IN (SELECT user_id from freelancer where reference_id="' . $request->user_id. '")
                    END
                ');
            return $data;
        }

    }
    public function link_permissions(Request $request)
    {
        if ($request->method == "getemplink") {
            // $menu= DB::select('SELECT * from links where link_type=1 AND  status IN ("active") and id IN (SELECT link_id from link_permission where user_id="'.$request->user_id.'" ) order by orderby ASC');

            $menu = DB::select('SELECT l.id, l.link_name, l.path, l.image, IF( lp.link_id IS NULL, "false", "true") as checked from links l LEFT JOIN link_permission lp ON lp.link_id=l.id AND lp.user_id="' . $request->user_id . '" where l.link_type=1 AND  l.status IN ("active") ');

            $data = array();
            foreach ($menu as $f => $mainmenu) {
                $data[$f]['id'] = $mainmenu->id;
                $data[$f]['link_name'] = $mainmenu->link_name;
                $data[$f]['path'] = $mainmenu->path;
                $data[$f]['image'] = $mainmenu->image;
                $data[$f]['checked'] = $mainmenu->checked;
                $data[$f]['submenu'] = DB::select('SELECT l.id, l.link_name, l.path, l.image, IF( lp.link_id IS NULL, "false", "true") as checked from links l  LEFT JOIN link_permission lp ON lp.link_id=l.id  AND lp.user_id="' . $request->user_id . '"  where l.link_id ="' . @$mainmenu->id . '" AND  l.status IN ("active")');
                // $data[$f]['submenu']=$submenu ;
            }
            return $data;

        }

        if ($request->method == "change_permission") {

            if ($request->link_status == "true") {

                do {
                    $linkid = Str::random(10);
                } while (DB::table('link_permission')->where("id", "=", $linkid)->first());

                $linkvalue['id'] = $linkid;
                $linkvalue['user_id'] = $request->user_id;
                $linkvalue['link_id'] = $request->linkid;

                DB::table('link_permission')->where("user_id", '=', $request->user_id)->insert($linkvalue);
            } else {
                DB::table('link_permission')->where("user_id", '=', $request->user_id)->where('link_id', '=', $request->linkid)->delete();
            }
        }
    }
    public function agent(Request $request)
    {

        if ($request->method == "upgrade") {
            do {
                $agent_roles_master_id = Str::random(10);
            } while (DB::table('agent_roles_master')->where("id", "=", $agent_roles_master_id)->first());
            $values = array(
                'id' => $agent_roles_master_id,
                'business_id' => $request->business_id,
                'commination' => $request->commination,
                // 'timeduration' => $request->duration,
                // 'timedurationvalue' => $request->durationvalue,
                'level' => $request->level,
                'role_title' => $request->rolename,
                // 'target' => $request->targetamount,
                // 'targetamount' => $request->targetamountvalue,
                // 'auto_upgrade' => $request->upgrade,
            );
            $data = DB::table('agent_roles_master')->insert($values);

            if ($data) {
                return ["message" => "success"];
            } else {
                return ["message" => "faild"];
            }

        }
        if ($request->method == "get") {
            $userdetails  = DB::select('select arm.level from users u
            INNER JOIN freelancer f ON f.user_id =u.id
            INNER JOIN agent_roles_master arm ON arm.id=f.role_id
            where u.id="'.$request->user_id.'" ');
           

            
            DB::statement(DB::raw("SET @usertype='" . $request->usertype . "'"));
            $data = DB::select('SELECT * from agent_roles_master  where
             case
            WHEN  (@usertype="s_admin") THEN
            business_id ="'.$request->business_id.'" 

            WHEN (@usertype="Agent") THEN
            business_id ="'.$request->business_id.'" AND level > "'. @$userdetails[0]->level.'" 
            END

            order by level ASC
            ');
            
            return $data;
        }
    }
    public  function test(Request $request)
    {
// return DB::table('users')->get();
        return DB::select( DB::raw('INSERT INTO users (name) VALUES ("praveen")'));
    }
}
