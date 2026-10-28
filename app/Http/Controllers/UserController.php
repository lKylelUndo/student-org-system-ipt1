<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;


class UserController extends Controller
{
    public function __construct(private UserService $student) {}

    public function testapi() {
        return $this->student->test();
    }


}
