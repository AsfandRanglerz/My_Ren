<?php

namespace App\Http\Controllers;

use App\Models\LoginRule;
use Illuminate\Http\Request;

class LoginRuleController extends Controller
{
    //

     public function index() {
        // yah validate method  khbi tang krta ias liye agr post na hou data tou validate comment kr lai
        $loginRules = LoginRule::all();

        return view('admin.loginRule.index', compact('loginRules'));
     }

        public function create() {
            return view('admin.loginRule.create');
        }

        public function store(Request $request) {
            $request->validate([
                'Consecutive_days' => 'required|unique:login_rules,Consecutive-days',
                'points' => 'required|unique:login_rules,points',
            ]);

            LoginRule::create([
                'Consecutive_days' => $request->Consecutive_days,
                'points' => $request->points,
            ]);

            return redirect()->route('login-rules.index')->with('success', 'Login rule created successfully.');
        }

        
}
