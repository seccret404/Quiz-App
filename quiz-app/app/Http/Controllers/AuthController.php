<?php

namespace App\Http\Controllers;

use App\Models\Firebase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected $database;

    public function __construct(Firebase $firebase)
    {
        $this->database = $firebase->getDatabase();
    }

    public function LoginForm()
    {
        return view('pages.auth.login');
    }

    public function RegisterForm()
    {
        return view('pages.auth.register');
    }

    public function Register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string',
        ]);

        $userData = [
            'id'          => uniqid(),
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'profil_pic'  => '',
            'role'        => 'user',
            'created_at'  => now()->toDateTimeString()
        ];

        $this->database->getReference('users/' . $userData['id'])->set($userData);

        return redirect('/dashboard/admin')->with('success', 'Registration successful and saved to Firebase!'); //here.....
    }

    public function Login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $usersRef = $this->database->getReference('users')->getValue();
        // dd($usersRef);

        if (!$usersRef) {
            return redirect()->back()->withErrors(['email' => 'Email tidak ditemukan']);
        }

        $user = null;
        foreach ($usersRef as $userId => $userData) {
            if (is_array($userData) && isset($userData['email']) && $userData['email'] === $request->email) {
                $user = $userData;
                $user['id'] = $userId;
                break;
            }
        }

        if (!$user) {
            return redirect()->back()->withErrors(['email' => 'Email tidak ditemukan']);
        }

        if (!isset($user['password']) || empty($user['password'])) {
            return redirect()->back()->withErrors(['password' => 'Password tidak ditemukan di database']);
        }

        if (!Hash::check($request->password, $user['password'])) {
            return redirect()->back()->withErrors(['password' => 'Password tidak valid']);
        }

        session(['user' => $user]);

        if (isset($user['role']) && $user['role'] === 'admin') {
            return redirect('/dashboard/admin')->with('success', 'Login berhasil sebagai Admin');
        } elseif (isset($user['role']) && $user['role'] === 'user') {
            return redirect('/student/home')->with('success', 'Login berhasil sebagai User');
        }

        return redirect('/login')->withErrors(['error' => 'Role tidak valid']);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
