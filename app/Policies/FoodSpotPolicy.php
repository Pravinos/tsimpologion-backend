<?php
// filepath: /C:/tsimpologion-app/tsimpologion-backend/app/Policies/FoodSpotPolicy.php
namespace App\Policies;

use App\Models\FoodSpot;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FoodSpotPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, FoodSpot $foodSpot): bool
    {
        return true; 
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isSpotOwner();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FoodSpot $foodSpot): bool
    {
        return $user->isAdmin() || 
               ($user->isSpotOwner() && $user->id === $foodSpot->owner_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FoodSpot $foodSpot): bool
    {
        return $user->isAdmin() || 
               ($user->isSpotOwner() && $user->id === $foodSpot->owner_id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, FoodSpot $foodSpot): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, FoodSpot $foodSpot): bool
    {
        return $user->isAdmin();
    }
}