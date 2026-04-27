<?php

namespace Modules\HRCore\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ManagerResource",
 *     type="object",
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="employee_code", type="string", example="EMP-001"),
 *     @OA\Property(property="full_name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email"),
 *     @OA\Property(property="designation", type="string", example="Team Lead"),
 *     @OA\Property(property="profile_picture", type="string", nullable=true)
 * )
 */
class ManagerResource extends JsonResource
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
            'employee_code' => $this->code,
            'full_name' => $this->getFullName(),
            'email' => $this->email,
            'designation' => $this->whenLoaded('designation', function () {
                return $this->designation->name;
            }),
            'profile_picture' => $this->getProfilePicture(),
        ];
    }
}
