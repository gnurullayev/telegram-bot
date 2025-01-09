<?php

namespace App\Http\Controllers;

use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class FileUploadController extends Controller
{
    public function upload(Request $request)
    {
        $receiver = new FileReceiver('file', $request, HandlerFactory::classFromRequest($request), null, null);

        if ($receiver->isUploaded()) {
            $save = $receiver->receive();

            Log::info($save->getFile());
            // dd($save);
            if ($save->isFinished()) {
                $file = $save->getFile();
                // Fayl nomini olish
                $fileName = $file->getClientOriginalName() ?? 'default_name.' . $file->getClientOriginalExtension();

                // Fayl nomiga vaqt qo'shish va yangi nom yaratish
                $newFileName = pathinfo($fileName, PATHINFO_FILENAME) . '_' . time() . '.' . $file->getClientOriginalExtension();

                $filePath = $file->storeAs('uploads', $newFileName, 'public');

                // Faylning to'liq URL manzilini olish
                // $fileUrl = asset('storage/' . $filePath);

                return response()->json([
                    'url' => $filePath,
                    'message' => 'Fayl muvaffaqiyatli yuklandi!'
                ]);
            } else {
                return response()->json(['message' => 'Fayl hali yuklanmoqda...']);
            }
        }

        return response()->json(['message' => 'Fayl yuklanmoqda...']);
    }
}
