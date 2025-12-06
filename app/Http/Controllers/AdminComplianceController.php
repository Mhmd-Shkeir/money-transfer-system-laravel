<?php

namespace App\Http\Controllers;

use App\Models\Compliance;
use Illuminate\Http\Request;

class AdminComplianceController extends Controller
{
    public function index()
    {
        $compliances = Compliance::orderBy('created_at', 'desc')->get();
        return view('admin.compliance', compact('compliances'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'        => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        Compliance::create([
            'type'        => $request->type,
            'description' => $request->description,
            'created_by'  => auth()->id(),
            'status'      => 'pending'
        ]);

        return back()->with('success', 'Compliance record added.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected'
        ]);

        $item = Compliance::findOrFail($id);
        $item->update(['status' => $request->status]);

        return back()->with('success', 'Compliance status updated.');
    }

    public function destroy($id)
    {
        Compliance::findOrFail($id)->delete();
        return back()->with('success', 'Compliance record deleted.');
    }
}
