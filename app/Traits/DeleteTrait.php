<?php

namespace App\Traits;

trait DeleteTrait
{
    public function Delete($request,$model) {
        $model->delete();
        if($model) {
            return response()->json([
                'message' => 'Data Deleted successfully',
                'code' => 200,
                'id' => $request,
            ]);
        } else {
            return response()->json([
                'message' => 'internal server error',
                'code' => 500,
            ]);
        }
    }
}
