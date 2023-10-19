<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MailConfigProvider extends ServiceProvider
{
    public $request;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Request $request)
    {
       $data = DB::table('smtpservers')->where('business_id', '=', $request->business_id)->first();

        if($data ){
            $smtp =[
                "driver"=>"smtp",
                "host"=>$data->host,
                "port"=>$data->port,
                "encryption"=>$data->encryption, 
                "username"=>$data->from, 
                "password"=>$data->password,
                "from"=>[
                    "address"=>$data->from,
                    "name"=>"Holaciti"
                ]
            ];
            Config::set('mail', $smtp);
        }
    }
}
