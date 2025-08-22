<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
   
    public function index()
    {
        // Fetch the existing record or create a default one
        $settings = Settings::firstOrCreate(
            ['id' => 1],
            [
                'name' => "",
                'address' => "",
                'phone1' => "",
                'phone2' => "",
                'title1' => "",
                'title2' => "",
                'note' => "",
                'image' => "",
            ]
        );

        return inertia('Settings/Edit', [
            'Settings' => $settings,
        ]);
    }
    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone1' => ['nullable', 'string', 'max:255'],
            'phone2' => ['nullable', 'string', 'max:255'],
            'title1' => ['nullable', 'string', 'max:255'],
            'title2' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:2048'], // only valid if it's a real file
        ]);

        $settings = Settings::firstOrCreate(['id' => 1]);

        if ($request->hasFile('image')) {
            // Delete old image from storage
            if ($settings->image && Storage::disk('public')->exists($settings->image)) {
                Storage::disk('public')->delete($settings->image);
            }
            $path = $request->file('image')->store('settings_images', 'public');
            $data['image'] = $path;
        } else {
            // Don't update image if no new file is uploaded
            unset($data['image']);
        }

        $settings->update($data);

        return redirect()->back()->with('message', 'نرخی دۆلار و وێنە نوێکرانەوە');
    }
}
