<?php

namespace Test\TripServiceKata\Trip;

use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use TripServiceKata\Exception\UserNotLoggedInException;
use TripServiceKata\Trip\TripRepository;
use TripServiceKata\Trip\TripService;
use TripServiceKata\User\User;
use TripServiceKata\User\UserSession;

class TripServiceTest extends TestCase
{
    private TripService $tripService;

    /**
     * @var MockInterface&TripRepository
     */
    private $tripRepository;

    /**
     * @var MockInterface&UserSession
     */
    private $userSession;

    private const USER_NAME = 'Quon Tama';
    private const LOG_USER_NAME = 'Itou Yuna';
    private const FRIEND_USER_NAME = 'Matori Kakeru';
    private const TRIP = 'Guild CQ';

    protected function setUp()
    {
        $this->tripRepository = Mockery::mock(TripRepository::class);
        $this->userSession = Mockery::mock(UserSession::class);

        $this->tripService = new TripService();
    }

    /**
     * @test
     */
    public function userNotLog(): void
    {
        $this->userSession->shouldReceive('getLoggedUser')
            ->andReturnNull();
        
        $this->expectException(UserNotLoggedInException::class);

        $this->tripService->getTripsByUser(
            new User(self::USER_NAME),
            $this->userSession,
            $this->tripRepository
        );
    }

    /**
     * @test
     */
    public function noFriend(): void
    {
        $loginUser = new User(self::LOG_USER_NAME);
        $user = new User(self::USER_NAME);

        $this->userSession->shouldReceive('getLoggedUser')
            ->andReturn($loginUser);

        $result = $this->tripService->getTripsByUser(
            $user,
            $this->userSession,
            $this->tripRepository
        );

        $this->assertEquals([], $result);
    }

    /**
     * @test
     */
    public function FriendNotLogIn(): void
    {
        $loginUser = new User(self::LOG_USER_NAME);
        $user = new User(self::USER_NAME);
        $friend = new User(self::FRIEND_USER_NAME);

        $user->addFriend($friend);

        $this->userSession->shouldReceive('getLoggedUser')
            ->andReturn($loginUser);

        $result = $this->tripService->getTripsByUser(
            $user,
            $this->userSession,
            $this->tripRepository
        );

        $this->assertEquals([], $result);
    }

    /**
     * @test
     */
    public function FriendLogIn(): void
    {
        $loginUser = new User(self::LOG_USER_NAME);
        $user = new User(self::USER_NAME);
        $friend = new User(self::FRIEND_USER_NAME);

        $user->addFriend($friend);
        $user->addFriend($loginUser);

        $this->userSession->shouldReceive('getLoggedUser')
            ->andReturn($loginUser);
        
        $this->tripRepository->shouldReceive('findTripsByUser')
            ->with($user)
            ->andReturn([self::TRIP]);

        $result = $this->tripService->getTripsByUser(
            $user,
            $this->userSession,
            $this->tripRepository
        );

        $this->assertEquals([self::TRIP], $result);
    }
}
