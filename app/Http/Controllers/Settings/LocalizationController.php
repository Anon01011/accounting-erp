<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LocalizationController extends Controller
{
    public function index()
    {
        return view('settings.localization.index');
    }

    public function update(Request $request)
    {
        $request->validate([
            'timezone' => ['required', 'string', 'timezone'],
            'date_format' => ['required', 'string', 'in:Y-m-d,d-m-Y,m-d-Y,d/m/Y,m/d/Y,Y/m/d'],
            'time_format' => ['required', 'string', 'in:H:i,H:i:s,h:i A,h:i:s A'],
            'currency' => ['required', 'string', 'size:3'],
            'currency_symbol' => ['required', 'string', 'max:10'],
            'currency_position' => ['required', 'string', 'in:prefix,suffix'],
            'decimal_separator' => ['required', 'string', 'size:1'],
            'thousand_separator' => ['required', 'string', 'size:1'],
            'decimal_places' => ['required', 'integer', 'min:0', 'max:4'],
        ]);

        $settings = [
            'timezone' => $request->timezone,
            'date_format' => $request->date_format,
            'time_format' => $request->time_format,
            'currency' => $request->currency,
            'currency_symbol' => $request->currency_symbol,
            'currency_position' => $request->currency_position,
            'decimal_separator' => $request->decimal_separator,
            'thousand_separator' => $request->thousand_separator,
            'decimal_places' => $request->decimal_places,
        ];

        foreach ($settings as $key => $value) {
            setting([$key => $value])->save();
        }

        return redirect()->route('settings.localization.index')
            ->with('success', 'Localization settings updated successfully.');
    }
} 