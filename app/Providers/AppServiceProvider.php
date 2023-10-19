<?php

namespace App\Providers;

use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
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
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Request $request)
    {
        if ($request->mailby == "email") {
            $data = DB::table('smtpservers')
                ->select('smtpservers.*')
                ->join('users', 'users.business_id', '=', 'smtpservers.business_id')
                ->where('users.email', '=', $request->email)->first();
            if ($data) {
                $smtp = [
                    "driver" => "smtp",
                    "host" => $data->host,
                    "port" => $data->port,
                    "encryption" => $data->encryption,
                    "username" => $data->from,
                    "password" => $data->password,
                    "from" => [
                        "address" => $data->from,
                        "name" => "Holaciti",
                    ],
                ];
                Config::set('mail', $smtp);
            }
        }
        if ($request->mailby == "business") {
            $data = DB::table('smtpservers')
                ->where('business_id', '=', $request->business_id)->first();
            if ($data) {
                $smtp = [
                    "driver" => "smtp",
                    "host" => $data->host,
                    "port" => $data->port,
                    "encryption" => $data->encryption,
                    "username" => $data->from,
                    "password" => $data->password,
                    "from" => [
                        "address" => $data->from,
                        "name" => "Holaciti",
                    ],
                ];
                Config::set('mail', $smtp);
            }
        }

    }
}
