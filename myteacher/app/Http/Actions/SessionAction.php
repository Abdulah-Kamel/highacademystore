<?php

namespace App\Http\Actions;

use App\Models\Session;
use App\Models\SessionDays;

class SessionAction extends MainAction
{
    public function __construct()
    {
        $this->model = new Session();
    }

    public function storeSession($data, $days)
    {
        $session = $this->store($data);
        foreach ($days as $day) {
            SessionDays::create([
                'session_id' => $session->id,
                'day' => $day
            ]);
        }
    }

    public function updateSession($id, $data, $days)
    {
        $this->find($id)->update($data);
        SessionDays::where('session_id', $id)->delete();
        foreach ($days as $day) {
            SessionDays::create([
                'session_id' => $id,
                'day' => $day
            ]);
        }
    }

    public function getDays($id)
    {
        return SessionDays::where('session_id', $id)->pluck('day')->ToArray();
    }

    public function getSessions($id)
    {
        return Session::where('grade_id', $id)->get();
    }

}
