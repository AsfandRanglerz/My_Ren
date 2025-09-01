<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SignupRewardSettingController extends Controller
{
    //

    public function index()
    {
        $setting = \App\Models\SignupRewardSetting::all();
        return view('admin.signuprewardsettings.index', compact('setting'));
    }



    public function edit($id)
    {
        $setting = \App\Models\SignupRewardSetting::findOrFail($id);
        return view('admin.signuprewardsettings.edit', compact('setting'));
    }       

    public function update(Request $request, $id)       
    {
        $request->validate([
            'points' => 'required|integer|min:0',
        ]);

        $setting = \App\Models\SignupRewardSetting::findOrFail($id);
        $setting->points = $request->points;
        $setting->save();

        return redirect()->route('signup_reward_setting.index')->with('success', 'Signup reward points updated successfully.');
    }
}
