<?php

namespace Azim\Broadcaster\Pusher;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use JetBrains\PhpStorm\Pure;
use Pusher\ApiErrorException;
use Pusher\Pusher;
use Pusher\PusherException;

class CustomPusher extends Pusher
{
    /**
     * @var null|resource
     */
    private $client = null; // Guzzle client

    public function __construct(
        string $auth_key,
        string $secret,
        string $app_id,
        array $options = [],
        ClientInterface $client = null
    ) {
        if (!is_null($client)) {
            $this->client = $client;
        } else {
            $this->client = new \GuzzleHttp\Client();
        }
        parent::__construct($auth_key, $secret, $app_id, $options, $client);
    }

    /**
     * @throws PusherException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Pusher\ApiErrorException
     */
    public function terminateConnection(string $userId)
    {
        $request = $this->prepareRequest($userId);
        try {
            $response = $this->client->send($request, [
                'http_errors' => false,
                'base_uri' => $this->channelsUrl()
            ]);
        } catch (ConnectException $e) {
            throw new ApiErrorException($e->getMessage());
        }

        $status = $response->getStatusCode();
        if ($status !== 200) {
            $body = (string) $response->getBody();
            throw new ApiErrorException($body, $status);
        }
        try {
            $result = json_decode($response->getBody(), false, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new PusherException('Data encoding error.');
        }
        return $result;
    }

    public function terminateChannel($userId, $channel)
    {
        $request = $this->prepareRequest($userId,$channel);
        try {
            $response = $this->client->send($request, [
                'http_errors' => false,
                'base_uri' => $this->channelsUrl()
            ]);
        } catch (ConnectException $e) {
            throw new ApiErrorException($e->getMessage());
        }
        $status = $response->getStatusCode();
        if ($status !== 200) {
            $body = (string) $response->getBody();
            throw new ApiErrorException($body, $status);
        }
        try {
            $result = json_decode($response->getBody(), false, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new PusherException('Data encoding error.');
        }
        return $result;
    }

    public function prepareRequest($id, $channel = null): Request
    {
        $query_params = [];
        $setting = $this->getSettings();
        if (!is_null($channel)) {
            $path = $setting['base_path'].'/users/'.$id.'/channel/'.$channel.'/terminate_channel';
        } else {
            $path = $setting['base_path'].'/users/'.$id.'/terminate_connections';
        }

        $post_params = [];
        $post_params['name'] = '';
        $post_params['data'] = '';
        $post_params['channels'] = '';

        $all_params = $post_params;

        try {
            $post_value = json_encode($all_params, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new PusherException('Data encoding error.');
        }

        $query_params['body_md5'] = md5($post_value);

        $signature = self::build_auth_query_params(
            $setting['auth_key'],
            $setting['secret'],
            'POST',
            $path,
            $query_params
        );
        $headers = [
            'Content-Type' => 'application/json',
            'X-Pusher-Library' => 'pusher-http-php '.self::$VERSION
        ];

        $params = array_merge($signature, $query_params);
        $query_string = self::array_implode('=', '&', $params);
        $full_path = $path."?".$query_string;
        return new Request('POST', $full_path, $headers, $post_value);
    }

    #[Pure] private function channelsUrl(): string
    {
        $setting = $this->getSettings();
        return $setting['scheme'].'://'.$setting['host'].':'.$setting['port'].$setting['path'];
    }
}
