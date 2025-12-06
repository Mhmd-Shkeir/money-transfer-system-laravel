<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AgentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;



class UserController extends Controller
{
    public function showSignupForm()
    {
        return view('auth.signup');
    }

        public function showLoginForm()
    {
        return view('auth.login');
    }

 
      public function signup(Request $request)
    {
           $validator = Validator::make($request->all(), [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => [
            'required',
            'string',
            'min:8',             
            'max:30',            
            'confirmed',         
            'regex:/[a-z]/',     
            'regex:/[A-Z]/',     
            'regex:/[0-9]/',     
            'regex:/[@$!%*#?&_-]/' 
        ],
        'phone' => 'required|string|max:20',
        'country' => 'required|string|max:100',
        'address' => 'required|string|max:255',
        'date_of_birth' => 'required|date|before:today',
        'national_id' => 'required|string|max:50|unique:users',
        'role' => 'required|string|in:user,agent',
        'store_name' => 'required_if:role,agent|nullable|string|max:255'
    ], [
        'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number and one special character.',
        'password.min' => 'Password must be at least 8 characters.',
        'password.max' => 'Password cannot be more than 30 characters.',
        'password.confirmed' => 'Password confirmation does not match.',
        'role.required' => 'Please select an account type.',
        'role.in' => 'The selected account type is invalid.',
        'store_name.required_if' => 'Store name is required for agent registration.'
    ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password_hash' => Hash::make($request->password),
            'phone' => $request->phone,
            'country' => $request->country,
            'address' => $request->address,
            'date_of_birth' => $request->date_of_birth,
            'national_id' => $request->national_id,
            'role' => $request->role,
            'status' => $request->role === 'agent' ? 'suspended' : 'active', 
            'is_email_verified' => false,
            'is_phone_verified' => true,
            'verification_token' => Str::random(64),
            'token_expires_at' => now()->addHours(2)
        ]);
   app(\App\Http\Controllers\NotificationController::class)->notifyAdmins(
    'new_user',
    'New User Registered',
    $user->first_name . ' ' . $user->last_name . ' has signed up.',
    null
);


        if ($request->role === 'agent') {
            AgentProfile::create([
                'user_id' => $user->id,
                'store_name' => $request->store_name,
                'address' => $request->address,
                'is_approved' => 0, 
                'commission_rate' => 2.5, 
            ]);
            app(\App\Http\Controllers\NotificationController::class)->notifyAdmins(
    'agent_application',
    'New Agent Application',
    $user->first_name . ' ' . $user->last_name . ' applied to become an agent.',
    null
);


        }

        Mail::send('emails.verify-email', ['user' => $user], function($message) use ($user) {
            $message->to($user->email);
            $message->subject('Verify Your Email Address');
        });

        return redirect()->route('login')
            ->with('success', 'Registration successful! Please check your email to verify your account.');
    }


     public function verifyEmail($token)
    {
        $user = User::where('verification_token', $token)
                    ->where('token_expires_at', '>', now())
                    ->first();

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Invalid or expired verification link.');
        }

        $user->update([
            'is_email_verified' => true,
            'verification_token' => null,
            'token_expires_at' => null
        ]);

        return redirect()->route('login')
            ->with('success', 'Email verified successfully! You can now login.');
    }

       public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password_hash)) {
            return redirect()->back()
                ->withErrors(['email' => 'Invalid credentials.'])
                ->withInput($request->only('email'));
        }

        if (! $user->is_email_verified) {
            return redirect()->back()
                ->withErrors(['email' => 'Please verify your email before logging in.'])
                ->withInput($request->only('email'));
        }

        $user->update([
        'last_login' => now()
        ]);
        
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'agent':
                return redirect()->route('agent.dashboard');
            case 'user':
            default:
                return redirect()->route('user.dashboard');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}