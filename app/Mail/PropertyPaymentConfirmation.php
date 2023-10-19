<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PropertyPaymentConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $emaildata;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($emaildata)
    {
        $this->emaildata = $emaildata;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(Request $request)
    {
        $data = DB::table('emailtemplates')->where('business_id', '=', $request->business_id)->first();
        $businessdetails  = DB::table('business as b')->selectRaw('b.id, b.business_name, b.logo, b.pincode, b.post_office, s.StateName ')
        ->join('states as s', 's.state_id', '=', 'b.state')
        ->where('id', '=',  $request->business_id)->first();

       
        $personalinfo = DB::select('SELECT p.unit_no, blf.title as line, pbt.title as block, pj.project_name, l.name as cname from prpty_units p INNER JOIN block_line_flores blf ON blf.id=p.line_id INNER JOIN prpty_block_towers pbt ON pbt.id=p.block_id INNER JOIN projects pj ON pj.id=pbt.prop_id LEFT JOIN leads l ON l.id=p.lead_id where p.id="'.$request->unit_id.'"');

        return $this->subject('Email verification')
        ->view('emails.PropertyPaymentConfirmation',  compact('businessdetails', 'personalinfo', 'request'));
    }
}
