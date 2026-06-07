<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\Virtual3dRoom;
use App\Models\Virtual3dMedia;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Virtual3dRoomController extends Controller
{
    public function index(Feature $feature)
    {
        // Order berdasarkan urutan dari yang paling awal dibuat
        $virtual3dRooms = $feature->virtual3dRooms()->orderBy('created_at', 'asc')->get();

        // Order berdasarkan data yang baru dibuat
        // $virtual3dRooms = $feature->virtual3dRooms()->latest()->get();
        return view('cms.features.virtual_3d_rooms.index', compact('feature', 'virtual3dRooms'));
    }

    public function create(Feature $feature)
    {
        $allRooms = $feature->virtual3dRooms()->get();
        return view('cms.features.virtual_3d_rooms.create', compact('feature', 'allRooms'));
    }

    public function store(Request $request, Feature $feature, TranslationService $translationService)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:2048',
            'wall_color' => 'nullable|string',
            'floor_color' => 'nullable|string',
            'ceiling_color' => 'nullable|string',
            'diameter_front' => 'nullable|integer',
            'diameter_back' => 'nullable|integer',
            'diameter_left' => 'nullable|integer',
            'diameter_right' => 'nullable|integer',
            'doors' => 'nullable|array',
        ]);

        $room = new Virtual3dRoom();
        $room->feature_id = $feature->id;
        $room->name = $validated['name'];
        $room->description = $validated['description'];
        $room->wall_color = $validated['wall_color'] ?? '#e5e7eb';
        $room->floor_color = $validated['floor_color'] ?? '#8B7355';
        $room->ceiling_color = $validated['ceiling_color'] ?? '#f5f5f5';
        $room->diameter_front = $validated['diameter_front'] ?? 1000;
        $room->diameter_back = $validated['diameter_back'] ?? 1000;
        $room->diameter_left = $validated['diameter_left'] ?? 1000;
        $room->diameter_right = $validated['diameter_right'] ?? 1000;

        // Default doors structure
        $defaultDoors = [
            'front' => ['link_type' => 'none', 'target' => null, 'label' => null],
            'back'  => ['link_type' => 'none', 'target' => null, 'label' => null],
            'left'  => ['link_type' => 'none', 'target' => null, 'label' => null],
            'right' => ['link_type' => 'none', 'target' => null, 'label' => null],
        ];

        $room->doors = array_merge($defaultDoors, $validated['doors'] ?? []);

        if ($request->hasFile('thumbnail')) {
            $room->thumbnail_path = $request->file('thumbnail')->store('virtual_3d_rooms/thumbnails', 'public');
        } elseif ($request->filled('auto_thumbnail')) {
            // Process base64 auto-thumbnail from html2canvas
            $imgData = $request->input('auto_thumbnail');
            if (preg_match('/^data:image\/(\w+);base64,/', $imgData, $type)) {
                $imgData = substr($imgData, strpos($imgData, ',') + 1);
                $imgData = base64_decode($imgData);

                $extension = strtolower($type[1]); // jpeg, png
                $fileName = 'thumbnail_' . Str::random(10) . '_' . time() . '.' . $extension;
                $path = 'virtual_3d_rooms/thumbnails/' . $fileName;

                Storage::disk('public')->put($path, $imgData);
                $room->thumbnail_path = $path;
            }
        }

        $room->save();

        return redirect()->route('cms.features.virtual_3d_rooms.edit', [$feature, $room])
            ->with('success', __('cms.virtual_3d_rooms.flash.created'));
    }

    public function edit(Feature $feature, Virtual3dRoom $room)
    {
        $room->load('media');
        $allRooms = $feature->virtual3dRooms()->where('id', '!=', $room->id)->get();

        // Normalize doors: force any invalid link_type to 'none'
        $validLinkTypes = ['room', 'url'];
        $defaultWalls = ['front', 'back', 'left', 'right'];
        $doors = $room->doors ?? [];
        $normalizedDoors = [];
        foreach ($defaultWalls as $wall) {
            $cfg = $doors[$wall] ?? [];
            $linkType = $cfg['link_type'] ?? 'none';
            if (!in_array($linkType, $validLinkTypes)) {
                $linkType = 'none';
            }
            $normalizedDoors[$wall] = [
                'link_type' => $linkType,
                'target'    => $cfg['target'] ?? null,
                'label'     => $cfg['label'] ?? null,
            ];
        }
        $room->doors = $normalizedDoors;

        return view('cms.features.virtual_3d_rooms.edit', compact('feature', 'room', 'allRooms'));
    }

    public function update(Request $request, Feature $feature, Virtual3dRoom $room)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'description'      => 'nullable|string',
            'thumbnail'        => 'nullable|image|max:2048',
            'remove_thumbnail' => 'nullable|in:0,1',
            'wall_color'       => 'nullable|string',
            'floor_color'      => 'nullable|string',
            'ceiling_color'    => 'nullable|string',
            'diameter_front'   => 'nullable|integer',
            'diameter_back'    => 'nullable|integer',
            'diameter_left'    => 'nullable|integer',
            'diameter_right'   => 'nullable|integer',
            'doors'            => 'nullable|array',
            'deleted_media_ids' => 'nullable|string',
            'new_media'           => 'nullable|array',
            'new_media.*.file'    => 'required|file|mimes:jpg,jpeg,png,webp,mp4,webm|max:20480',
            'new_media.*.wall'    => 'required|in:front,back,left,right',
            'new_media.*.type'    => 'required|in:image,video',
            'new_media.*.position_x' => 'required|numeric',
            'new_media.*.position_y' => 'required|numeric',
            'new_media.*.width'      => 'required|numeric',
            'new_media.*.height'     => 'required|numeric',
            'new_media.*.description' => 'nullable',
        ]);

        // Apply deferred media deletions (media the user removed in the editor but
        // that are only committed once "Save" is pressed).
        $mediaChanged = false;
        if (!empty($validated['deleted_media_ids'])) {
            $deletedIds = array_filter(array_map('intval', explode(',', $validated['deleted_media_ids'])));
            if (!empty($deletedIds)) {
                $mediaToDelete = $room->media()->whereIn('id', $deletedIds)->get();
                foreach ($mediaToDelete as $media) {
                    Storage::disk('public')->delete($media->file_path);
                    $media->delete();
                }
                $room->load('media');
                $mediaChanged = $mediaChanged || $mediaToDelete->isNotEmpty();
            }
        }

        // Apply deferred media uploads (files held in the browser and only sent
        // to the server when "Save" is pressed).
        if ($request->hasFile('new_media') || !empty($validated['new_media'])) {
            foreach ($validated['new_media'] ?? [] as $i => $item) {
                if (!$request->hasFile("new_media.$i.file")) {
                    continue;
                }
                $path = $request->file("new_media.$i.file")
                    ->store('virtual_3d_rooms/media', 'public');

                $desc = $item['description'] ?? '';
                if (is_string($desc) && (str_starts_with($desc, '{') || str_starts_with($desc, '['))) {
                    $decoded = json_decode($desc, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $desc = $decoded;
                    }
                }

                $media = new Virtual3dMedia();
                $media->virtual3d_room_id = $room->id;
                $media->wall = $item['wall'];
                $media->type = $item['type'];
                $media->description = $desc;
                $media->file_path = $path;
                $media->position_x = $item['position_x'];
                $media->position_y = $item['position_y'];
                $media->width = $item['width'];
                $media->height = $item['height'];
                $media->save();

                $mediaChanged = true;
            }
            $room->load('media');
        }

        $room->name         = $validated['name'];
        $room->description  = $validated['description'];
        $room->wall_color   = $validated['wall_color']   ?? '#e5e7eb';
        $room->floor_color  = $validated['floor_color']  ?? '#8B7355';
        $room->ceiling_color = $validated['ceiling_color'] ?? '#f5f5f5';
        $room->diameter_front = $validated['diameter_front'] ?? 1000;
        $room->diameter_back = $validated['diameter_back'] ?? 1000;
        $room->diameter_left = $validated['diameter_left'] ?? 1000;
        $room->diameter_right = $validated['diameter_right'] ?? 1000;

        $defaultDoors = [
            'front' => ['link_type' => 'none', 'target' => null, 'label' => null],
            'back'  => ['link_type' => 'none', 'target' => null, 'label' => null],
            'left'  => ['link_type' => 'none', 'target' => null, 'label' => null],
            'right' => ['link_type' => 'none', 'target' => null, 'label' => null],
        ];

        $room->doors = array_merge($defaultDoors, $validated['doors'] ?? []);

        if ($request->hasFile('thumbnail')) {
            // ① Manual upload — replace existing
            if ($room->thumbnail_path) {
                Storage::disk('public')->delete($room->thumbnail_path);
            }
            $room->thumbnail_path = $request->file('thumbnail')
                ->store('virtual_3d_rooms/thumbnails', 'public');
        } elseif ($request->input('remove_thumbnail') == '1') {
            // ② User deleted thumbnail → remove file, then immediately auto-generate
            if ($room->thumbnail_path) {
                Storage::disk('public')->delete($room->thumbnail_path);
                $room->thumbnail_path = null;
            }
            // Auto-generate right away so it's never left empty
            $room->load('media');
            $room->thumbnail_path = $this->generateAutoThumbnail($room);
        } elseif (!$room->thumbnail_path) {
            // ③ No thumbnail at all → auto-generate
            $room->load('media');
            $room->thumbnail_path = $this->generateAutoThumbnail($room);
        } elseif ($mediaChanged && str_contains($room->thumbnail_path, 'virtual_3d_rooms/thumbnails/thumbnail_')) {
            // ④ Media added/removed and current thumbnail is auto-generated → regenerate
            // so it reflects the current media (and clean up the stale file).
            Storage::disk('public')->delete($room->thumbnail_path);
            $room->load('media');
            $room->thumbnail_path = $this->generateAutoThumbnail($room);
        }
        // ⑤ Existing manual thumbnail + no changes → keep as-is (do nothing)

        $room->save();

        return redirect()->route('cms.features.virtual_3d_rooms.index', $feature)
            ->with('success', __('cms.virtual_3d_rooms.flash.updated'));
    }

    /**
     * Generate a thumbnail by compositing ALL front-wall media images
     * onto the wall color background.
     * Canvas: 1280×720 (16:9). Returns the storage-relative path or null on failure.
     */
    private function generateAutoThumbnail(Virtual3dRoom $room): ?string
    {
        if (!function_exists('imagecreatetruecolor')) {
            return null; // GD not available
        }

        // Collect all front-wall images; fallback to any wall images
        $mediaItems = $room->media
            ->where('wall', 'front')
            ->where('type', 'image')
            ->values();

        if ($mediaItems->isEmpty()) {
            $mediaItems = $room->media
                ->where('type', 'image')
                ->values();
        }

        if ($mediaItems->isEmpty()) {
            return null; // No image media at all
        }

        // ── Canvas ────────────────────────────────────────────────────
        $canvasW = 1280;
        $canvasH = 720;

        $canvas = imagecreatetruecolor($canvasW, $canvasH);

        // Fill wall color background
        $hex = ltrim($room->wall_color ?? '#e5e7eb', '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        $bg = imagecolorallocate(
            $canvas,
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2))
        );
        imagefill($canvas, 0, 0, $bg);

        // ── Composite every media item onto the canvas ─────────────────
        foreach ($mediaItems as $media) {
            if (!Storage::disk('public')->exists($media->file_path)) {
                continue;
            }

            $srcPath = Storage::disk('public')->path($media->file_path);
            $ext     = strtolower(pathinfo($srcPath, PATHINFO_EXTENSION));

            $src = match ($ext) {
                'jpg', 'jpeg' => @imagecreatefromjpeg($srcPath),
                'png'         => @imagecreatefrompng($srcPath),
                'webp'        => @imagecreatefromwebp($srcPath),
                default       => null,
            };

            if (!$src) {
                continue;
            }

            $srcW = imagesx($src);
            $srcH = imagesy($src);

            // Slot: position_x/y are the CENTER of the slot (%), width/height are size (%)
            $slotW = (int)($media->width      / 100 * $canvasW);
            $slotH = (int)($media->height     / 100 * $canvasH);
            $slotX = (int)($media->position_x / 100 * $canvasW) - intdiv($slotW, 2);
            $slotY = (int)($media->position_y / 100 * $canvasH) - intdiv($slotH, 2);

            // Maintain aspect ratio (objectFit: contain — same as guest viewer)
            $scale = min(
                $slotW / max($srcW, 1),
                $slotH / max($srcH, 1)
            );
            $drawW = (int)($srcW * $scale);
            $drawH = (int)($srcH * $scale);
            $drawX = $slotX + intdiv($slotW - $drawW, 2);
            $drawY = $slotY + intdiv($slotH - $drawH, 2);

            imagecopyresampled(
                $canvas,
                $src,
                $drawX,
                $drawY,
                0,
                0,
                $drawW,
                $drawH,
                $srcW,
                $srcH
            );
            imagedestroy($src);
        }

        // ── Save ──────────────────────────────────────────────────────
        Storage::disk('public')->makeDirectory('virtual_3d_rooms/thumbnails');
        $fileName = 'thumbnail_' . Str::random(10) . '_' . time() . '.jpg';
        $path     = 'virtual_3d_rooms/thumbnails/' . $fileName;
        $fullPath = Storage::disk('public')->path($path);

        imagejpeg($canvas, $fullPath, 90);
        imagedestroy($canvas);

        return $path;
    }




    public function destroy(Feature $feature, Virtual3dRoom $room)
    {
        $room->delete();

        return redirect()->route('cms.features.virtual_3d_rooms.index', $feature)
            ->with('success', __('cms.virtual_3d_rooms.flash.deleted'));
    }

    // Media Management
    public function uploadMedia(Request $request, Feature $feature, Virtual3dRoom $room)
    {
        $validated = $request->validate([
            'wall' => 'required|in:front,back,left,right',
            'file' => 'required|file|mimes:jpg,jpeg,png,webp,mp4,webm|max:20480',
            'type' => 'required|in:image,video',
            'description' => 'nullable',
            'position_x' => 'required|numeric',
            'position_y' => 'required|numeric',
            'width' => 'required|numeric',
            'height' => 'required|numeric',
        ]);

        $path = $request->file('file')->store('virtual_3d_rooms/media', 'public');

        $desc = $validated['description'];
        if (is_string($desc) && (str_starts_with($desc, '{') || str_starts_with($desc, '['))) {
            $decoded = json_decode($desc, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $desc = $decoded;
            }
        }

        $media = new Virtual3dMedia();
        $media->virtual3d_room_id = $room->id;
        $media->wall = $validated['wall'];
        $media->type = $validated['type'];
        $media->description = $desc;
        $media->file_path = $path;
        $media->position_x = $validated['position_x'];
        $media->position_y = $validated['position_y'];
        $media->width = $validated['width'];
        $media->height = $validated['height'];
        $media->save();

        // Regenerate auto-thumbnail if the thumbnail was auto-generated to keep it updated,
        // and delete the old one to avoid leaving orphaned thumbnails in storage.
        if ($room->thumbnail_path && str_contains($room->thumbnail_path, 'virtual_3d_rooms/thumbnails/thumbnail_')) {
            Storage::disk('public')->delete($room->thumbnail_path);
            $room->load('media');
            $room->thumbnail_path = $this->generateAutoThumbnail($room);
            $room->save();
        }

        return response()->json([
            'success' => true,
            'media' => $media,
            'url' => asset('storage/' . $path)
        ]);
    }

    public function updateMediaPosition(Request $request, Feature $feature, Virtual3dRoom $room, Virtual3dMedia $media)
    {
        $validated = $request->validate([
            'position_x' => 'required|numeric',
            'position_y' => 'required|numeric',
            'width' => 'required|numeric',
            'height' => 'required|numeric',
            'description' => 'nullable',
        ]);

        $desc = $request->input('description');
        $debug_type = gettype($desc);
        
        if (is_string($desc)) {
            // Clean up possible HTML encoding from middleware
            $desc = html_entity_decode($desc);
            if (str_starts_with($desc, '{') || str_starts_with($desc, '[')) {
                $decoded = json_decode($desc, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $desc = $decoded;
                }
            }
        }

        $media->position_x = $validated['position_x'];
        $media->position_y = $validated['position_y'];
        $media->width = $validated['width'];
        $media->height = $validated['height'];
        $media->description = $desc;
        $media->save();

        // Regenerate auto-thumbnail if the thumbnail was auto-generated to keep it updated,
        // and delete the old one to avoid leaving orphaned thumbnails in storage.
        if ($room->thumbnail_path && str_contains($room->thumbnail_path, 'virtual_3d_rooms/thumbnails/thumbnail_')) {
            Storage::disk('public')->delete($room->thumbnail_path);
            $room->load('media');
            $room->thumbnail_path = $this->generateAutoThumbnail($room);
            $room->save();
        }

        return response()->json(['success' => true, 'media' => $media]);
    }

    public function deleteMedia(Feature $feature, Virtual3dRoom $room, Virtual3dMedia $media)
    {
        Storage::disk('public')->delete($media->file_path);
        $media->delete();

        // Regenerate auto-thumbnail if the thumbnail was auto-generated to keep it updated,
        // and delete the old one to avoid leaving orphaned thumbnails in storage.
        if ($room->thumbnail_path && str_contains($room->thumbnail_path, 'virtual_3d_rooms/thumbnails/thumbnail_')) {
            Storage::disk('public')->delete($room->thumbnail_path);
            $room->load('media');
            $room->thumbnail_path = $this->generateAutoThumbnail($room);
            $room->save();
        }

        return response()->json(['success' => true]);
    }
}
