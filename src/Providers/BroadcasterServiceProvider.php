<?php

namespace Azim\Broadcaster\Providers;

use Azim\Broadcaster\Broadcasting\Broadcasters\CustomPusherBroadcaster;
use Azim\Broadcaster\Broadcasting\BroadcastManagerWrapper;
use Azim\Broadcaster\Websocket\API\TerminateChannel;
use Azim\Broadcaster\Websocket\API\TerminateConnection;
use Illuminate\Broadcasting\BroadcastManager;
use BeyondCode\LaravelWebSockets\Server\Router;
use Illuminate\Support\ServiceProvider;

class BroadcasterServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->extend('websockets.router',function (Router $router){
            $router->post('/apps/{appId}/users/{userId}/terminate_connections',
                TerminateConnection::class);
            $router->post('/apps/{appId}/users/{userId}/channel/{channelName}/terminate_channel',
                TerminateChannel::class);
            return $router;
        });
    }

    public function register()
    {
        $this->app->extend(BroadcastManager::class, function ($service, $app) {
            return new BroadcastManagerWrapper($app);
        });
        $this->app->singleton('CustomBroadcast', function ($app) {
            $arg = $app->make(BroadcastManager::class);
            $pusher = $arg->pusher(config("broadcasting.connections.pusher"));
            return new CustomPusherBroadcaster($pusher);
        });
    }
}
