<?php

namespace App\Http\Controllers\Api;

use App\Constants\AuthenConstant\StatusResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\SendMailFormRequests\StoreSendMailFormRequest;
use App\Services\ModelServices\SendMailService;

class SendMailController extends Controller
{
    protected $sendMailService;

    public function __construct(SendMailService $sendMailService) {
        $this->sendMailService = $sendMailService;
    }

    public function store(StoreSendMailFormRequest $request)
    {
        $result = $this->sendMailService->store($request->all());
        return response()->json([
            'message' => $result ? 'Send mail successfull' : 'Send mail fail',
        ], $result ? StatusResponse::SUCCESS : StatusResponse::ERROR);
    }
}
