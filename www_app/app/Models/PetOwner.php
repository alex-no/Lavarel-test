<?php

namespace App\Models;

use App\Models\Base\AdvModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\PetBreed;
use Illuminate\Support\Facades\Auth;

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

    /**
     * Correctly set the user_id and pet_type_id before saving the model.
     * @param array $options
     * @return bool
     * @throws \Exception
     */
    public function save(array $options = []): bool
    {
        $dirty = $this->getDirty();

        if (empty($this->user_id)) {
            $this->user_id = Auth::user()->id;
        }

        if (isset($dirty['pet_breed_id'])) {
            $petBreed = PetBreed::find($this->pet_breed_id);
            if ($petBreed) {
                $this->pet_type_id = $petBreed->pet_type_id;
            }
        }

        return parent::save($options);
    }

}
