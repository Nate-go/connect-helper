<?php

namespace App\Services\ModelServices;

class BaseService {

    protected function midleware($midlewares, $action) {
        foreach ($midlewares as $midleware) {
            $result = $midleware();
            if($result) {
                return $result;
            }
        }
        return $action();
    }

    protected function author($roles) {
        return function () use ($roles) {
            if(! in_array(auth()->user()->role, $roles)) {
                return response()->json(["error" => "Unauthorized", "message" => "You do not have permission to access"]);
            }
            return null;
        };
    }
}