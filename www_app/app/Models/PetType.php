<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Base\AdvModel;

class PetType extends AdvModel
{
    /** @use HasFactory<\Database\Factories\PetTypeFactory> */
    use HasFactory;
    protected $fillable = [
        'name_uk',
        'name_en',
        'name_ru',
    ];
}
