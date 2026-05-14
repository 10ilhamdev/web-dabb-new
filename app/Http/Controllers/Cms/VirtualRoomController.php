<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\VirtualRoom;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VirtualRoomController extends Controller
{
    public function index(Feature $feature)
    {
        $rooms = $feature->virtualRooms()->withCount('hotspots')->latest()->get();

        $totalRooms = $rooms->count();
        $totalHotspots = $rooms->sum('hotspots_count');
        $avgHotspots = $totalRooms > 0 ? round($totalHotspots / $totalRooms, 1) : 0;

        return view('cms.features.virtual_rooms.index', compact('feature', 'rooms', 'totalRooms', 'totalHotspots', 'avgHotspots'));
    }

    public function create(Feature $feature)
    {
        // Load existing rooms so we can link them in hotspots
        $allRooms = $feature->virtualRooms()->get();
        return view('cms.features.virtual_rooms.form', compact('feature', 'allRooms'));
    }

    public function store(Request $request, Feature $feature)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'thumbnail' => 'required|image|mimes:jpg,jpeg,png,webp',
            'image_360' => 'required|image|mimes:jpg,jpeg,png,webp',
            'hotspots' => 'nullable|array',
            'hotspots.*.yaw' => 'required|numeric',
            'hotspots.*.pitch' => 'required|numeric',
            'hotspots.*.text_tooltip' => 'required|string|max:255',
            'hotspots.*.target_room_id' => 'required|exists:virtual_rooms,id',
            'hotspots.*.type' => 'nullable|string|in:floor,door',
        ]);

        $room = new VirtualRoom();
        $room->feature_id = $feature->id;
        $room->name = $validated['name'];
        $room->description = $validated['description'];

        if ($request->hasFile('thumbnail')) {
            $room->thumbnail_path = $request->file('thumbnail')->store('virtual_rooms/thumbnails', 'public');
        }

        if ($request->hasFile('image_360')) {
            $room->image_360_path = $request->file('image_360')->store('virtual_rooms/panoramas', 'public');
        }

        $room->save();

        if (!empty($validated['hotspots'])) {
            foreach ($validated['hotspots'] as $hotspotData) {
                $room->hotspots()->create($hotspotData);
            }
        }

        return redirect()->route('cms.features.virtual_rooms.index', $feature)
            ->with('success', __('cms.virtual_rooms.flash.created'));
    }

    public function edit(Feature $feature, VirtualRoom $room)
    {
        $allRooms = $feature->virtualRooms()->where('id', '!=', $room->id)->get();
        $room->load('hotspots');
        return view('cms.features.virtual_rooms.form', compact('feature', 'room', 'allRooms'));
    }

    public function update(Request $request, Feature $feature, VirtualRoom $room)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'image_360' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'hotspots' => 'nullable|array',
            'hotspots.*.id' => 'nullable|exists:virtual_hotspots,id',
            'hotspots.*.yaw' => 'required|numeric',
            'hotspots.*.pitch' => 'required|numeric',
            'hotspots.*.text_tooltip' => 'required|string|max:255',
            'hotspots.*.target_room_id' => 'required|exists:virtual_rooms,id',
            'hotspots.*.type' => 'nullable|string|in:floor,door',
        ]);

        $room->name = $validated['name'];
        $room->description = $validated['description'];

        if ($request->hasFile('thumbnail')) {
            if ($room->thumbnail_path) {
                Storage::disk('public')->delete($room->thumbnail_path);
            }
            $room->thumbnail_path = $request->file('thumbnail')->store('virtual_rooms/thumbnails', 'public');
        }

        if ($request->hasFile('image_360')) {
            if ($room->image_360_path) {
                Storage::disk('public')->delete($room->image_360_path);
            }
            $room->image_360_path = $request->file('image_360')->store('virtual_rooms/panoramas', 'public');
        }

        $room->save();

        // Sync hotspots - delete all entirely and re-add or selectively update
        // We will just recreate for simplicity if there is a list of submitted ones, but we need to track IDs to keep them
        $submittedIds = collect($validated['hotspots'] ?? [])->pluck('id')->filter()->toArray();
        $room->hotspots()->whereNotIn('id', $submittedIds)->delete();

        if (!empty($validated['hotspots'])) {
            foreach ($validated['hotspots'] as $hotspotData) {
                if (!empty($hotspotData['id'])) {
                    $room->hotspots()->where('id', $hotspotData['id'])->update([
                        'yaw' => $hotspotData['yaw'],
                        'pitch' => $hotspotData['pitch'],
                        'text_tooltip' => $hotspotData['text_tooltip'],
                        'target_room_id' => $hotspotData['target_room_id'],
                        'type' => $hotspotData['type'] ?? 'floor',
                    ]);
                } else {
                    $room->hotspots()->create($hotspotData);
                }
            }
        }

        return redirect()->route('cms.features.virtual_rooms.index', $feature)
            ->with('success', __('cms.virtual_rooms.flash.updated'));
    }

    public function destroy(Feature $feature, VirtualRoom $room)
    {
        if ($room->thumbnail_path) {
            Storage::disk('public')->delete($room->thumbnail_path);
        }
        if ($room->image_360_path) {
            Storage::disk('public')->delete($room->image_360_path);
        }

        $room->delete();

        return redirect()->route('cms.features.virtual_rooms.index', $feature)
            ->with('success', __('cms.virtual_rooms.flash.deleted'));
    }
}
