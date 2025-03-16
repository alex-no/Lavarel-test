<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetBreed extends Model
{
    /** @use HasFactory<\Database\Factories\PetBreedFactory> */
    use HasFactory;

    protected $fillable = ['pet_type_id', 'name_uk', 'name_en', 'name_ru'];
}
