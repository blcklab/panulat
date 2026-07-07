<?php

declare(strict_types=1);

namespace App\Resources;

use Panulat\Resource\JsonResource;

final readonly class UserResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $user = is_array($this->resource) ? $this->resource : [];

        $data = [
            'id' => $user['id'] ?? null,
            'name' => $user['name'] ?? null,
            'email' => $user['email'] ?? null,
            'role' => $user['role'] ?? null,
        ];

        if (array_key_exists('profile_id', $user)) {
            $data['profile'] = $user['profile_id'] === null ? null : [
                'id' => $user['profile_id'],
                'avatar' => $user['profile_avatar'] ?? null,
                'bio' => $user['profile_bio'] ?? null,
            ];
        }

        return $data;
    }
}
