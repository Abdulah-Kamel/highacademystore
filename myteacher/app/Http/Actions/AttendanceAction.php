<?php

namespace App\Http\Actions;

use App\Models\Attendance;
use App\Models\Student;

class AttendanceAction extends MainAction
{
    public function __construct()
    {
        $this->model = new Attendance();
    }

    public function storeAttendance($data, $request)
    {
        if (in_array(null, $request->studentsAttendance_id)) {
            Attendance::whereIn('id', $request->studentsAttendance_id)->delete();
            foreach ($data['students_id'] as $key => $student_id) {
                Attendance::create([
                    'student_id' => $student_id,
                    'status' => $data['status'][$key],
                    'session_id' => $data['session_id']
                ]);
            }
        } else {
            foreach ($request->studentsAttendance_id as $key => $item) {
                Attendance::findorfail($item)->update([
                    'student_id' => $data['students_id'][$key],
                    'status' => $data['status'][$key],
                    'session_id' => $data['session_id']
                ]);
            }

        }

    }


}
