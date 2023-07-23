<?php

namespace App\Http\Controllers;

use App\Http\Traits\GeneraleTrait;
use App\Models\User;
use Illuminate\Http\Request;

class ContactController extends Controller
{

    use GeneraleTrait;
    public function index()
    {
        return $this->returnData("data", User::all(), 200, "Contacts Selected Successfully !");
    }
}
