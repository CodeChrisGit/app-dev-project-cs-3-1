<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\PropertyType;

class LookupController extends Controller
{
    public function locations()
    {
        return Location::orderBy('city')->get(['id', 'city', 'province']);
    }

    public function types()
    {
        return PropertyType::orderBy('type_name')->get(['id', 'type_name as name']);
    }
}
