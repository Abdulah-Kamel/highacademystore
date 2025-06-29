<?php

namespace App\Http\Actions;

use App\Models\Money;
use Illuminate\Support\Carbon;

class MoneyAction extends MainAction
{
    public function __construct()
    {
        $this->model = new Money();
    }

    public function updateMoney($data, $request)
    {
        $this->find($request->recordId)->update([
            'is_paid' => $data['is_paid']
        ]);
    }

    public function check($id, $month)
    {
        Money::where('student_id', $id)->where('month', Carbon::now()->locale("ar")->translatedFormat("F"));
    }
}
