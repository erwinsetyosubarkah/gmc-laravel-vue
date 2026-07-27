<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminAuthRequest;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class AdminLoginController extends Controller
{
    public function index() {
        return view('admin/login',[
            'site_profile' => Profile::first()
        ]);
    }

    public function authenticate(AdminAuthRequest $request){
        $credentials = $request->validated();
        $result = [];
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $result = [
                "status"    => "success",
                "message"   => "Login Berhasil !"
            ];
        }else{
            $result = [
                "status"    => "error",
                "message"   => "Login Gagal !"
            ];
        }

        echo json_encode($result);
    }

    public function check()
    {
        if (!Auth::check()) {
            return response()->json([
                'authenticated' => false
            ]);
        }

        return response()->json([
            'authenticated' => true,
            'user' => Auth::user()
        ]);
    }

    public function logout(Request $request){
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();
        $result = [
            'status'    => 'success',
            'message'   => "Berhasil Logout !"
        ];
        return response()->json($result);
    }
}
