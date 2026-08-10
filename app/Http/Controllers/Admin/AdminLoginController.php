<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminAuthRequest;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        $credentials['username'] = trim($credentials['username']);
        $credentials['username'] = strtolower($credentials['username']);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return response()->json([
                'status' => 'success',
                'message' => 'Login Berhasil !'
            ]);
        }

        $user = User::whereRaw('LOWER(username) = ?', [$credentials['username']])->first();
        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::login($user);
            $request->session()->regenerate();

            return response()->json([
                'status' => 'success',
                'message' => 'Login Berhasil !'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Login Gagal !'
        ]);
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
