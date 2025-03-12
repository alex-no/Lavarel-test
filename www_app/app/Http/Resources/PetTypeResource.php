<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Resources\Json\JsonResource;

class PetTypeResource extends JsonResource
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
            'name' => $this->{'name_' . $language},
            'updated_at' => $this->updated_at,
        ];
    }
}
