<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Base\AdvModel;

class PetBreed extends AdvModel
{
    /** @use HasFactory<\Database\Factories\PetBreedFactory> */
    use HasFactory;

    protected $fillable = ['pet_type_id', 'name_uk', 'name_en', 'name_ru'];
}
