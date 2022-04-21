<?php

namespace TripServiceKata\Trip;

use TripServiceKata\User\User;
use TripServiceKata\User\UserSession;
use TripServiceKata\Exception\UserNotLoggedInException;

class TripService
{
    public function getTripsByUser(
        User $user,
        UserSession $userSession,
        TripRepository $tripRepository
    ): array {
        return in_array($this->getLoggedUserFromSession($userSession), $user->getFriends())
            ? $tripRepository->findTripsByUser($user)
            : [];
    }

    private function getLoggedUserFromSession(UserSession $userSession): User
    {
        $user = $userSession->getLoggedUser();
        if ($user === null) {
            throw new UserNotLoggedInException();
        }

        return $user;
    }
}
