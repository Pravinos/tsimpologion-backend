<?php

namespace App\Traits;

trait HasImages
{
    /**
     * Get the folder name for storing images.
     *
     * @return string
     */
    public function getImageFolder(): string
    {
        return strtolower(class_basename($this)) . 's'; // e.g., "food-spots", "users", "reviews"
    }

    /**
     * Check if a user can manage images for this model.
     *
     * @param \App\Models\User $user
     * @return bool
     */
    public function userCanManageImages($user): bool
    {
        // Default implementation, override in models as needed
        return $user->can('update', $this);
    }
}
