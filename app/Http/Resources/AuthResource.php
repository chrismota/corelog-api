<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'access_token' => $this['access_token'],
            'token_type' => $this['token_type'],
            'expires_in' => $this['expires_in'],
        ];
    }
}
