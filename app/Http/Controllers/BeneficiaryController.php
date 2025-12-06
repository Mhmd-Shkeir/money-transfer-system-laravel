<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Beneficiary;
use Illuminate\Validation\Rule;

class BeneficiaryController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $beneficiaries = $user->beneficiaries()->with('country')->get();
        return view('user.beneficiaries', compact('beneficiaries'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('beneficiaries')->where(function ($query) use ($user) {
                    return $query->where('user_id', $user->id);
                }),
            ],
            'country_id' => 'required|exists:countries,id',
            'bank_name' => 'nullable|string|max:255',
            'bank_account' => 'nullable|string|max:255',
            'swift_code' => 'nullable|string|max:255',
            'iban' => 'nullable|string|max:255',
            'wallet_provider' => 'nullable|string|max:255',
            'wallet_id' => 'nullable|string|max:255',
        ]);

        $data['user_id'] = $user->id;

        Beneficiary::create($data);

        return redirect()->route('user.beneficiaries')->with('success', 'Beneficiary added.');
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $b = $user->beneficiaries()->where('id', $id)->firstOrFail();
        $b->delete();
        return redirect()->route('user.beneficiaries')->with('success', 'Beneficiary removed.');
    }
}
