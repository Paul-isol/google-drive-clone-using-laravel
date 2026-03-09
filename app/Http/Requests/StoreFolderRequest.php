<?php

namespace App\Http\Requests;

use App\Models\File;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreFolderRequest extends ParentIdBaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(),
            [
                'name' => [
                    'required',
                    'max:255',
                    Rule::unique(File::class, 'name')
                        ->where(function (Builder $query) {
                            $parentId = $this->parent_id ?: File::whereNull('parent_id')
                                ->where('created_by', Auth::id())
                                ->value('id');

                            $query->where('created_by', Auth::id())
                                ->where('parent_id', $parentId)
                                ->whereNull('deleted_at');
                        }),
                ],
            ],
        );
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Folder ":input" already exists',
        ];
    }
}
