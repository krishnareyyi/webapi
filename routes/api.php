<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Application\AccountsController;
use App\Http\Controllers\Application\ProjectController;
use App\Http\Controllers\Application\EmployeeController;
use App\Http\Controllers\Application\LeadsController;
use App\Http\Controllers\PincodeController;
use App\Http\Controllers\Application\DashboardController;
use App\Http\Controllers\Application\SettingsController;
use App\Http\Controllers\Application\MasterController;
use App\Http\Controllers\Application\AppointmentsController;
use App\Http\Controllers\Application\PaymentReceptController;
use App\Http\Controllers\Application\ReportsController;



use App\Http\Controllers\SendMail;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::group(['middleware' => 'auth:sanctum'], function(){ 

    
    Route::post("employee/list",[EmployeeController::class,'employee']);
    Route::post("employee/freelancer",[EmployeeController::class,'freelancer']);
    Route::post("employee/link_permissions",[EmployeeController::class,'link_permissions']);
    Route::post("employee/agent",[EmployeeController::class,'agent']);

    Route::post("dashboard",[DashboardController::class,'dashboard']);
    Route::post("dashboard/salesgrap",[DashboardController::class,'salesgrap']);

    Route::post("projects/list",[ProjectController::class,'project']);
    Route::post("projects/configuration",[ProjectController::class,'configuration']);
    Route::post("projects/amenities",[ProjectController::class,'amenities']);
    Route::post("projects/blocktowers",[ProjectController::class,'blocktowers']);
    Route::post("projects/layout",[ProjectController::class,'layout']); 
    Route::post("projects/size_conf",[ProjectController::class,'size_conf']);
    Route::post("projects/line_units",[ProjectController::class,'line_units']);
    Route::post("projects/payments",[ProjectController::class,'payments']);
    Route::post("projects/project_leads",[ProjectController::class,'project_leads']);
    Route::post("projects/media",[ProjectController::class,'media']);
    Route::post("projects/prptypaumentmode",[ProjectController::class,'prptypaumentmode']);
    Route::post("projects/paymentconfig",[ProjectController::class,'paymentconfig']);
    Route::post("projects/buyback",[ProjectController::class,'buyback']);
    

    Route::post("leads/list",[LeadsController::class,'leads']);
    Route::post("leads/appointment",[LeadsController::class,'appointment']);
    Route::post("leads/lead_unit_details",[LeadsController::class,'lead_unit_details']);
    Route::post("leads/search",[LeadsController::class,'search']);
    Route::post("leads/asignleads",[LeadsController::class,'asignleads']);
    Route::post("leads/remainders",[LeadsController::class,'remainders']);

    
    Route::post("apointments",[AppointmentsController::class,'apointments']);

    Route::post("accounts/ledgerbook", [AccountsController::class, 'ledgerbook']); 
    Route::post("accounts/expensive", [AccountsController::class, 'expensive']); 
    Route::post("accounts/payout", [AccountsController::class, 'payout']); 
    Route::post("accounts/profile", [AccountsController::class, 'profile']); 
    Route::post("accounts/changepassword", [AccountsController::class, 'changepassword']);
    Route::post("accounts/checkcode", [AccountsController::class, 'checkcode']);
    Route::post("accounts/userlist", [AccountsController::class, 'userlist']);
    

    Route::post("settings/payout", [SettingsController::class, 'payout']);
    Route::post("settings/smtp", [SettingsController::class, 'smtp']);
    Route::post("settings/securitypassword", [SettingsController::class, 'securitypassword']);

    Route::post("master/week", [MasterController::class, 'week']);
    Route::post("master/slot", [MasterController::class, 'slot']);
    Route::post("master/userlinks", [MasterController::class, 'userlinks']);


    Route::post("reports/paymentreport", [ReportsController::class, 'paymentreport']);
    Route::post("reports/customerreport", [ReportsController::class, 'customerreport']);
  
});
Route::get("employee/test",[EmployeeController::class,'test']);
Route::get("project/agentteampayout",[ProjectController::class,'agentteampayout']);
Route::get("master/ifscode/{ifsccode}",[MasterController::class,'ifscode']);

Route::get("pdf/PaymentRecept/{id}",[PaymentReceptController::class,'PaymentRecept']);
Route::get("pdf/email",[PaymentReceptController::class,'email']);
Route::get("projects/proppaymentreceipt/{id}",[ProjectController::class,'paymentpdf']);

Route::get("projects/agentteampayout",[ProjectController::class,'agentteampayout']);

Route::post("projects/projectview",[ProjectController::class,'projectview']);
Route::post("accounts/register", [AccountsController::class, 'register']);
Route::post("accounts/forgotpassword", [AccountsController::class, 'forgotpassword']); 
Route::post("accounts/login", [AccountsController::class, 'login']); 
Route::post("pincode", [PincodeController::class, 'pincode']);
Route::post("accounts/checkuser", [AccountsController::class, 'checkuser']);

Route::post("accounts/business_setup", [AccountsController::class, 'business_setup']); 
Route::get('/storage/{folder}/{filename}', function ($folder,$filename)
{
    // return Image::make(storage_path('public/' . $filename))->response();
     $path = storage_path('app/' .$folder.'/'. $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
});
