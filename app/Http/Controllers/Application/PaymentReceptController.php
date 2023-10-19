<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentReceptController extends Controller
{
    public function PaymentRecept($id){
        return view('Invoice-recepts.payment-recept',  compact('id')); 
    }
    public function email(){
        return view('emails.email'); 
    }
}
