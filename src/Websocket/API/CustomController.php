<?php

namespace Azim\Broadcaster\Websocket\API;

use BeyondCode\LaravelWebSockets\API\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Pusher\Pusher;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class CustomController extends Controller
{
    protected function ensureValidSignature(Request $request): CustomController|static
    {
        $params = Arr::except($request->query(), [
            'auth_signature', 'body_md5', 'appId', 'appKey', 'userId', 'channelName'
        ]);

        if ($request->getContent() !== '') {
            $params['body_md5'] = md5($request->getContent());
        }

        ksort($params);

        $signature = "{$request->getMethod()}\n/{$request->path()}\n".Pusher::array_implode('=', '&', $params);

        $authSignature = hash_hmac('sha256', $signature, $this->app->secret);

        if ($authSignature !== $request->get('auth_signature')) {
            throw new HttpException(401, 'Invalid auth signature provided.');
        }

        return $this;
    }

}
