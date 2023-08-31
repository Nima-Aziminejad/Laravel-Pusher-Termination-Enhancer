<?php

namespace Azim\Broadcaster\Facades;

use Illuminate\Support\Facades\Facade;
use Azim\Broadcaster\Broadcasting\Broadcasters\CustomPusherBroadcaster;

/**
 * @method static mixed subscribedUsers(string $channel)
 * @method static terminateConnection(string $userId)
 * @method static terminateChannel(string $userId, string $channel)
 * @see \Azim\Broadcaster\Broadcasting\Broadcasters\CustomPusherBroadcaster
 */
class CustomBroadcast extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'CustomBroadcast';
    }
}
