<?php

namespace App\Http\Controllers;


//use Illuminate\Foundation\Auth\User;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function create()
    {
        return view ('user.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed']
        ]);

        $user = User::create($request->all());
        event(new Registered($user));
        Auth::login($user);

        //return redirect()->route('login')->with('success', 'Successfully registration');
        return redirect()->route('verification.notice');


        // dd($request->all());
    }

    public function login()
    {
        return view ('user.login');
    }

    public function loginAuth(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard')->with('success', 'Welcome, ' . Auth::user()->name . '!');
        }

        return back()->withErrors([
            'email' => 'Wrong login or password',
        ]);

//        dump($request->boolean('remember'));
//        dd($request->all());
    }

    public function logout()
    {
        Auth::logout();                         // разлогинили пользователя
        return redirect()->route('login');
    }

    public function dashboard()
    {
        return view ('user.dashboard');
    }
}
