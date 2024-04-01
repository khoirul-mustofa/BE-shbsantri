<?php

namespace App\Http\Controllers;

use App\Response\CustomsResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileController extends Controller
{
    public function uploadImage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg|max:5048',
        ]);

        if ($validator->fails()) {
            return CustomsResponse::error(
                $validator->errors()->toArray(),
                'The image field is required.',
                422
            );
        }

        $image = $request->file('image');
        $imageName = $image->getClientOriginalName(); // Get original file name
        $imageSize = $this->formatSizeUnits($image->getSize());
        $slug = Str::slug(pathinfo($imageName, PATHINFO_FILENAME)); // Generate slug from filename
        $extension = $image->getClientOriginalExtension(); // Get original file extension
        $filename = $slug . '.' . $extension; // Generate filename using slug and original extension
        if (Storage::exists('public/images/' . $filename)) {
            return response()->json([
                "status" => 400,
                'file_name' => $filename,
                'message' => 'File with the same name already exists.',
            ], 400);
        }
        $path = $image->storeAs('public/images/', $filename); // Store file with generated filename

        return response()->json([
            "status" => 201,
            'message' => 'Image uploaded successfully.',
            'path' => url(Storage::url($path)),
            'size_file' => $imageSize,
            'file_name' => $filename,
        ]);
    }


    public function uploadPDF(Request $request): JsonResponse
    {


        $validator = Validator::make($request->all(), [
            'pdf' => 'required|mimes:pdf|max:5048',
        ]);

        if ($validator->fails()) {
            return CustomsResponse::error(
                $validator->errors()->toArray(),
                'The pdf field is required.',
                422
            );
        }

        $pdf = $request->file('pdf');
        $pdfName = $pdf->getClientOriginalName(); // Get original file name
        $pdfSize = $this->formatSizeUnits($pdf->getSize());
        $slug = Str::slug(pathinfo($pdfName, PATHINFO_FILENAME)); // Generate slug from filename
        $filename = $slug . '.' . $pdf->getClientOriginalExtension(); // Generate filename using slug and original extension
        if (Storage::exists('public/pdfs/' . $filename)) {
            return response()->json([
                "status" => 400,
                'file_name' => $filename,
                'message' => 'File with the same name already exists.',
            ], 400);
        }
        $path = $pdf->storeAs('public/pdfs', $filename); // Store file with generated filename

        return response()->json([
            "status" => 201,
            'message' => 'PDF uploaded successfully.',
            'path' => url(Storage::url($path)),
            'size_file' => $pdfSize,
            'file_name' => $filename,
        ]);
    }

    public function uploadPPT(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'ppt' => 'required|mimes:ppt,pptx|max:5048',
        ]);

        if ($validator->fails()) {
            return CustomsResponse::error(
                $validator->errors()->toArray(),
                'The ppt field is required.',
                422
            );
        }

        $ppt = $request->file('ppt');
        $pptName = $ppt->getClientOriginalName(); // Get original file name
        $pptSize = $this->formatSizeUnits($ppt->getSize());
        $slug = Str::slug(pathinfo($pptName, PATHINFO_FILENAME)); // Generate slug from filename
        $filename = $slug . '.' . $ppt->getClientOriginalExtension(); // Generate filename using slug and original extension
        // Memeriksa apakah file dengan nama yang sama sudah ada
        if (Storage::exists('public/ppts/' . $filename)) {
            return response()->json([
                "status" => 400,
                'file_name' => $filename,
                'message' => 'File with the same name already exists.',
            ], 400);
        }

        $path = $ppt->storeAs('public/ppts', $filename); // Store file with generated filename

        return response()->json([
            "status" => 201,
            'message' => 'PPT uploaded successfully.',
            'path' => url(Storage::url($path)),
            'size_file' => $pptSize,
            'file_name' => $filename,
        ]);
    }

    public function downloadPDF($fileName): BinaryFileResponse
    {
        $file = Storage::disk('public')->path('pdfs/' . $fileName);

        return response()->download($file, $fileName);
    }

    public function downloadPPT($fileName): BinaryFileResponse
    {
        $file = Storage::disk('public')->path('ppts/' . $fileName);

        return response()->download($file, $fileName);
    }

    public function downloadImage($fileName): BinaryFileResponse
    {
        $file = Storage::disk('public')->path('images/' . $fileName);

        return response()->download($file, $fileName);
    }

    private function formatSizeUnits($bytes): string
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }


    public function listFiles(): JsonResponse
    {
        $ppts = Storage::files('public/ppts');
        $images = Storage::files('public/images');
        $pdfs = Storage::files('public/pdfs');

        $files = array_merge($ppts, $images, $pdfs);

        return response()->json([
            "status" => 200,
            "message" => "List of files retrieved successfully.",
            "files" => $files
        ]);
    }
}
