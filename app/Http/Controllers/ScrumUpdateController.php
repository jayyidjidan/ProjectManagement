<?php

namespace App\Http\Controllers;

use App\Models\Members;
use App\Models\Attendance;
use App\Models\Scrum;
use App\Models\ScrumUpdate;
use Illuminate\Http\Request;

class ScrumUpdateController extends Controller
{
    public function saveScrum(
        Request $request,
        Scrum $scrum
    )
    {

        if ($scrum->is_locked) {

            return back()->with(
                'error',
                'Scrum already finalized.'
            );
        }

        foreach (
            $request->members
            as $memberId => $data
        ) {

            $update =
                ScrumUpdate::updateOrCreate(

                    [
                        'id_scrum' =>
                            $scrum->id_scrum,

                        'id_member' =>
                            $memberId
                    ],

                    [
                        'id_task_1' =>
                            $data['id_task_1']
                            ?? null,

                        'target_1' =>
                            $data['target_1']
                            ?? null,

                        'id_task_2' =>
                            $data['id_task_2']
                            ?? null,

                        'target_2' =>
                            $data['target_2']
                            ?? null,
                    ]
                );

            $scrum->members()
                ->syncWithoutDetaching([
                    $memberId => [
                        'id_status' =>
                            $data['id_status']
                    ]
                ]);

            Attendance::firstOrCreate(

                [
                    'id_member' => $memberId,
                    'tanggal' => $scrum->date_scrum
                ],

                [
                    'id_scrum' => $scrum->id_scrum,
                    'id_status' => $data['id_status'],
                    'start_hour' => null,
                    'leave_hour' => null
                ]

            );

            $this->updateStatistics(
                $memberId,
                $data['id_status']
            );
        }
        
        $scrum->update(['is_locked' => true]);
        
        return redirect()
            ->route(
                'scrums.show',
                $scrum
            )
            ->with(
                'success',
                'Scrum saved successfully'
            );
    }

    private function updateStatistics(
        $memberId,
        $statusId
    )
    {
        $member =
            Members::find(
                $memberId
            );

        switch ($statusId) {

            case 4:

                $member->increment(
                    'total_WFH'
                );

                break;

            case 7:

                $member->increment(
                    'total_cuti'
                );

                break;

            case 8:

                $member->point =
                    $member->point + 1.5;

                $member->save();

                break;
        }
    }
}