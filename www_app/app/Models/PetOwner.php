<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Base\AdvModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PetOwner extends AdvModel
{
    /** @use HasFactory<\Database\Factories\PetOwnerFactory> */
    use HasFactory;
    protected $fillable = ['nickname_uk', 'nickname_en', 'nickname_ru'];

    /**
     *
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     *
     */
    public function petType(): BelongsTo
    {
        return $this->belongsTo(PetType::class, 'pet_type_id');
    }

    /**
     *
     */
    public function petBreed(): BelongsTo
    {
        return $this->belongsTo(PetBreed::class, 'pet_breed_id');
    }
}
