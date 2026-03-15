<?php

namespace App\Http\Requests;

use App\Models\File;
use Illuminate\Support\Facades\Auth;

class StoreFileRequest extends ParentIdBaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    protected function prepareForValidation(): void
    {
        $paths = array_filter($this->relative_path ?? [], fn ($f) => $f != null);
        $this->merge([
            'file_paths' => $paths,
            'folder_name' => $this->detectFolderName($paths),
        ]);
    }

    protected function passedValidation(): void
    {
        $data = $this->validated();
        $this->replace([
            'file_tree' => $this->buildFileTree($this->file_paths, $data['files'] ?? []),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'files.*' => [
                'required',
                'file',
                function ($attribute, $value, $fail) {
                    if (! $this->folder_name) {
                        $file = File::query()->where('name', $value->getClientOriginalName())
                            ->where('parent_id', $this->parent_id ?: File::whereNull('parent_id')
                                ->where('created_by', Auth::id())
                                ->value('id'))
                            ->where('created_by', Auth::id())
                            ->whereNull('deleted_at')
                            ->exists();

                        if ($file) {
                            $fail('File '.$value->getClientOriginalName().' already exists');
                        }
                    }
                },
            ],
            'folder_name' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $file = File::query()->where('name', $value)
                            ->where('parent_id', $this->parent_id ?: File::whereNull('parent_id')
                                ->where('created_by', Auth::id())
                                ->value('id'))
                            ->where('created_by', Auth::id())
                            ->whereNull('deleted_at')
                            ->exists();

                        if ($file) {
                            $fail('File '.$value.' already exists');
                        }
                    }
                },
            ],
        ]);
    }

    public function detectFolderName($paths)
    {
        if (! $paths) {
            return null;
        }
        $firstPath = $paths[0];

        return explode('/', $firstPath)[0];
    }

    private function buildFileTree($paths, $files)
    {
        $paths = array_slice($paths, 0, count($files));
        $paths = array_filter($paths, fn ($f) => $f != null);
        $tree = [];

        foreach ($paths as $index => $path) {
            $parts = explode('/', $path);
            $currentNode = &$tree;

            foreach ($parts as $key => $part) {
                if (! isset($currentNode[$part])) {
                    $currentNode[$part] = [];
                }

                if ($key === count($parts) - 1) {
                    $currentNode[$part] = $files[$index];
                } else {
                    $currentNode = &$currentNode[$part];
                }
            }
        }

        return $tree;
    }
}
