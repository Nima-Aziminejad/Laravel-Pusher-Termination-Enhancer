<?php

namespace Azim\Broadcaster\Websocket\API;

use BeyondCode\LaravelWebSockets\Contracts\ChannelManager;
use BeyondCode\LaravelWebSockets\Server\MockableConnection;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TerminateChannel extends CustomController
{

    public function __invoke(Request $request)
    {

        $appId = $request->appId;
        $userId = $request->userId;
        $channel = $request->channelName;
        $socketId = null;
        $channelManager = app(ChannelManager::class);
        if ($channelManager->find($appId, $channel)) {
            $response = $channelManager->getMemberSockets($userId, $appId, $channel)
                ->then(function ($arg) {
                    return $arg;
                });
            $socketArray = Arr::flatten(collect($response)->values()->toArray());
            if (count($socketArray) > 0) {
                $socketId = $socketArray[0];
                $connection = new MockableConnection($appId, $socketId);
                $payload = [
                    'channel' => $channel,
                ];
                $result = $channelManager->unsubscribeFromChannel($connection,$channel,(object)$payload)
                    ->then(function ($arg) {
                    return $arg;
                });
                return $result;
            } else {
                return new HttpException(400, "A user with user ID `{$userId}` has not yet joined to the channel");
            }
        } else {
            return new HttpException(400, "There is not valid channel");
        }
    }
}
