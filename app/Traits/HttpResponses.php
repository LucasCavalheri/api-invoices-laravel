<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\MessageBag;
use Illuminate\Http\Resources\Json\JsonResource;

trait HttpResponses
{
    public function response(string $message, string|int $status, array|Model|JsonResource $data = [])
    {
        return response()->json([
            'message' => $message,
            'status' => $status,
            'data' => $data,
        ], $status);
    }

    public function error(string $message, string|int $status, array|MessageBag $errors = [], array $data = [])
    {
        return response()->json([
            'message' => $message,
            'status' => $status,
            'errors' => $errors,
            'data' => $data,
        ], $status);
    }
}
