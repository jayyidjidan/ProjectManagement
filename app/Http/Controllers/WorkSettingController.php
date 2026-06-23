<?php

namespace App\Http\Controllers;

use App\Models\WorkSetting;
use Illuminate\Http\Request;

class WorkSettingController extends Controller
{
    public function index()
    {
        $setting =
            WorkSetting::first();

        if(!$setting)
        {
            $setting =
                WorkSetting::create([

                    'checkin_start' =>
                        '07:00:00',

                    'checkin_end' =>
                        '09:00:00',

                    'checkout_start' =>
                        '16:00:00',

                    'checkout_end' =>
                        '20:00:00'

                ]);
        }

        return view(
            'work-settings.index',
            compact('setting')
        );
    }

    public function update(
        Request $request
    )
    {
        $request->validate([

            'checkin_start' =>
                'required',

            'checkin_end' =>
                'required',

            'checkout_start' =>
                'required',

            'checkout_end' =>
                'required',

        ]);

        $setting =
            WorkSetting::first();

        $setting->update([

            'checkin_start' =>
                $request->checkin_start,

            'checkin_end' =>
                $request->checkin_end,

            'checkout_start' =>
                $request->checkout_start,

            'checkout_end' =>
                $request->checkout_end

        ]);

        return redirect()
            ->route(
                'work-settings.index'
            )
            ->with(
                'success',
                'Work hours updated successfully.'
            );
    }
}