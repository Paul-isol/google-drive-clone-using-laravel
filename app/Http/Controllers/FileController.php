<?php

namespace App\Http\Controllers;

use App\Http\Requests\DestroyFilesRequest;
use App\Http\Requests\StoreFileRequest;
use App\Http\Requests\StoreFolderRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
class FileController extends Controller
{
    public function index(?string $folder = null)
    {
        if ($folder) {
            $folder = File::query()
                ->where('created_by', Auth::id())
                ->where('path', $folder)
                ->firstOrFail();
        }

        if (! $folder) {
            $folder = $this->getRoot();
        }
        $files = File::query()
            ->where('parent_id', $folder->id)
            ->where('created_by', Auth::id())
            ->orderBy('is_folder', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        $files = FileResource::collection($files);
        $ancestors = FileResource::collection([...$folder->ancestors, $folder]);
        $folder = new FileResource($folder);

        return inertia('Myfiles', [
            'files' => Inertia::scroll($files),
            'folder' => $folder,
            'ancestors' => $ancestors,
        ]);
    }

    public function upload(StoreFileRequest $request)
    {
        $data = $request->validated();
        $fileTree = $request->file_tree;
        $parent = $request->parent;
        $user = Auth::user();
        if (! $parent) {
            $parent = $this->getRoot();
        }
        if (! empty($fileTree)) {
            $this->saveFileTree($fileTree, $parent, $user);
        } else {
            foreach ($data['files'] as $file) {
                $path = $file->store('files/'.$user->id);
                $model = new File;
                $model->name = $file->getClientOriginalName();
                $model->storage_path = $path;
                $model->is_folder = 0;
                $model->mime = $file->getMimeType();
                $model->size = $file->getSize();
                $model->created_by = Auth::id();
                $model->updated_by = Auth::id();

                $parent->appendNode($model);
            }
        }

        // return redirect()->route('myfiles', ['folder' => $parent->path]);
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

        return redirect()->route('myfiles', ['folder' => $parent->path]);
    }

    public function destroy(DestroyFilesRequest $request) {
        $data = $request->validated();
        $parent = $request->parent;
        
        if($data['all']) {
            $children = $parent->children;
            foreach ($children as $child ){
                $child->delete();
            }
        } else {
            foreach($data['ids'] ?? [] as $id){
                $file = File::find($id);
                if($file){
                    $file->delete();
                }
            }
        }

        return redirect()->route('myfiles', ['folder' => $parent->path]);
    }
    private function getRoot(): File
    {
        return File::query()
            ->whereNull('parent_id')
            ->where('created_by', Auth::id())
            ->first() ?? $this->createRoot();
    }

    public function saveFileTree($fileTree, $parent, $user)
    {
        foreach ($fileTree as $name => $file) {
            if (is_array($file)) {
                $folder = new File;
                $folder->is_folder = 1;
                $folder->name = $name;
                $folder->created_by = Auth::id();
                $folder->updated_by = Auth::id();

                $parent->appendNode($folder);
                $this->saveFileTree($file, $folder, $user);
            } else {
                $path = $file->store('files/'.$user->id);
                $model = new File;
                $model->name = $name;
                $model->storage_path = $path;
                $model->is_folder = 0;
                $model->mime = $file->getMimeType();
                $model->size = $file->getSize();
                $model->created_by = Auth::id();
                $model->updated_by = Auth::id();

                $parent->appendNode($model);
            }
        }
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
