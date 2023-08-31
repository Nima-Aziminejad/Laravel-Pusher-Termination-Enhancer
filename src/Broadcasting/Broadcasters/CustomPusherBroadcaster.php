<?php

namespace Azim\Broadcaster\Broadcasting\Broadcasters;

use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Broadcasting\Broadcasters\PusherBroadcaster;
use Pusher\Pusher;

class CustomPusherBroadcaster extends PusherBroadcaster
{
    public function terminateConnection(string $userId)
    {
        if(empty($userId)){
            throw new ('user id is not valid');
        }else{
            $this->pusher->terminateConnection($userId);
        }
    }

    public function terminateChannel(string $userId, string $channel)
    {
        if(empty($userId) || empty($channel)){
            throw new ('user id or channel name is not valid');
        }else{
            $this->pusher->terminateChannel($userId, $channel);
        }
    }

    public function subscribedUsers(string $channel): object
    {
        return $this->pusher->getPresenceUsers($channel);
    }
}
