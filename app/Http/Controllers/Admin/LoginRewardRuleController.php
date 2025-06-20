<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\LoginRewardRule;
use App\Http\Controllers\Controller;

class LoginRewardRuleController extends Controller
{
    //

    public function index()
    {
        $data = LoginRewardRule::all();
        return view('admin.loginrewardrule.index' , compact('data'));
    }

    public function create()
    {
        return view('admin.loginrewardrule.create');
    }

    public function store(Request $request)
    {

        $request->validate([
            'day' => 'required|string|max:255',
            'points' => 'required|integer|min:0',
        ]);



        LoginRewardRule::create([
            'day' => $request->day,
            'points' => $request->points,
        ])->save();
        

        return redirect()->route('login-reward-rules.index')->with('success', 'Login reward rule created successfully.');
    }

    public function edit($id)
    {
        $data = LoginRewardRule::findOrFail($id);
        return view('admin.loginrewardrule.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'day' => 'required|string|max:255',
            'points' => 'required|integer|min:0',
        ]);

        $data = LoginRewardRule::findOrFail($id);
        $data->day = $request->day;
        $data->points = $request->points;
        $data->save();

        return redirect()->route('login-reward-rules.index')->with('success', 'Login reward rule updated successfully.');
    }


    public function destroy($id)
    {
        $data = LoginRewardRule::findOrFail($id);
        $data->delete();

        return redirect()->route('login-reward-rules.index')->with('success', 'Login reward rule deleted successfully.');
    }
}
