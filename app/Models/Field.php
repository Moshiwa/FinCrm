<?php

namespace App\Models;

use App\Traits\SettingableTrait;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Field extends Model
{
    use CrudTrait;
    use HasFactory;
    use SettingableTrait;

    public $timestamps = false;

    protected $guarded = ['id'];

    protected $casts = [
        'options' => 'array'
    ];

    public function clients()
    {
        return $this->morphedByMany(Client::class, 'fieldable')
            ->withPivot('is_enable');
    }

    public function deals()
    {
        return $this->morphedByMany(Deal::class, 'fieldable')
            ->withPivot('is_enable');
    }

    protected static function booted()
    {
        static::created(function (self $field) {
            $settings = Setting::query()->select('id')->where('type', 'field')->pluck('id');
            $field->settings()->sync($settings);

            $field->clients()->attach(Client::query()->select('id')->pluck('id')->toArray());
            $field->deals()->attach(Deal::query()->select('id')->pluck('id')->toArray());
        });
    }
}
