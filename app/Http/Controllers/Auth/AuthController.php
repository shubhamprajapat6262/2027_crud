<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Models\User;
use Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AuthController extends Controller
{
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function index(): View
    {
        return view('auth.login');
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function registration(): View
    {
        return view('auth.registration');
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function postLogin(Request $request): RedirectResponse
    {
        // Validate input
        $validated = $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|min:6|max:128',
        ], [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'Please enter your password.',
            'password.min' => 'Password must be at least 8 characters.',
        ]);

        // Login credentials
        $credentials = [
            'email' => $validated['email'],
            'password' => $validated['password'],
        ];

            // Check authentication
    if (Auth::attempt($credentials)) {

        // Regenerate session for security
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'))
            ->with('success', 'Welcome back! You have successfully logged in.');
    }

    // Invalid credentials
    return back()
        ->withInput($request->only('email'))
        ->withErrors([
            'email' => 'Invalid email or password. Please try again.',
        ]);

    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function postRegistration(Request $request): RedirectResponse
    {
        // VALIDATION RULES
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:100',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ],[
            'name.required' => 'Please enter your full name.',
            'name.min' => 'Name must be at least 3 characters.',
            'name.max' => 'Name cannot exceed 100 characters.',

            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered.',

            'password.required' => 'Please enter your password.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        // $data = $request->all();
        // $user = $this->create($data);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password'])
        ]);

        // LOGIN USER
        Auth::login($user);
        // REGENERATED SESSION
        $request->session()->regenerate();

        return redirect()->route('dashboard')
                ->with('success', 'Congratulations! Your account has been created successfully.');
        // return redirect("dashboard")->withSuccess('Great! You have Successfully loggedin');
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function dashboard()
    {
        if(Auth::check()){
            return view('dashboard');
        }

        return redirect("login")->withSuccess('Opps! You do not have access');
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function create(array $data)
    {
      return User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password'])
      ]);
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function logout(): RedirectResponse
    {
        Session::flush();
        Auth::logout();

        return Redirect('login');
    }
}
