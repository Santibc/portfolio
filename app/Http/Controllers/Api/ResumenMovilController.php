<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResumenMovilRequest;
use App\Services\ResumenMovilService;
use Illuminate\Http\JsonResponse;

class ResumenMovilController extends Controller
{
    public function __construct(private ResumenMovilService $resumen)
    {
    }

    public function __invoke(ResumenMovilRequest $request): JsonResponse
    {
        return response()->json($this->resumen->generar($request->desde(), $request->hasta()));
    }
}
