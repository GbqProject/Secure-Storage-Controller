<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use App\Models\StoredFile;
use App\Models\ForbiddenExtension;

class FileController extends Controller
{
    public function index(Request $r){
        $user = $r->user();
        $files = $user->files()->get();
        $forbidden = ForbiddenExtension::pluck('ext')->toArray();
        $globalQuota = (int)\DB::table('settings')->where('key','global_quota_bytes')->value('value') ?? 10*1024*1024;
        return view('user.dashboard', compact('files','forbidden','globalQuota'));
    }

    public function upload(Request $r){
        $user = $r->user();
        if (!$r->hasFile('file')) {
            return response()->json(['error'=>'No file uploaded'], 422);
        }
        $file = $r->file('file');
        $size = $file->getSize();
        $orig = $file->getClientOriginalName();
        $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));

        // 1) Check forbidden ext global list
        $forbiddenList = ForbiddenExtension::pluck('ext')->map(function($e){ return ltrim(strtolower($e),'.'); })->toArray();
        if (in_array($ext, $forbiddenList)) {
            return response()->json(['error'=>"Error: El tipo de archivo '.{$ext}' no está permitido"], 422);
        }

        // 2) If zip, inspect contents
        if ($ext === 'zip') {
            $tmp = $file->getRealPath();
            $zip = new ZipArchive();
            if ($zip->open($tmp) === true) {
                for ($i=0; $i<$zip->numFiles; $i++){
                    $stat = $zip->statIndex($i);
                    $innerName = $stat['name'];
                    $innerExt = strtolower(pathinfo($innerName, PATHINFO_EXTENSION));
                    if ($innerExt && in_array($innerExt, $forbiddenList)){
                        $zip->close();
                        return response()->json(['error'=>"Error: El archivo '{$innerName}' dentro del .zip no está permitido"], 422);
                    }
                }
                $zip->close();
            } else {
                return response()->json(['error'=>'Error al leer el .zip'], 422);
            }
        }

        // 3) Quota check (user effective)
        $currentUsed = $user->usedBytes();
        $quota = $user->effectiveQuota();
        if (($currentUsed + $size) > $quota) {
            $mb = round($quota / (1024*1024), 2);
            return response()->json(['error'=>"Error: Cuota de almacenamiento ({$mb} MB) excedida"], 422);
        }

        // 4) Save file
        $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $orig);
        $path = $file->storeAs('public/uploads', $filename);
        $stored = StoredFile::create([
            'user_id' => $user->id,
            'original_name' => $orig,
            'path' => $path,
            'size_bytes' => $size,
            'mime_type' => $file->getClientMimeType()
        ]);

        return response()->json([
            'success' => true,
            'file' => [
                'id' => $stored->id,
                'name' => $stored->original_name,
                'size' => $stored->size_bytes
            ]
        ]);
    }

    public function download($id, Request $r){
        $file = StoredFile::findOrFail($id);
        if ($file->user_id != $r->user()->id && !$r->user()->isAdmin()) abort(403);
        return Storage::download($file->path, $file->original_name);
    }
}
