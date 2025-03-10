<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $table = 'language'; // Specify the table explicitly

    /** @use HasFactory<\Database\Factories\LanguageFactory> */
    use HasFactory;
}
