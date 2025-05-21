<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class DevelopmentPlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sort_order' => $this->sort_order,
            'status' => $this->status,
            'feature' => $this->{'@@feature'},
            'technology' => $this->{'@@technology'},
            'result' => $this->{'@@result'},
            'status_adv' => self::makeStatusAdv($this->status),
            'updated' => Carbon::parse($this->updated_at)->format('d.m.Y H:i'),
        ];
    }
}
