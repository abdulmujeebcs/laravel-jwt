<?php

namespace App\Providers;

use Illuminate\Http\Response as HttpResponses;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response as ResponseFacade;

class ResponseMacroServiceProvider extends ServiceProvider
{
    public function boot()
    {
        ResponseFacade::macro('success', function ($message, $data, $status = HttpResponses::HTTP_OK) {
            return response()->json([
                'success' => true,
                'status' => $status,
                'data' => $data,
                'message' => $message,
            ], $status);
        });

        ResponseFacade::macro('error', function ($message, $status = HttpResponses::HTTP_INTERNAL_SERVER_ERROR) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'status' => $status
            ], $status);
        });
    }
}
