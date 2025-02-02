<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Kreait\Firebase\Factory;
use App\Models\FirebaseConfig;

class AuthController extends Controller
{
    protected $database;

    public function __construct(FirebaseConfig $firebase)
    {
        $this->database = $firebase->getDatabase();
    }

    public function LoginForm()
    {
        return view('Auth.login');
    }

    public function RegisterForm()
    {
        return view('Auth.register');
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

        return redirect('/')->with('success', 'Registration successful and saved to Firebase!');
    }

    public function Login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $usersRef = $this->database->getReference('users')->getValue();

        if (!$usersRef) {
            return redirect()->back()->withErrors(['email' => 'Email tidak ditemukan']);
        }

        $user = null;
        foreach ($usersRef as $userId => $userData) {
            if (is_array($userData) && isset($userData['email']) && $userData['email'] === $request->email) {
                $user = $userData;
                break;
            }
        }

        if (!$user) {
            return redirect()->back()->withErrors(['email' => 'Email tidak ditemukan']);
        }

        if (!Hash::check($request->password, $user['password'])) {
            return redirect()->back()->withErrors(['password' => 'Password tidak valid']);
        }

        return redirect('/students-index')->with('success', 'Login berhasil');
    }
}
