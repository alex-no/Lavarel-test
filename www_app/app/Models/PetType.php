<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetType extends Model
{
    protected $table = 'pet_type'; // Specify the table explicitly

    /** @use HasFactory<\Database\Factories\PetTypeFactory> */
    use HasFactory;
}
