<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use SoftDeletes;

    protected $fillable = ['path', 'filename', 'mime_type', 'size', 'fileable_id', 'fileable_type', 'uploaded_by'];

    public function fileable()
    {
        return $this->morphTo();
    }
}
