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
    ) {
        $tripList = array();
        $loggedUser = $userSession->getLoggedUser();
        $isFriend = false;
        if ($loggedUser != null) {
            foreach ($user->getFriends() as $friend) {
                if ($friend == $loggedUser) {
                    $isFriend = true;
                    break;
                }
            }
            if ($isFriend) {
                $tripList = $tripRepository->findTripsByUser($user);
            }
            return $tripList;
        } else {
            throw new UserNotLoggedInException();
        }
    }
}
