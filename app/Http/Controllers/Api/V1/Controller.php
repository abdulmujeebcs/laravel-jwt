<?php

namespace App\Http\Controllers\Api\V1;

use OpenApi\Attributes as OA;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

#[OA\Info(title: "Lara JWT", version: "1.0.0")]
#[OA\SecurityScheme(securityScheme: "bearerAuth", type: "http", scheme: "bearer")]

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
