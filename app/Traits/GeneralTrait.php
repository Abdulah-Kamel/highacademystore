<?php

namespace App\Traits;

use Exception;
use Illuminate\Validation\ValidationException;

trait GeneralTrait
{

    public function SuccessJsonData() {
        return response()->json([
            'message' => 'Data inserted successfully',
            'code' => 200,
        ]);
    }

    public function ErrorJsondata() {
        return response()->json([
            'message' => 'Internal Server Error',
            'code' => 500,
        ]);
    }

    public function ErrorJsonValidation($e) {
        return response()->json([
            'message' => 'Validation Error',
            'errors' => $e->errors(),
            'code' => 422,
        ]);
    }

    public function JsonData($model) {
        if($model) {
            return response()->json([
                'message' => 'Data inserted successfully',
                'code' => 200,
            ]);
        } else {
            return response()->json([
                'message' => 'internal server error',
                'code' => 500,
            ]);
        }
    }
}
