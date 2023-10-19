<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use App\Mail\PropertyPaymentConfirmation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Mail;

class ProjectController extends Controller
{
    public function project(Request $request)
    {

        if ($request->method == "create") {
            do {
                $project_id = Str::random(10);
                // $project_id = '';
                // $keys = array_merge(range(0, 9), range('A', 'Z'));

                // for ($i = 0; $i < 10; $i++) {
                //     $project_id .= $keys[array_rand($keys)];
                // }

            } while (DB::table('projects')->where("id", "=", $project_id)->first());

            $values['user_id'] = $request->user_id;
            $values['business_id'] = $request->business_id;
            $values['project_name'] = $request->project_name;
            $values['type'] = $request->type;
            $values['approvelby'] = $request->approvelby;
            $values['startdate'] = $request->startdate;
            $values['projectsize'] = $request->projectsize;
            $values['reraid'] = $request->reraid;
            $values['pincode'] = $request->pincode;
            $values['state'] = $request->state_id;
            $values['city'] = $request->dist_id;
            $values['area'] = $request->area;
            $values['landmark'] = $request->landmark;
            $values['fulladdress'] = $request->fulladdress;
            $values['status'] = $request->status;
            $values['create_date'] = date("Y-m-d h:i:s");
            $values['update_date'] = date("Y-m-d h:i:s");

            if ($request->project_id == "") {
                $values['id'] = $project_id;
                $data = DB::table('projects')->insert($values);
            } else {
                $values['about_us'] = $request->about_us;
                $data = DB::table('projects')->where('id', '=', $request->project_id)->update($values);
            }

            if ($data) {
                return ["message" => "success"];
            } else {
                return ["message" => "faild"];
            }
        }
        if ($request->method == "list") {
            // $data = DB::select('SELECT * from projects where
            // IF
            //     @business_type=="Developer" THEN
            //     business_id="'.$request->business_id.'"');
            //  END;
            DB::statement(DB::raw("SET @businesstype='" . $request->business_type . "'"));
            $data = DB::select("SELECT * from projects
                where
                    case
                        WHEN (@businesstype='developer') THEN
                            business_id='" . $request->business_id . "'

                        END
                  ");
            //   WHEN (@businesstype='Agent') THEN
            //   business_id IN(SELECT request_by from freelancer where accepted_by='" . $request->business_id . "')
            return $data;

        }
        if ($request->method == "details") {
            $data = DB::table('projects')->where("id", "=", $request->project_id)->get();
            return $data;
        }
    }
    public function amenities(Request $request)
    {

        $data = DB::select("SELECT m.id, m.cat, m.type, m.title, m.image, m.status, IF(pa.id IS NULL, 'false', 'true')  as checked, pa.id as test from master m LEFT JOIN prop_amenities pa ON m.id=pa.amenities_id AND pa.prop_id='" . $request->prop_id . "'  WHERE (m.type ='" . $request->type . "' AND m.status='" . $request->status . "') AND m.cat='" . $request->cat_id . "'");

        return $data;
    }
    public function configuration(Request $request)
    {
        if ($request->method == 'create') {
            // $values['']=$request->prop_id;
            $prop_details['type'] = $request->property_type;
            $prop_details['prop_status'] = $request->PossessionStatus;
            $prop_details['units'] = $request->Units;
            $prop_details['price'] = $request->saleprice;
            $prop_details['gove_price'] = $request->govprice;
            $prop_details['avg_price'] = $request->avgprice;

            /*Block and line start*/

            DB::table('prpty_paymentmode')->where('project_id', '=', $request->prop_id)->delete();
            for ($pay = 0; $pay <= count($request->paymentmode) - 1; $pay++) {

                do {
                    $paymentmodeid = Str::random(10);
                } while (DB::table('prpty_paymentmode')->where("id", "=", $paymentmodeid)->first());
                $paymentmodedata['id'] = $paymentmodeid;
                $paymentmodedata['project_id'] = $request->prop_id;
                $paymentmodedata['paymentmode'] = $request->paymentmode[$pay];

                DB::table('prpty_paymentmode')->insert($paymentmodedata);

            }
            $data = DB::table('projects')->where("id", "=", $request->prop_id)->update($prop_details);

            if ($data) {
                return ["message" => "success"];
            } else {
                return ["message" => "faild"];
            }

        }
        if ($request->method == "Amenities") {
            if ($request->action == "update") {
                DB::table('prop_amenities')->where('prop_id', $request->prop_id)->delete();
                // return  $request->amenities[0]; exit;
                if ($request->amenities[0] != '') {
                    for ($i = 0; $i <= count($request->amenities) - 1; $i++) {
                        do {
                            $amenities_id = Str::random(10);
                        } while (DB::table('prop_amenities')->where("id", "=", $amenities_id)->first());
                        $amenities_ins['id'] = $amenities_id;
                        $amenities_ins['prop_id'] = $request->prop_id;
                        $amenities_ins['amenities_id'] = $request->amenities[$i];

                        DB::table('prop_amenities')->insert($amenities_ins);

                    }
                }
            }

        }

        if ($request->method == "plotsize") {
            if ($request->action == "create") {

                $property_size['prop_id'] = $request->prop_id;
                $property_size['area'] = $request->area;
                $property_size['height'] = $request->height;
                $property_size['width'] = $request->width;
                $property_size['units'] = $request->units;
                $property_size['status'] = $request->status;
                if ($request->plotsize_id == "") {
                    do {
                        $property_size_id = Str::random(10);
                    } while (DB::table('property_size')->where("id", "=", $property_size_id)->first());

                    $property_size['id'] = $property_size_id;
                    $data = DB::table('property_size')->insert($property_size);
                } else {
                    $data = DB::table('property_size')->where('id', '=', $request->plotsize_id)->update($property_size);
                }

                if ($data) {
                    return ["message" => "success"];
                } else {
                    return ["message" => "faild"];
                }
            }
            if ($request->action == "get") {
                $data = DB::table('property_size as ps')
                    ->where('ps.prop_id', '=', $request->prop_id)->get();

                return $data;
            }

        }

        if ($request->method == "plotprice") {
            if ($request->action == "create") {

                $unit_prices['project_id'] = $request->prop_id;
                $unit_prices['price'] = $request->price;
                $unit_prices['type'] = $request->type;
                $unit_prices['faceing'] = $request->Faceing;
                if ($request->priceinfo_id == '') {
                    do {
                        $plotprice_id = Str::random(10);
                    } while (DB::table('unit_prices')->where("unit_prices_id", "=", $plotprice_id)->first());

                    $unit_prices['unit_prices_id'] = $plotprice_id;
                    $data = DB::table('unit_prices')->insert($unit_prices);
                } else {
                    $data = DB::table('unit_prices')->where('unit_prices_id', '=', $request->priceinfo_id)->update($unit_prices);
                }

                if ($data) {
                    return ["message" => "success"];
                } else {
                    return ["message" => "faild"];
                }
            }
            if ($request->action == "get") {
                $data = DB::table('unit_prices as up')->selectRaw('up.*, master.title')
                    ->join('master', 'master.id', '=', 'up.faceing')
                    ->where('up.project_id', '=', $request->prop_id)->get();

                return $data;
            }

        }

    }
    public function prptypaumentmode(Request $request)
    {

        if ($request->method == 'list') {

            $data = DB::table('master')
                ->selectRaw('prpty_paymentmode.project_id, master.id, master.title, master.id, IF(prpty_paymentmode.paymentmode is null, "false", "true")  as checked')
                ->leftjoin('prpty_paymentmode', 'prpty_paymentmode.paymentmode', '=', 'master.id')
                ->where('master.type', '=', $request->type)->whereIn('master.status', $request->status)->get();
            return $data;
        }
        if ($request->method == 'get_project_paymentmode') {

            $data = DB::table('master')
                ->selectRaw('prpty_paymentmode.project_id, master.id, master.title, master.id')
                ->join('prpty_paymentmode', 'prpty_paymentmode.paymentmode', '=', 'master.id')
                ->where('prpty_paymentmode.project_id', '=', $request->project_id)->get();
            return $data;
        }
    }
    public function paymentconfig(Request $request)
    {
        if ($request->method == 'pricebackup_update') {

            $pricebackup['booking'] = $request->booking;
            $pricebackup['advance_payment'] = $request->advancepayment;

            $data = DB::table('projects')->where('id', '=', $request->project_id)->update($pricebackup);

            if ($data) {
                return ["message" => "success"];
            } else {
                return ["message" => "faild"];
            }
        }
        if ($request->method == 'pricebackup_get') {

            $data = DB::table('projects')->selectRaw('booking, advance_payment')->where('id', '=', $request->project_id)->get();

            return $data;

        }
    }
    public function blocktowers(Request $request)
    {
        if ($request->method == "create") {

            do {
                $blockId = Str::random(10);
            } while (DB::table('prpty_block_towers')->where("id", "=", $blockId)->first());

            $Block['id'] = $blockId;
            $Block['prop_id'] = $request->prop_id;
            $Block['title'] = $request->blockname;
            $Block['total_units'] = $request->totalunits;
            DB::table('prpty_block_towers')->insert($Block);

            $insertBlock = DB::table('prpty_block_towers')->where('prop_id', '=', $request->prop_id)->where('id', '=', $blockId)->first();

            for ($l = $request->min; $l <= $request->max; $l++) {
                do {
                    $lineID = Str::random(10);
                } while (DB::table('block_line_flores')->where("id", "=", $lineID)->first());

                $lines['id'] = $lineID;
                $lines['block_id'] = $blockId;
                $lines['title'] = "Line " . +$l;
                $lines['num'] = $l;

                DB::table('block_line_flores')->insert($lines);
            }

            for ($fp = 0; $fp <= $request->totalunits - 1; $fp++) {
                do {
                    $unit_id = Str::random(10);
                } while (DB::table('prpty_units')->where("id", "=", $unit_id)->first());

                $units['id'] = $unit_id;
                $units['block_id'] = $blockId;
                $units['unit_no'] = $fp + 1;
                DB::table('prpty_units')->insert($units);
            }
            if ($insertBlock) {
                return ["message" => "success"];
            } else {
                return ["message" => "faild"];
            }

        }
        if ($request->method == 'get') {
            $data = DB::select('SELECT pbt.id, pbt.title,
                (select MIN(num) from block_line_flores where block_id=pbt.id) as min,
                (select MAX(num) from block_line_flores where block_id=pbt.id) as max ,
                (select count(id) from prpty_units where block_id=pbt.id) as totalunits
            from
                prpty_block_towers pbt
            where
                pbt.prop_id="' . $request->prop_id . '"');
            return $data;

        }

    }
    public function size_conf(Request $request)
    {
        if ($request->method == "create") {
            do {
                $property_sizeid = Str::random(10);

            } while (DB::table('property_size')->where("id", "=", $property_sizeid)->first());

            $property_size['id'] = $property_sizeid;
            $property_size['prop_id'] = $request->prop_id;
            $property_size['area'] = $request->area;
            $property_size['width'] = $request->width;
            $property_size['height'] = $request->height;
            $property_size['units'] = $request->units;

            $data = DB::table('property_size')->insert($property_size);

            if ($data) {
                return ["message" => "success"];
            } else {
                return ["message" => "faild"];
            }
        }
        if ($request->method == 'get') {
            $data = DB::select('SELECT * from  property_size where prop_id="' . $request->prop_id . '"');
            return $data;

        }
    }
    public function layout(Request $request)
    {
        if ($request->method == 'get') {

            $data['lines'] = DB::select("SELECT blf.id, blf.title, blf.block_id from block_line_flores blf   where block_id='" . $request->block_id . "'");

            $layout = array();
            foreach ($data['lines'] as $l => $lines) {

                $layout[$l]['line_id'] = $lines->id;
                $layout[$l]['title'] = $lines->title;
                $layout[$l]['block_id'] = $lines->block_id;

                $data['faceing'] = DB::select("SELECT lf.faceing_id, m.title from line_facing lf INNER JOIN master m ON m.id=lf.faceing_id  where line_id='" . $lines->id . "'");

                $faecingdata = array();
                foreach ($data['faceing'] as $f => $faecing) {
                    $faecingdata[$f]['faceing_id'] = $faecing->faceing_id;
                    $faecingdata[$f]['title'] = $faecing->title;

                    $data['units'] = DB::select("SELECT lu.id, lu.unit_no as title, lu.type as unit_type, lu.lead_id, ps.area as size, ps.width, ps.height, p.units,  m.title as faceing,  lu.status,
                    (ps.area*(SELECT up.price from unit_prices up where up.type=lu.type and up.faceing=lu.facing limit 1)) as estprice,
                     (SELECT up.price from unit_prices up where up.type=lu.type and up.faceing=lu.facing limit 1) as price,
                     l.lead_status, p.gove_price, p.avg_price, p.booking, p.advance_payment, (SELECT SUM(amount) as paidamt from payment_history   where unit_id=lu.id) as paidamt, p.id as project_id
                    from prpty_units  lu
                        INNER JOIN property_size ps ON ps.id=lu.size_id
                        INNER JOIN prpty_block_towers pbt ON pbt.id=lu.block_id
                        INNER JOIN  projects p ON p.id=pbt.prop_id
                        INNER JOIN master m ON m.id=lu.facing
                        LEFT JOIN leads l ON l.id=lu.line_id
                    where lu.facing='" . $faecing->faceing_id . "' and lu.line_id='" . $lines->id . "'");

                    $faecingdata[$f]['units'] = $data['units'];

                }

                $layout[$l]['faceing'] = $faecingdata;

            }

            return $layout;
        }
        if ($request->method == 'get_blocks') {
            $data = DB::select("SELECT * from prpty_block_towers where prop_id='" . $request->project_id . "'");
            return $data;
        }
        //

    }
    public function line_units(Request $request)
    {

        if ($request->method == 'edit_line_units') {
            $data = DB::select('SELECT id, block_id, line_id, unit_no, lead_id, facing, size_id, price, status,  IF(line_id IS NULL, "false", "true")  as checked  from  prpty_units where (block_id="' . $request->block_id . '" and line_id="' . $request->line_id . '" and facing="' . $request->faceing . '" AND size_id="' . $request->size . '"  AND type="' . $request->type . '") || (block_id="' . $request->block_id . '" and line_id IS NULL)');
            return $data;
        }
        if ($request->method == 'details') {
            $data = DB::select('SELECT * from  block_line_flores where id="' . $request->line_id . '"');
            return $data;
        }

        if ($request->method == 'update') {
            $line_faceing_data = DB::table('line_facing')->where('line_id', '=', $request->line_id)->where('faceing_id', '=', $request->faceing)->get();

            if (count($line_faceing_data) == 0) {
                do {
                    $line_facing_id = Str::random(10);
                } while (DB::table('line_facing')->where("id", "=", $line_facing_id)->first());

                $line_faceing['id'] = $line_facing_id;
                $line_faceing['line_id'] = $request->line_id;
                $line_faceing['faceing_id'] = $request->faceing;

                DB::table('line_facing')->insert($line_faceing);

            }
            $line_faceing_data = DB::table('line_facing')->where('line_id', '=', $request->line_id)->where('faceing_id', '=', $request->faceing)->get();

            for ($i = 0; $i <= count($request->units) - 1; $i++) {
                $checkunitupdate = DB::table('prpty_units')->where('line_id', '=', $request->line_id)->where('facing', '=', $request->faceing)->where('size_id', '=', $request->size)->where('type', '=', $request->type)->get();
                // if(count( $checkunitupdate)==0){
                $amenities_ins['line_id'] = $request->line_id;
                $amenities_ins['facing'] = $request->faceing;
                $amenities_ins['size_id'] = $request->size;
                $amenities_ins['type'] = $request->type;
                // $amenities_ins['amenities_id'] = $request->amenities[$i];

                DB::table('prpty_units')->where('id', '=', $request->units[$i])->update($amenities_ins);
                // }

            }
            $updateunits['line_id'] = null;
            $updateunits['facing'] = null;
            $updateunits['size_id'] = null;
            DB::table('prpty_units')->where('line_id', '=', $request->line_id)->where('facing', '=', $request->faceing)->where('size_id', '=', $request->size)->where('type', '=', $request->type)->whereNotIn('id', $request->units)->update($updateunits);
            // DB::select("UPDATE prpty_units SET line_id='' ,facing='',size_id='' WHERE  line_id = '".$request->line_id."' AND id NOT IN (implode(',', $request->units))");

        }
    }

    public function payments(Request $request)
    {

        if ($request->method == 'bookingpayment') {

            do {
                $booking_details_id = Str::random(10);
            } while (DB::table('booking_details')->where("id", "=", $booking_details_id)->first());

            $booking_details['id'] = $booking_details_id;
            $booking_details['business_id'] = $request->business_id;
            $booking_details['user_id'] = $request->user_id;
            $booking_details['lead_id'] = $request->lead_id;
            $booking_details['unit_id'] = $request->unit_id;
            $booking_details['date'] = $request->date;
            if ($request->paymenttype == 'PAY0001') {
                $booking_details['status'] = $request->type;
            }
            if ($request->paymenttype == 'PAY0002') {
                $booking_details['status'] = "block";
            }
            if ($request->paymenttype == 'PAY004') {
                $booking_details['status'] = "Registration";
            }
            $booking_details['create_date'] = date("Y-m-d h:i:s");
            $booking_detailsdata = DB::table('booking_details')->insert($booking_details);
            /*Booking details end*/

            /*ledger book start*/

            do {
                $ledgerbook_id = Str::random(10);
            } while (DB::table('ledgerbook')->where("id", "=", $ledgerbook_id)->first());

            $ledgerbook['id'] = $ledgerbook_id;
            $ledgerbook['business_id'] = $request->business_id;
            $ledgerbook['user_id'] = $request->user_id;
            $ledgerbook['lead_id'] = $request->lead_id;
            $ledgerbook['unit_id'] = $request->unit_id;

            if ($request->paymenttype == 'PAY0001') {
                $ledgerbook['particular'] = $request->type;
            }
            if ($request->paymenttype == 'PAY0002') {
                $ledgerbook['particular'] = 'Part payment';
            }
            if ($request->paymenttype == 'PAY004') {
                $ledgerbook['particular'] = 'Buy Back';
            }

            $ledgerbook['payment_type'] = $request->payment_mode;
            $ledgerbook['remark'] = $request->remark;
            $ledgerbook['date'] = $request->date;

            /*ledger book end*/

            /*payment_history start*/

            do {
                $payment_history_id = Str::random(10);
            } while (DB::table('payment_history')->where("id", "=", $payment_history_id)->first());

            $payment_history['id'] = $payment_history_id;
            $payment_history['business_id'] = $request->business_id;
            $payment_history['user_id'] = $request->user_id;
            $payment_history['lead_id'] = $request->lead_id;
            $payment_history['unit_id'] = $request->unit_id;
            $payment_history['type'] = $request->type;
            $payment_history['payment_type'] = $request->payment_mode;
            $payment_history['remark'] = $request->remark;

            $payment_history['date'] = $request->date;

            $unitsize = DB::table('prpty_units')
                ->selectRaw('property_size.area, (SELECT price from unit_prices up where type=prpty_units.type and faceing=prpty_units.facing limit 1) as price, projects.gove_price , projects.avg_price, IF(business.avg=1, "true", "false") as avg, business.gov ')

                ->join('property_size', 'property_size.id', '=', 'prpty_units.size_id')
                ->join('projects', 'projects.id', '=', 'property_size.prop_id')
                ->join('business', 'business.id', '=', 'projects.business_id')
                ->where('prpty_units.id', '=', $request->unit_id)->first();
            if ($unitsize->avg == "true") {
                $paidamt = DB::select('SELECT SUM(amount) as paidamt from payment_history where unit_id="' . $request->unit_id . '"');
                $totalpaidamt = $paidamt[0]->paidamt + (int) $request->amount;

                $orgvalue = $unitsize->avg_price * $unitsize->area;

                if ($totalpaidamt > $orgvalue) {

                    $remaingamt = $totalpaidamt - $orgvalue;
                    // return  $remaingamt; exit;
                    if ($remaingamt > $request->amount) {
                        $this->payout($request, $request->amount);
                        $payment_history['amount'] = $request->amount;

                        $ledgerbook['flag'] = "org";

                        $ledgerbook['flag'] = "org";
                        $ledgerbook['inword'] = $request->amount;

                    } else {
                        $payment_history['amount'] = $orgvalue - $paidamt[0]->paidamt;
                        $payment_history['flag'] = "avg";

                        $ledgerbook['flag'] = "avg";
                        $ledgerbook['inword'] = $orgvalue - $paidamt[0]->paidamt;
                        $this->payout($request, $orgvalue - $paidamt[0]->paidamt);
                    }

                    $payment_historydata = DB::table('payment_history')->insert($payment_history);

                    $booking_detailsdata = DB::table('ledgerbook')->insert($ledgerbook);

                    if ($remaingamt < $request->amount) {
                        do {
                            $payment_history_id = Str::random(10);
                        } while (DB::table('payment_history')->where("id", "=", $payment_history_id)->first());

                        do {
                            $ledgerbook_id = Str::random(10);
                        } while (DB::table('ledgerbook')->where("id", "=", $ledgerbook_id)->first());
                        $payment_history['id'] = $payment_history_id;
                        $payment_history['flag'] = "org";
                        $payment_history['amount'] = $totalpaidamt - $orgvalue;
                        $payment_historydata = DB::table('payment_history')->insert($payment_history);

                        $ledgerbook['id'] = $ledgerbook_id;
                        $ledgerbook['flag'] = "org";
                        $ledgerbook['inword'] = $totalpaidamt - $orgvalue;
                        $this->payout($request, $totalpaidamt - $orgvalue);
                        $booking_detailsdata = DB::table('ledgerbook')->insert($ledgerbook);
                    }

                } else {
                    do {
                        $payment_history_id = Str::random(10);
                    } while (DB::table('payment_history')->where("id", "=", $payment_history_id)->first());

                    do {
                        $ledgerbook_id = Str::random(10);
                    } while (DB::table('ledgerbook')->where("id", "=", $ledgerbook_id)->first());

                    $payment_history['id'] = $payment_history_id;
                    $payment_history['flag'] = "avg";
                    $payment_history['amount'] = $request->amount;

                    $payment_historydata = DB::table('payment_history')->insert($payment_history);

                    $ledgerbook['id'] = $ledgerbook_id;
                    $ledgerbook['flag'] = "avg";
                    $ledgerbook['inword'] = $request->amount;
                    $this->payout($request, $request->amount);
                    $booking_detailsdata = DB::table('ledgerbook')->insert($ledgerbook);
                }
            } else {
                $payment_history['flag'] = "avg";
                $payment_history['amount'] = $request->amount;
                $payment_historydata = DB::table('payment_history')->insert($payment_history);

                $ledgerbook['inword'] = $request->amount;
                $ledgerbook['flag'] = "avg";
                $this->payout($request, $request->amount);
                $booking_detailsdata = DB::table('ledgerbook')->insert($ledgerbook);
            }

            if ($request->paymenttype == 'PAY0002') {
                for ($e = 1; $e <= $request->emi_tunure; $e++) {
                    do {
                        $payment_shedule_id = Str::random(10);
                    } while (DB::table('payment_shedule')->where("id", "=", $payment_shedule_id)->first());

                    $emi_schedule['id'] = $payment_shedule_id;
                    $emi_schedule['shedule_type'] = "emi";
                    $emi_schedule['business_id'] = $request->business_id;
                    $emi_schedule['project_id'] = $request->project_id;
                    $emi_schedule['lead_id'] = $request->lead_id;
                    $emi_schedule['unit_id'] = $request->unit_id;
                    $emi_schedule['due_amount'] = $request->emi_amount;
                    $emi_schedule['emi_date'] = date("Y-m-" . $request->emi_date . " h:i:s", strtotime(+$e . " month"));

                    $emi_schedule['status'] = 'created';
                    DB::table('payment_shedule')->insert($emi_schedule);
                }

            }
            if ($request->paymenttype == 'PAY004') {
                $buyback = DB::select('SELECT * from bauyback_master where project_id="' . $request->project_id . '" and DAY("' . $request->date . '") BETWEEN from_date AND to_date');

                $date = date('Y-m-d', strtotime(date('Y-m-' . $buyback[0]->to_date) . ' + ' . +$buyback[0]->first_payout_duration . ' days'));

                for ($b = 0; $b < $buyback[0]->tenure; $b++) {
                    do {
                        $buyback_id = Str::random(10);
                    } while (DB::table('payment_shedule')->where("id", "=", $buyback_id)->first());

                    $emi_schedule['id'] = $buyback_id;
                    $emi_schedule['shedule_type'] = "buyback";
                    $emi_schedule['business_id'] = $request->business_id;
                    $emi_schedule['project_id'] = $request->project_id;
                    $emi_schedule['lead_id'] = $request->lead_id;
                    $emi_schedule['unit_id'] = $request->unit_id;
                    $emi_schedule['due_amount'] = $request->property_value / $buyback[0]->tenure;
                    $emi_schedule['emi_date'] = date('Y-m-d', strtotime($date . ' + ' . $b . ' month'));
                    $emi_schedule['status'] = 'created';
                    DB::table('payment_shedule')->insert($emi_schedule);
                }

            }

            /*payment_history end*/
            if ($request->type != 'partial amount') {
                if ($request->paymenttype == 'PAY0001') {
                    $leadupdate['lead_status'] = $request->type;
                }
                if ($request->paymenttype == 'PAY0002') {
                    $leadupdate['lead_status'] = "block";
                }
                if ($request->paymenttype == 'PAY004') {
                    $leadupdate['lead_status'] = "Registration";
                }
                $leadupdate['unit_id'] = $request->unit_id;
                DB::table('leads')->where('id', '=', $request->lead_id)->update($leadupdate);

                if ($request->propertytype == "new") {
                    $unitupdate['price'] = $request->property_value;
                    $unitupdate['sftprice'] = $request->sftprice;
                    $unitupdate['payment_mode'] = $request->paymenttype;
                }
                if ($request->paymenttype == 'PAY0001') {
                    $unitupdate['status'] = $request->type;
                }
                if ($request->paymenttype == 'PAY0002') {
                    $unitupdate['status'] = "block";
                }
                if ($request->paymenttype == 'PAY004') {
                    $unitupdate['status'] = "Registration";
                }
                $unitupdate['lead_id'] = $request->lead_id;

                DB::table('prpty_units')->where('id', '=', $request->unit_id)->update($unitupdate);
            }

            /*payout start*/

            $emaildata = [
                'name' => $request->name,
                'business_id' => $request->business_id,
                'lead_id' => $request->lead_id,
                'particular' => $request->type,
                'unit_id' => $request->unit_id,
                'amount' => $request->amount,
            ];
            Mail::to('krishnareyyi@gmail.com')->send(new PropertyPaymentConfirmation($emaildata));

            /*Booking details start*/

            /*payout end */

            if ($booking_detailsdata && $booking_detailsdata && $payment_historydata) {
                return ["message" => "success"];
            } else {
                return ["message" => "faild"];
            }

        }
        if ($request->method == 'payment-History') {
            DB::statement(DB::raw("SET @access='" . $request->access . "'"));
            $data['payment_history'] = DB::select('SELECT * from payment_history
            where
                case
                    WHEN (@access="avg") THEN

                        flag ="' . $request->access . '"  AND unit_id ="' . $request->unit_id . '"
                    ELSE
                        unit_id ="' . $request->unit_id . '"
                    END

                    ORDER BY create_date DESC
            ');
            $data['paidamount'] = DB::select('SELECT SUM(amount) as paidamount from payment_history
            where
                case
                    WHEN (@access="avg") THEN

                        flag ="' . $request->access . '"  AND unit_id ="' . $request->unit_id . '"
                    ELSE
                        unit_id ="' . $request->unit_id . '"
                    END

                    ORDER BY create_date DESC
            ');

            // $data = DB::table('payment_history')->where('unit_id', '=', $request->unit_id)->orderBy('create_date', 'DESC')->get();
            return $data;
        }
        if ($request->method == 'booking-details') {
            $data = DB::table('prpty_units as pu')->selectRaw('l.id as lead_id, pu.price as buyprice, l.name, l.mobile_no, l.mobile_no, l.email, l.Area, l.full_address, l.land_mark, l.pincode, l.State, l.City, l.adhar_no, l.pan, f.title as faceing, ps.area as pu_size, ps.height, ps.width,  pu.unit_no, states.StateName, districts.district_name, p.project_name, pbt.title as block, blf.title as line, bi.ifsc_code, bi.account_no, bi.account_holder, bi.branch_name, bi.bankcode, bi.bank_name, bi.address, bi.state, bi.district')
                ->leftjoin('master as f', 'f.id', '=', 'pu.facing')
                ->leftjoin('property_size as ps', 'ps.id', '=', 'pu.size_id')
                ->join('leads as l', 'l.id', '=', 'pu.lead_id')
                ->leftjoin('states', 'states.state_id', '=', 'l.State')
                ->leftjoin('bank_info as bi', 'bi.unit_id', '=', 'pu.id')
                ->leftjoin('districts', 'districts.districts_id', '=', 'l.City')
                ->leftjoin('projects as p', 'p.id', '=', 'l.project_id')
                ->leftjoin('prpty_block_towers as pbt', 'pbt.id', '=', 'pu.block_id')
                ->leftjoin('block_line_flores as blf', 'blf.id', '=', 'pu.line_id')
                ->where('pu.id', '=', $request->unit_id)->get();
            return $data;
        }
        if ($request->method == 'personalinfoupdate') {
            $personlinfo['name'] = $request->name;
            $personlinfo['mobile_no'] = $request->mobile_no;
            $personlinfo['email'] = $request->email;
            $personlinfo['adhar_no'] = $request->adharno;
            $personlinfo['pan'] = $request->panno;
            $personlinfo['pincode'] = $request->pincode;
            $personlinfo['Area'] = $request->area;
            $personlinfo['full_address'] = $request->address;
            $personlinfo['land_mark'] = $request->landmark;
            $personlinfo['State'] = $request->state_id;
            $personlinfo['City'] = $request->dist_id;

            $bankinfo = DB::table('bank_info')->where('unit_id', '=', $request->unit_id)->first();
            $bankdetails['unit_id'] = $request->unit_id;
            $bankdetails['lead_id'] = $request->lead_id;

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
                $bankdetails['type'] = 'lead_info';
                $bankdetails['id'] = $bank_info_id;
                DB::table('bank_info')->insert($bankdetails);
            }
            $data = DB::table('leads')->where('id', '=', $request->lead_id)->update($personlinfo);
            if ($data) {
                return ["message" => "success"];
            } else {
                return ["message" => "faild"];
            }

        }
        if ($request->method == 'EMI schedule') {

            $data = DB::table('payment_shedule')->where('unit_id', '=', $request->unit_id)->get();
            return $data;

        }
        if ($request->method == 'buyback') {

            $data = DB::table('payment_shedule')->where('unit_id', '=', $request->unit_id)->get();
            return $data;

        }
        if ($request->method == 'checkpaymenttype') {

            $data = DB::table('payment_history')->where('lead_id', '=', $request->lead_id)->where('unit_id', '=', $request->unit_id)->where('type', '=', $request->paymenttype)->get();

            if (count($data) == 0) {
                return ["message" => "true"];
            } else {
                return ["message" => "false"];
            }
        }
        if ($request->method == 'checkpaidamount') {
            $data = DB::select('SELECT SUM(amount) as paidamt from payment_history   where unit_id="' . $request->unit_id . '"');
            return $data;
        }
    }
    public function payout($request, $value)
    {
        $lead_details = DB::table('leads as l')
            ->selectRaw('u.usertype, l.id as lead_id, u.id as user_id, l.business_id, f.commission')
            ->join('users as u', 'u.id', '=', 'l.user_id')
            ->leftjoin('freelancer as f', 'f.user_id', '=', 'l.user_id')
            ->where('l.id', '=', $request->lead_id)->first();

        //     $lead_working_users = DB::select('SELECT DISTINCT u.id, u.usertype, b.type from users  u
        //     INNER JOIN business b ON b.id = u.business_id
        //     LEFT JOIN leads l ON l.user_id=u.id
        //     where
        //     l.id="' . $request->lead_id . '" OR
        //     ( u.id IN (SELECT asign_to from asign_leads WHERE lead_id="' . $request->lead_id . '" ) OR  u.id IN (SELECT asign_by from asign_leads WHERE lead_id="' . $request->lead_id . '" ))
        // ');

        if ($lead_details->usertype == 'Agent') {
            do {
                $payout_id = Str::random(10);
            } while (DB::table('payout')->where("id", "=", $payout_id)->first());

            $payout['id'] = $payout_id;
            $payout['business_id'] = $request->business_id;
            $payout['property_id'] = $request->unit_id;
            $payout['amount'] = $value;
            $payout['lead_id'] = $request->lead_id;
            $payout['user_id'] = $lead_details->user_id;
            $payout['status'] = 'Request';
            $payout['date'] = date("Y-m-d h:i:s");
            $payout['created_date'] = date("Y-m-d h:i:s");
            $payout['payout_value'] =  ($value / 100) * $lead_details->commission;

            DB::table('payout')->insert($payout);

            DB::statement(DB::raw("SET @MainRegid11='" . $lead_details->user_id . "'"));
            DB::statement(DB::raw("SET @business_id='" . $lead_details->business_id . "'"));

            $agentpayout = DB::select("WITH  recursive Upline AS (
                SELECT @MainRegid11 AS user_id,f.reference_id As Thru,1 AS Lvl, f.user_id AS Leg, sp.name as sponsername, cm.commission, arm.level as sp_level, sp.id as sp_id  FROM freelancer f
                    INNER JOIN users sp ON sp.id=f.reference_id
                    INNER JOIN agent_roles_master arm ON arm.id=f.role_id
                    INNER JOIN freelancer cm ON cm.user_id=sp.id
                WHERE f.user_id COLLATE utf8mb4_general_ci=@MainRegid11 and f.business_id COLLATE utf8mb4_general_ci=@business_id and sp.usertype COLLATE utf8mb4_general_ci='Agent'
                UNION ALL
                SELECT @MainRegid11 AS user_id, c.reference_id AS Thru,b.Lvl+1 AS Lvl,b.Thru, sp.name as sponsername, cm.commission,  arm.level as sp_level, sp.id as sp_id FROM freelancer c
                    INNER JOIN users sp ON sp.id=c.reference_id
                      INNER JOIN agent_roles_master arm ON arm.id=c.role_id
                      INNER JOIN freelancer cm ON cm.user_id=sp.id
                INNER JOIN Upline b ON c.user_id=b.Thru where  c.business_id COLLATE utf8mb4_general_ci= @business_id and sp.usertype COLLATE utf8mb4_general_ci='Agent')
        
                select *  from Upline  order by sp_level DESC");

            for ($p = 0; $p < count($agentpayout); $p++) {
                $i = $p - 1;
                if ($p == 0) {
                    
                    $payoutvalue =   ($value / 100) * ($agentpayout[$p]->commission - $lead_details->commission);
                } else {
                    $payoutvalue = ($value / 100) * ($agentpayout[$p]->commission - $agentpayout[$i]->commission);
                }

                do {
                    $payout_id = Str::random(10);
                } while (DB::table('payout')->where("id", "=", $payout_id)->first());
                $payout['id'] = $payout_id;
                $payout['business_id'] = $request->business_id;
                $payout['property_id'] = $request->unit_id;
                $payout['amount'] = $value;
                $payout['lead_id'] = $request->lead_id;
                $payout['user_id'] = $agentpayout[$p]->sp_id;
                $payout['status'] = 'Request';
                $payout['date'] = date("Y-m-d h:i:s");
                $payout['created_date'] = date("Y-m-d h:i:s");
                $payout['payout_value'] = @$payoutvalue;

                DB::table('payout')->insert($payout);
            }

        }

    }
    public function agentteampayout()
    {

        DB::statement(DB::raw("SET @MainRegid11=123"));
        DB::statement(DB::raw("SET @business_id='Q9CMFZDWW6'"));
        $agentpayout = DB::select("WITH  recursive Upline AS (
        SELECT @MainRegid11 AS user_id,f.reference_id As Thru,1 AS Lvl, f.user_id AS Leg, sp.name as sponsername, cm.commission, arm.level as sp_level, sp.id as sp_id  FROM freelancer f
            INNER JOIN users sp ON sp.id=f.reference_id
            INNER JOIN agent_roles_master arm ON arm.id=f.role_id
            INNER JOIN freelancer cm ON cm.user_id=sp.id
        WHERE f.user_id COLLATE utf8mb4_general_ci=@MainRegid11 and f.business_id COLLATE utf8mb4_general_ci=@business_id and sp.usertype COLLATE utf8mb4_general_ci='Agent'
        UNION ALL
        SELECT @MainRegid11 AS user_id, c.reference_id AS Thru,b.Lvl+1 AS Lvl,b.Thru, sp.name as sponsername, cm.commission,  arm.level as sp_level, sp.id as sp_id FROM freelancer c
            INNER JOIN users sp ON sp.id=c.reference_id
              INNER JOIN agent_roles_master arm ON arm.id=c.role_id
              INNER JOIN freelancer cm ON cm.user_id=sp.id
        INNER JOIN Upline b ON c.user_id=b.Thru where  c.business_id COLLATE utf8mb4_general_ci= @business_id and sp.usertype COLLATE utf8mb4_general_ci='Agent')

        select *  from Upline  order by sp_level DESC");
        return $agentpayout;

    }

    public function project_leads(Request $request)
    {
        if ($request->method == 'pagreement') {
            $data = DB::table('leads as l')->selectRaw('l.id, l.name, l.mobile_no, l.email, bd.date as bookingdate')
                ->join('booking_details as bd', 'bd.lead_id', '=', 'l.id')
                ->where('bd.status', '=', 'Agenement')
                ->get();
            return $data;

        }
        if ($request->method == 'pRegistrations') {
            $data = DB::table('leads as l')->selectRaw('l.id, l.name, l.mobile_no, l.email, bd.date as bookingdate')
                ->join('booking_details as bd', 'bd.lead_id', '=', 'l.id')
                ->where('bd.status', '=', 'Registration')
                ->get();
            return $data;
        }
        if ($request->method == 'enquiries') {
            $data = DB::table('leads as l')->selectRaw('l.id, l.name, l.mobile_no, l.email, l.create_date, l.status')
                ->whereIn('l.status', ['Request for site visit', 'call me later', 'created'])
                ->orderBy('l.create_date', 'DESC')
                ->get();
            return $data;
        }

    }
    public function media(Request $request)
    {

        if ($request->get('method') == 'create') {
            do {
                $media_id = Str::random(10);
            } while (DB::table('payment_history')->where("id", "=", $media_id)->first());
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();
            $picture = $request->get('project_id') . '-' . date('dmYHis') . '.' . $file->getClientOriginalExtension();
            $isupload = $file->storeAs('media', $picture);
            // $image_resize->save(public_path('checklist' .$name));
            if ($isupload) {
                $values['id'] = $media_id;
                $values['media_type'] = $request->get('media_type');
                $values['type'] = $request->get('type');
                $values['media'] = $picture;
                $values['faceing_id'] = $request->get('faceing_id');
                $values['size_id'] = $request->get('size_id');
                $values['title'] = $request->get('title');
                $values['description'] = $request->get('description');
                $values['project_id'] = $request->get('project_id');

                $data = DB::table('media')->insert($values);
                if ($data) {
                    return ["message" => "success"];
                } else {
                    return ["message" => "faild"];
                }
            } else {
                return ["error" => "Something wrng"];
            }
        }
        if ($request->method == 'get') {
            $data = DB::table('media')->where('project_id', '=', $request->project_id)->where('media_type', '=', $request->media_type)->get();

            return $data;
        }

    }
    public function projectview(Request $request)
    {
        if ($request->method == 'view') {
            $data = DB::select("SELECT project_name, json_array(
                JSON_OBJECT(
                 'amenities',  CONCAT('[',(select GROUP_CONCAT(JSON_OBJECT('title', a.title, 'image', a.image)) from prop_amenities pm INNER JOIN master a ON a.id=pm.amenities_id where pm.prop_id=p.id), ']'),
                 'brochers',  CONCAT('[',(select GROUP_CONCAT(JSON_OBJECT('image', b.media)) from media b   where b.project_id=p.id and b.media_type='brocher'), ']'),
                 'imagegallery',  CONCAT('[',(select GROUP_CONCAT(JSON_OBJECT('image', b.media)) from media b   where b.project_id=p.id and b.media_type='imagegallery'), ']'),
                 'plans',  CONCAT('[',(select GROUP_CONCAT(JSON_OBJECT('image', m.media, 'faceing', mp.title)) from media m INNER JOIN master mp ON mp.id=m.faceing_id   where m.project_id=p.id and m.media_type='plans'), ']')
            )) as data from projects p where p.id='" . $request->project_id . "'");
            return $data;
        }

    }
    public function paymentpdf($payment_id)
    {

        $data = DB::select('SELECT b.business_name, b.logo, b.pincode, b.post_office, b.address, s.StateName, d.district_name, ph.amount, ph.type, ph.payment_type, DATE(date) as paymentdate, p.project_name, l.name, ph.id as receiptno from payment_history ph
        INNER JOIN business b ON b.id=ph.business_id
        INNER JOIN states s ON s.state_id=b.state
        INNER JOIN districts d ON d.districts_id=b.district
        INNER JOIN leads l ON l.id=ph.lead_id
        INNER JOIN projects p ON p.id=l.project_id
        where ph.id="' . $payment_id . '"');
        if ($data) {
            $data[0]->inwores = $this->numberToWord(@$data[0]->amount);
            // return $data;
            return view('PDF.testpdf', compact('data'));
            $pdf = Pdf::loadView('PDF.testpdf');
            // $p="a4";
            // $pdf->setOptions(['defaultPaperSize' => $p, 'dpi' => 150,'isRemoteEnabled'=>true]);
            $pdf->setOptions(['isRemoteEnabled' => true]);

            $pdf->getDomPDF()->setHttpContext(
                stream_context_create([
                    'ssl' => [
                        'allow_self_signed' => true,
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ],
                ])
            );
            $customPaper = array(0, 0, 900, 500);
            $pdf->set_paper($customPaper);
            return $pdf->download('invoice.pdf');
        } else {
            return abort(404);
        }

    }

    public function numberToWord($number)
    {

        $no = floor($number);
        $point = round($number - $no, 2) * 100;
        $hundred = null;
        $digits_1 = strlen($no);
        $i = 0;
        $str = array();
        $words = array('0' => '', '1' => 'one', '2' => 'two',
            '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six',
            '7' => 'seven', '8' => 'eight', '9' => 'nine',
            '10' => 'ten', '11' => 'eleven', '12' => 'twelve',
            '13' => 'thirteen', '14' => 'fourteen',
            '15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen',
            '18' => 'eighteen', '19' => 'nineteen', '20' => 'twenty',
            '30' => 'thirty', '40' => 'forty', '50' => 'fifty',
            '60' => 'sixty', '70' => 'seventy',
            '80' => 'eighty', '90' => 'ninety');
        $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
        while ($i < $digits_1) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += ($divider == 10) ? 1 : 2;
            if ($number) {
                $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                $str[] = ($number < 21) ? $words[$number] .
                " " . $digits[$counter] . $plural . " " . $hundred
                :
                $words[floor($number / 10) * 10]
                    . " " . $words[$number % 10] . " "
                    . $digits[$counter] . $plural . " " . $hundred;
            } else {
                $str[] = null;
            }

        }
        $str = array_reverse($str);
        $result = implode('', $str);
        $points = ($point) ?
        "." . $words[$point / 10] . " " .
        $words[$point = $point % 10] : '';
        return $result . "Rupees  " . $points . " Paise";
    }
    public function buyback(Request $request)
    {
        if ($request->method == 'create') {

            do {
                $buyback_id = Str::random(10);
            } while (DB::table('bauyback_master')->where("id", "=", $buyback_id)->first());
            $values['id'] = $buyback_id;
            $values['business_id'] = $request->business_id;
            $values['project_id'] = $request->project_id;
            $values['from_date'] = $request->fromdate;
            $values['to_date'] = $request->todate;
            $values['tenure'] = $request->tenure;
            $values['first_payout_duration'] = $request->first_payout_days;

            $data = DB::table('bauyback_master')->insert($values);

            if ($data) {
                return ["message" => "success"];
            } else {
                return ["message" => "faild"];
            }
        }
        if ($request->method == 'get') {

            $data = DB::table('bauyback_master')->where('business_id', '=', $request->business_id)->where('project_id', '=', $request->project_id)->get();
            return $data;

        }
    }
}
