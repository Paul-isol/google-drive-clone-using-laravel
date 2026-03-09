<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFolderRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
use Illuminate\Support\Facades\Auth;

class FileController extends Controller
{
    public function index()
    {
        $folder = $this->getRoot();
        $files = File::query()
            ->where('parent_id', $folder->id)
            ->where('created_by', Auth::id())
            ->orderBy('is_folder', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        $files = FileResource::collection($files);

        return inertia('Myfiles', [
            'files' => $files,
        ]);
    }

    public function createFolder(StoreFolderRequest $request)
    {
        $data = $request->validated();
        $parent = $request->parent;
        if (! $parent) {
            $parent = $this->getRoot();
        }
        $file = new File;
        $file->is_folder = 1;
        $file->name = $data['name'];
        $file->created_by = Auth::id();
        $file->updated_by = Auth::id();

        $parent->appendNode($file);
        $file->save();

        return redirect()->route('myfiles');
    }

    private function getRoot(): File
    {
        return File::query()
            ->whereNull('parent_id')
            ->where('created_by', Auth::id())
            ->first() ?? $this->createRoot();
    }

    private function createRoot(): File
    {
        $root = new File;
        $root->name = Auth::user()->name."'s Drive";
        $root->is_folder = 1;
        $root->created_by = Auth::id();
        $root->updated_by = Auth::id();
        $root->saveAsRoot();

        return $root;
    }
}
