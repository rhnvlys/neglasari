<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'nullable|array',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
        ]);

        try {
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $filename = 'logo-tasikmalaya.png';
                $destinationPath = public_path('images');

                if (!file_exists($destinationPath)) {
                    @mkdir($destinationPath, 0755, true);
                }

                try {
                    $file->move($destinationPath, $filename);
                } catch (\Throwable $e) {
                    Log::warning('Could not write logo to public_path, saving to storage: ' . $e->getMessage());
                    @mkdir(storage_path('app/public/images'), 0755, true);
                    @copy($file->getRealPath(), storage_path('app/public/images/' . $filename));
                }

                Setting::updateOrCreate(
                    ['key' => 'app_logo'],
                    [
                        'value' => 'images/' . $filename,
                        'group' => 'general',
                        'type' => 'string',
                        'is_public' => true,
                    ]
                );
            }

            if ($request->filled('settings')) {
                foreach ($request->settings as $key => $value) {
                    Setting::updateOrCreate(
                        ['key' => $key],
                        [
                            'value' => is_array($value) ? json_encode($value) : (string) $value,
                            'group' => 'general',
                            'type' => 'string',
                            'is_public' => true,
                        ]
                    );
                }
            }

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'UPDATE',
                'module' => 'SETTINGS',
                'description' => 'Mengubah pengaturan sistem dan memperbarui logo instansi',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->route('admin.settings.index')->with('success', 'Pengaturan sistem & logo berhasil diperbarui.');
        } catch (\Throwable $e) {
            Log::error('Settings update error: ' . $e->getMessage());
            return redirect()->route('admin.settings.index')->with('error', 'Gagal memperbarui pengaturan: ' . $e->getMessage());
        }
    }
}
