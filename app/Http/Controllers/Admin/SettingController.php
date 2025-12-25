<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function bankAccount()
    {
        $settings = Setting::where('group', 'bank_account')->get()->keyBy('key');

        return view('admin.settings.bank-account', compact('settings'));
    }

    public function updateBankAccount(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:100',
            'bank_account_number' => 'required|string|max:50',
            'bank_account_name' => 'required|string|max:100',
        ], [
            'bank_name.required' => 'Nama bank wajib diisi',
            'bank_account_number.required' => 'Nomor rekening wajib diisi',
            'bank_account_name.required' => 'Nama pemilik rekening wajib diisi',
        ]);

        Setting::set('bank_name', $request->bank_name);
        Setting::set('bank_account_number', $request->bank_account_number);
        Setting::set('bank_account_name', $request->bank_account_name);

        // Clear cache
        Setting::clearGroupCache('bank_account');

        return redirect()->back()->with('success', 'Informasi rekening bank berhasil diperbarui');
    }
}
