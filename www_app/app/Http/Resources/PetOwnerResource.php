<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Resources\Json\JsonResource;

class PetOwnerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $language = App::getLocale();
        return [
            'id' => $this->id,
            'users_id' => $this->users_id,
            'owner' => $this->user->name,

            'pet_type_id' => $this->pet_type_id,
            'type' => $this->petType->{'name_' . $language},

            'pet_breed_id' => $this->pet_breed_id,
            'breed' => $this->petBreed->{'name_' . $language},
            
            'nickname' => $this->{'nickname_' . $language},
            'year_of_birth' => $this->year_of_birth,
            'age' => date('Y') - $this->year_of_birth,
            'updated_at' => $this->updated_at,
        ];
    }
}
