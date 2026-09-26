<?php

namespace App\Http\Controllers;

use App\Helpers\ImageOptimizer;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class CompanySettingController extends Controller
{
    public function index()
    {
        $setting = CompanySetting::getSetting();

        $businessTypes = [
            'General / Multi-industry',
            'F&B / Restaurant & Cafe',
            'Retail / E-Commerce',
            'Manufacturing & Factory',
            'Healthcare & Clinic',
            'Logistics & Transportation',
            'Technology / Software',
            'Construction & Real Estate',
            'Education & Training',
            'Hospitality & Hotel',
            'Professional Services & Consulting',
        ];

        $timezones = [
            'Asia/Jakarta' => 'WIB — Waktu Indonesia Barat (Asia/Jakarta)',
            'Asia/Makassar' => 'WITA — Waktu Indonesia Tengah (Asia/Makassar)',
            'Asia/Jayapura' => 'WIT — Waktu Indonesia Timur (Asia/Jayapura)',
        ];

        $currencies = [
            'IDR' => 'IDR — Rupiah (Rp)',
            'USD' => 'USD — US Dollar ($)',
            'SGD' => 'SGD — Singapore Dollar (S$)',
        ];

        $dateFormats = [
            'd-m-Y' => 'DD-MM-YYYY (Contoh: 25-09-2026)',
            'd/m/Y' => 'DD/MM/YYYY (Contoh: 25/09/2026)',
            'Y-m-d' => 'YYYY-MM-DD (Contoh: 2026-09-25)',
            'd M Y' => 'DD Mon YYYY (Contoh: 25 Sep 2026)',
        ];

        return view('settings.company.index', compact('setting', 'businessTypes', 'timezones', 'currencies', 'dateFormats'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'legal_name' => 'nullable|string|max:255',
            'business_type' => 'required|string|max:100',
            'npwp' => 'nullable|string|max:50',
            'nib' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:1000',
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'website' => 'nullable|url|max:150',
            'timezone' => 'required|string|max:50',
            'locale' => 'required|string|max:10',
            'currency' => 'required|string|max:10',
            'date_format' => 'required|string|max:20',
            'payroll_cutoff_date' => 'required|integer|min:1|max:31',
            'payroll_payment_date' => 'required|integer|min:1|max:31',
            'app_name' => 'required|string|max:100',
            'app_tagline' => 'nullable|string|max:255',
            'theme_color_primary' => 'nullable|string|max:20',
            'theme_color_secondary' => 'nullable|string|max:20',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $setting = CompanySetting::first() ?? new CompanySetting();

        $data = $request->only([
            'company_name',
            'legal_name',
            'business_type',
            'npwp',
            'nib',
            'address',
            'province',
            'city',
            'postal_code',
            'phone',
            'email',
            'website',
            'timezone',
            'locale',
            'currency',
            'date_format',
            'payroll_cutoff_date',
            'payroll_payment_date',
            'app_name',
            'app_tagline',
            'theme_color_primary',
            'theme_color_secondary',
        ]);

        if ($request->hasFile('logo')) {
            if ($setting->logo) {
                if (Storage::disk('public')->exists('logo/' . $setting->logo)) {
                    Storage::disk('public')->delete('logo/' . $setting->logo);
                }
            }

            $logoName = ImageOptimizer::saveAsWebp(
                $request->file('logo'),
                'logo',
                'company_logo_' . time() . '_' . uniqid(),
                85,
                512,
                'public'
            );

            $data['logo'] = $logoName;

            // Auto-sync to public/logo.png and public/favicon.ico
            try {
                $manager = new ImageManager(new Driver());
                $logoImg = $manager->read($request->file('logo'));
                $logoImg->toPng()->save(public_path('logo.png'));

                $favImg = $manager->read($request->file('logo'));
                $favImg->cover(64, 64)->toPng()->save(public_path('favicon.ico'));
            } catch (\Throwable $e) {
                \Log::warning('Logo sync warning: ' . $e->getMessage());
            }
        }

        $setting->fill($data);
        $setting->save();

        Cache::forget('company_settings_first');
        Cache::forget('global_app_logo_relative_path');
        \App\Services\ThemeResolver::forgetCache();

        return redirect()->route('company_settings.index')->with('success', 'Pengaturan profil perusahaan & branding berhasil diperbarui!');
    }
}
