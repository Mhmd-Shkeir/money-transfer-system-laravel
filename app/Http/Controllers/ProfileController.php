<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Country;
use App\Models\Currency;
use App\Models\BusinessHours;

class ProfileController extends Controller
{
  
    public function show()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }

   
    public function adminShow()
    {
        $user = Auth::user();
        return view('admin.profile', compact('user'));
    }

   
    public function agentShow()
    {
        $user = Auth::user();
        $agentProfile = $user->agentProfile;
        
        if (!$agentProfile) {
            return redirect()->route('agent.profile.create')->with('info', 'Please create your agent profile first.');
        }
        
        $countries = Country::orderBy('name')->get();
        $currencies = Currency::orderBy('name')->get();
        $businessHours = BusinessHours::where('agent_profile_id', $agentProfile->id)
            ->orderBy('day_of_week')
            ->get();
        
        $timezones = $this->getTimezonesList();

        return view('agent.profile', compact('user', 'agentProfile', 'countries', 'currencies', 'businessHours', 'timezones'));
    }

  
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'password' => 'nullable|min:8|confirmed',
        ]);

        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? $user->phone;
        $user->address = $validated['address'] ?? $user->address;

        if ($validated['password']) {
            $user->password_hash = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

   
    public function updateAgentProfile(Request $request)
    {
        $user = Auth::user();

        if (!$user->agentProfile) {
            return redirect()->back()->with('error', 'Agent profile not found!');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'business_name' => 'required|string|max:255',
            'business_registration_number' => 'required|string|max:255|unique:agent_profiles,business_registration_number,' . $user->agentProfile->id,
            'tax_id' => 'nullable|string|max:255',
            'business_description' => 'nullable|string|max:1000',
            'office_address' => 'nullable|string|max:500',
            'country_id' => 'required|exists:countries,id',
            'currency_id' => 'required|exists:currencies,id',
            'timezone' => 'required|string',
            'business_phone' => 'nullable|string|max:20',
            'business_email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'service_description' => 'nullable|string|max:1000',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? $user->phone;
        $user->save();

        $agentProfile = $user->agentProfile;
        $agentProfile->business_name = $validated['business_name'];
        $agentProfile->business_registration_number = $validated['business_registration_number'];
        $agentProfile->tax_id = $validated['tax_id'];
        $agentProfile->business_description = $validated['business_description'];
        $agentProfile->office_address = $validated['office_address'];
        $agentProfile->country_id = $validated['country_id'];
        $agentProfile->currency_id = $validated['currency_id'];
        $agentProfile->timezone = $validated['timezone'];
        $agentProfile->business_phone = $validated['business_phone'];
        $agentProfile->business_email = $validated['business_email'];
        $agentProfile->website = $validated['website'];
        $agentProfile->service_description = $validated['service_description'];
        $agentProfile->latitude = $validated['latitude'];
        $agentProfile->longitude = $validated['longitude'];
        $agentProfile->save();

        $this->updateBusinessHours($request, $agentProfile->id);

        return redirect()->back()->with('success', 'Agent profile and business information updated successfully!');
    }

   
    private function updateBusinessHours(Request $request, $agentProfileId)
    {
        $days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

        foreach ($days as $index => $day) {
            $isClosed = $request->input("closed_$day") === 'on';
            $openTime = $request->input("open_$day");
            $closeTime = $request->input("close_$day");

            $businessHour = BusinessHours::firstOrCreate(
                [
                    'agent_profile_id' => $agentProfileId,
                    'day_of_week' => $index,
                ],
                [
                    'is_closed' => true,
                    'opening_time' => null,
                    'closing_time' => null,
                ]
            );

            $businessHour->is_closed = $isClosed;
            $businessHour->opening_time = !$isClosed ? $openTime : null;
            $businessHour->closing_time = !$isClosed ? $closeTime : null;
            $businessHour->save();
        }
    }

  
    private function getTimezonesList()
    {
        return [
            'UTC' => 'UTC',
            'Africa/Cairo' => 'Cairo (UTC+2)',
            'Asia/Beirut' => 'Beirut (UTC+2)',
            'Asia/Amman' => 'Amman (UTC+2)',
            'Africa/Lagos' => 'Lagos (UTC+1)',
            'Africa/Johannesburg' => 'Johannesburg (UTC+2)',
            'Africa/Algiers' => 'Algiers (UTC+1)',
            'Africa/Casablanca' => 'Casablanca (UTC+0)',
            'America/New_York' => 'New York (UTC-5)',
            'America/Chicago' => 'Chicago (UTC-6)',
            'America/Los_Angeles' => 'Los Angeles (UTC-8)',
            'America/Toronto' => 'Toronto (UTC-5)',
            'America/Mexico_City' => 'Mexico City (UTC-6)',
            'America/Buenos_Aires' => 'Buenos Aires (UTC-3)',
            'Asia/Dubai' => 'Dubai (UTC+4)',
            'Asia/Riyadh' => 'Riyadh (UTC+3)',
            'Asia/Qatar' => 'Doha (UTC+3)',
            'Asia/Baghdad' => 'Baghdad (UTC+3)',
            'Asia/Bangkok' => 'Bangkok (UTC+7)',
            'Asia/Hong_Kong' => 'Hong Kong (UTC+8)',
            'Asia/Singapore' => 'Singapore (UTC+8)',
            'Asia/Tokyo' => 'Tokyo (UTC+9)',
            'Asia/Shanghai' => 'Shanghai (UTC+8)',
            'Asia/Kolkata' => 'India (UTC+5:30)',
            'Australia/Sydney' => 'Sydney (UTC+11)',
            'Europe/London' => 'London (UTC+0)',
            'Europe/Paris' => 'Paris (UTC+1)',
            'Europe/Berlin' => 'Berlin (UTC+1)',
            'Europe/Moscow' => 'Moscow (UTC+3)',
            'Pacific/Auckland' => 'Auckland (UTC+13)',
        ];
    }
}
