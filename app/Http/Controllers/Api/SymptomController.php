<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SymptomResource;
use App\Models\Symptoms;
use App\Traits\ApiTrait;
use Illuminate\Http\Request;

class SymptomController extends Controller
{
    use ApiTrait;
    public function index()
    {
        $symptoms = Symptoms::varicella()->get();
        return $this->okResponse(SymptomResource::collection($symptoms) , 'مرض الجديري');
    }
}
