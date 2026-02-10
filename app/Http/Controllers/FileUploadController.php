<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileUploadController extends Controller
{
   
    public function index()
    {
        return view('upload');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'files' => 'required',
            'files.*' => 'file|mimes:pdf,txt|max:10240',
        ], 
        [
            'files.required' => 'Please select at least one file to upload.',
            'files.*.mimes' => 'Each file must be a PDF or TXT file.',
            'files.*.max' => 'Each file must not exceed 10MB.',
        ]);

        try {
            $uploadedFiles = [];
            $failedFiles = [];
            
            foreach ($request->file('files') as $file) {
                $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                
                try {
                    Storage::disk('b2')->putFileAs(
                        'uploads',
                        $file,
                        $filename
                    );
                    
                    $uploadedFiles[] = $filename;
                    
                } catch (\Exception $e) {
                    $failedFiles[] = $file->getClientOriginalName() . ' (Error: ' . $e->getMessage() . ')';
                }
            }

            $message = '';
            if (count($uploadedFiles) > 0) {
                $message = count($uploadedFiles) . ' file(s) uploaded successfully!';
            }
            
            if (count($failedFiles) > 0) {
                $errorMsg = count($failedFiles) . ' file(s) failed to upload: ' . implode(', ', $failedFiles);
                if ($message) {
                    return back()
                        ->with('success', $message)
                        ->with('uploaded_files', $uploadedFiles)
                        ->with('error', $errorMsg);
                } else {
                    return back()->with('error', $errorMsg);
                }
            }

            return back()
                ->with('success', $message)
                ->with('uploaded_files', $uploadedFiles);

        } catch (\Exception $e) {
            return back()->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }

    public function list()
    {
        try {
            $files = Storage::disk('b2')->files('uploads');
            return view('files', compact('files'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }


    public function download($filename)
    {
        try {
            $path = 'uploads/' . $filename;
            
            if (Storage::disk('b2')->exists($path)) {
                return Storage::disk('b2')->download($path);
            }
            
            return back()->with('error', 'File not found');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function delete($filename)
    {
        try {
            $path = 'uploads/' . $filename;
            
            if (Storage::disk('b2')->exists($path)) {
                Storage::disk('b2')->delete($path);
                return back()->with('success', 'File deleted!');
            }
            
            return back()->with('error', 'File not found');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }


    public function bulkDelete(Request $request)
    {
        $files = json_decode($request->input('files'), true);
    
        if (!$files || !is_array($files)) {
        return redirect()->back()->with('error', 'No files selected.');
        }

        $deletedCount = 0;
    
        try {
            foreach ($files as $filename) {
                $path = 'uploads/' . $filename;
            
                if (Storage::disk('b2')->exists($path)) {
                    Storage::disk('b2')->delete($path);
                    $deletedCount++;
                }
            }   
        
            if ($deletedCount > 0) {
                return redirect()->back()->with('success', "{$deletedCount} file(s) deleted successfully.");
            } else {
                return redirect()->back()->with('error', 'No files were deleted.');
            }
        
        } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Delete failed: ' . $e->getMessage());
        }
    }
}