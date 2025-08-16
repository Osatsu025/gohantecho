<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as RoutingController;

abstract class Controller extends RoutingController
{
    use AuthorizesRequests, ValidatesRequests;
}
