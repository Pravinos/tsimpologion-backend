<?php
namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Review $review): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Review $review): bool
    {
        // Only the author can update their review
        return $user->id === $review->user_id;
    }

    public function delete(User $user, Review $review): bool
    {
        // Author can delete their review, admin can delete any review
        return $user->id === $review->user_id || $user->isAdmin();
    }

    public function moderate(User $user, Review $review): bool
    {
        // Only admins can moderate (approve/disapprove) reviews
        return $user->isAdmin();
    }
}