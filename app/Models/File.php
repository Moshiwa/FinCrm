<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = [
        'full_path'
    ];

    protected $fillable = [
        'type',
        'size',
        'meme',
        'path'
    ];

    public function getFullPathAttribute()
    {
        return '/storage/'.$this->path;
    }

    protected static function booted()
    {
        static::deleting(function (self $file) {
            if(Storage::disk('public')->exists($file->path)){
                Storage::disk('public')->delete($file->path);
            }else{
                dd('File does not exists.');
            }
        });
    }
}
