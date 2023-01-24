<?php

namespace Illuminate\Notifications;

use Illuminate\Notifications\Channels\ClickSendSmsChannel;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;
use ClickSend\Api\SMSApi as Client;
use RuntimeException;

class ClickSendChannelServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/clicksend.php', 'clicksend');

        $this->app->singleton(Client::class, function ($app) {
            $config = $app['config']['clicksend'];

            $httpClient = null;

            if ($httpClient = $config['http_client'] ?? null) {
                $httpClient = $app->make($httpClient);
            } elseif (! class_exists('GuzzleHttp\Client')) {
                throw new RuntimeException(
                    'The ClickSend client requires a "psr/http-client-implementation" class such as Guzzle.'
                );
            }

            return ClickSend::make($app['config']['clicksend'], $httpClient)->client();
        });

        $this->app->bind(ClickSendSmsChannel::class, function ($app) {
            return new ClickSendSmsChannel(
                $app->make(Client::class),
                $app['config']['clicksend.sms_from']
            );
        });

        Notification::resolved(function (ChannelManager $service) {
            $service->extend('clicksend', function ($app) {
                return $app->make(ClickSendSmsChannel::class);
            });
        });
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/clicksend.php' => $this->app->configPath('clicksend.php'),
            ], 'clicksend');
        }
    }
}
