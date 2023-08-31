<?php

namespace Azim\Broadcaster\Broadcasting;

use Azim\Broadcaster\Pusher\CustomPusher;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Broadcasting\BroadcastManager;
use Psr\Log\LoggerInterface;

class BroadcastManagerWrapper extends BroadcastManager
{
    /**
     * @throws \Pusher\PusherException
     */
    public function pusher(array $config)
    {
        $pusher = new CustomPusher(
            $config['key'],
            $config['secret'],
            $config['app_id'],
            $config['options'] ?? [],
            isset($config['client_options']) && ! empty($config['client_options'])
                ? new GuzzleClient($config['client_options'])
                : null,
        );

        if ($config['log'] ?? false) {
            $pusher->setLogger($this->app->make(LoggerInterface::class));
        }

        return $pusher;
    }
}
