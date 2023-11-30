<?php

namespace App\Http\Controllers\Api;

use App\Constants\AuthenConstant\StatusResponse;
use App\Http\Controllers\Controller;
use App\Services\ModelServices\EnterpriseService;
use Illuminate\Http\Request;

class EnterpriseController extends Controller
{
    protected $enterpriseService;

    public function __construct(EnterpriseService $enterpriseService) {
        $this->enterpriseService = $enterpriseService;
    }

    public function index(Request $request) {
        $data = $this->enterpriseService->get($request->all());
        return response()->json([
            'data' => $data
        ], StatusResponse::SUCCESS);
    }
}
