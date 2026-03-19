<?php

namespace App\Models;

use App\Traits\HasCreatorAndUpdater;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;

class File extends Model
{
    use HasCreatorAndUpdater, HasFactory,NodeTrait, SoftDeletes;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(File::class, 'parent_id');
    }

    public function owner(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $attributes['created_by'] == Auth::id() ? 'me' : $this->user->name;
            }
        );
    }

    public function isOwner($userId)
    {
        return $this->created_by == $userId;
    }

    public function isRoot()
    {
        return $this->parent_id === null;
    }

    public function getFileSize(){
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $power = $this->size > 0 ? floor(log($this->size, 1024)) : 0;
        return number_format($this->size / pow(1024, $power), 2, '.',','). ' '. $units[$power];
    }
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (! $model->parent) {
                return;
            }
            $model->path = (! $model->parent->isRoot() ? $model->parent->path.'/' : '').Str::slug($model->name);
        });

        static::deleted(function ($model) {
            if ($model->is_folder) {
                $model->children()->delete();
                Storage::deleteDirectory($model->storage_path);
            }
        });

    }
}
