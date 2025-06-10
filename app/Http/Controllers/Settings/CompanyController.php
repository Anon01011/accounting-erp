<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    public function index()
    {
        return view('settings.company.index');
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:20'],
            'tax_id' => ['nullable', 'string', 'max:50'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        $settings = [
            'company_name' => $request->name,
            'company_email' => $request->email,
            'company_phone' => $request->phone,
            'company_address' => $request->address,
            'company_city' => $request->city,
            'company_state' => $request->state,
            'company_country' => $request->country,
            'company_postal_code' => $request->postal_code,
            'company_tax_id' => $request->tax_id,
        ];

        if ($request->hasFile('logo')) {
            if (setting('company_logo')) {
                Storage::delete(setting('company_logo'));
            }
            $settings['company_logo'] = $request->file('logo')->store('company', 'public');
        }

        foreach ($settings as $key => $value) {
            setting([$key => $value])->save();
        }

        return redirect()->route('settings.company.index')
            ->with('success', 'Company settings updated successfully.');
    }
} 