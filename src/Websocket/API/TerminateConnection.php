<?php

namespace Azim\Broadcaster\Websocket\API;

use Azim\Broadcaster\Websocket\API\CustomController;
use BeyondCode\LaravelWebSockets\Channels\Channel;
use BeyondCode\LaravelWebSockets\Contracts\ChannelManager;
use BeyondCode\LaravelWebSockets\Server\MockableConnection;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TerminateConnection extends CustomController
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return HttpException
     */
    public function __invoke(Request $request)
    {
        $appId = $request->appId;
        $userId = $request->userId;
        $socketId = null;
        $channelManager = app(ChannelManager::class);
        $channelsObj = $channelManager->getGlobalChannels($appId)
            ->then(function ($channels) {
                $channels = collect($channels)->keyBy(function ($channel) {
                    return $channel instanceof Channel
                        ? $channel->getName()
                        : $channel;
                });
                return $channels->map(function ($channel) {
                    return $channel instanceof Channel
                        ? $channel->getName()
                        : $channel;
                })->toArray();
            });
        $channelsArray = Arr::flatten(collect($channelsObj)->values()->toArray());
        if (count($channelsArray) == 0) {
            return new HttpException(400, "There is not valid channel");
        }
        foreach ($channelsArray as $channel) {
            $response = $channelManager->getMemberSockets($userId, $appId, $channel)
                ->then(function ($arg) {
                    return $arg;
                });
            $socketArray = Arr::flatten(collect($response)->values()->toArray());
            if (count($socketArray) > 0) {
                $socketId = $socketArray[0];
                break;
            }
        }
        if (empty($socketId)) {
            return new HttpException(400, "A user with user ID `{$userId}` has not yet joined to the channel");
        }

        $connection = new MockableConnection($appId, $socketId);
        $result = $channelManager->unsubscribeFromAllChannels($connection)
            ->then(function ($arg) {
                return $arg;
            });

        return $result;
    }
}
