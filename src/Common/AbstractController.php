<?php

namespace NextDeveloper\Generator\Common;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use NextDeveloper\Generator\Http\Traits\Responsable;

class AbstractController extends Controller
{
    //  NextDeveloper Generator Traits
    use Responsable;

    //  Laravel Traits
    use DispatchesJobs, ValidatesRequests;
}