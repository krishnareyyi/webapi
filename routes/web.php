<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Application\SettingsController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('emails/forgot-password');
});
Route::get('/payment', function () {
    return view('emails/PropertyPaymentConfirmation');
});
Route::get('/testpdf', function () {
    return view('PDF/testpdf');
});
// Route::get('/test', function () {
//     return view('PDF/test');
// });
Route::get("test",[SettingsController::class,'pdftest']);
Route::get('/images/{filename}', function ($filename)
{
    // return Image::make(storage_path('public/' . $filename))->response();
     $path = base_path('images/'. $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
});