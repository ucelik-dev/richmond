<?php

namespace App\Http\Controllers\Frontend\Student;

use App\Http\Controllers\Controller;
use App\Models\DocumentCategory;
use App\Models\UserDocument;
use App\Traits\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Str;

class StudentDocumentController extends Controller
{
    use FileUpload;
    
    function index() {
        $student = Auth::user();
        $documentCategories = DocumentCategory::where(['status' => 1, 'role_id' => 2])->get();
        return view ('frontend.student.document.index', compact('student','documentCategories'));
    }

    function updateDocument(Request $request) {

        $user = Auth::user();

        foreach ($request->input('documents', []) as $index => $docInput) {
            if ($request->hasFile("documents.$index.file")) {
                $file = $request->file("documents.$index.file");
                $categoryId = $docInput['category_id'];

                $category = DocumentCategory::find($categoryId);
                $categorySlug = Str::slug($category->name); // Import Str if needed
                $filename = $categorySlug . '_' . uniqid();

                // Get existing document to delete old file
                $existingDoc = UserDocument::where('user_id', $user->id)
                                        ->where('category_id', $categoryId)
                                        ->first();

                if ($existingDoc && $existingDoc->path) {
                    $this->deleteFile($existingDoc->path); // Uses FileUpload trait
                }

                $path = $this->uploadFile($file, 'uploads/user-documents', $filename);

                UserDocument::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'category_id' => $categoryId,
                    ],
                    [
                        'path' => $path,
                    ]
                );
            }
        }
        

        notyf()->success('Document uploaded successfully!');
        return redirect()->back();
    }

}
