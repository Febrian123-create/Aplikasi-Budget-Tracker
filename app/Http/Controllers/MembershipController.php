<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MembershipController extends Controller
{
    public function index()
    {
        $premiumPackage = \DB::table('membership')->where('membership_id', 2)->first();
        return view('membership.index', compact('premiumPackage'));
    }

    public function upgrade(Request $request)
    {
        $user = auth()->user();
        if ($user->membership_id != 2) {
            $user->membership_id = 2;
            $user->save();
        }

        return redirect()->route('membership.index')->with('success', 'Berhasil upgrade ke Premium!');
    }
}
