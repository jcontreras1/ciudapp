<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\UserPreference;
use Illuminate\Http\Request;

class ApiPreferenceController extends Controller
{
    public function toggle(Request $request){
        $request->validate([
            'preference_id' => 'required|exists:preference,id'
        ]); 
        $user = auth()->user();
        $preference = UserPreference::where('user_id',$user->id)->where('preference_id',$request->preference_id)->first();
        if($preference){
            $preference->update([
                'value' => !$preference->value
            ]);
        }else{
            UserPreference::create([
                'preference_id' => $request->preference_id,
                'user_id' => $user->id,
                'value' => false,
            ]);
        }
        return response(
            $user->preferences
            ,200);
    }
}
