<?php

namespace Illuminate\Notifications\Tests\Feature;

use ClickSend\Api\SMSApi as Client;

class ClientBasicAPICredentialsTest extends FeatureTestCase
{
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('clicksend.api_key', 'my_api_key');
        $app['config']->set('clicksend.api_secret', 'my_secret');
    }

    public function testClientCreatedWithBasicAPICredentials()
    {
        $credentials = $this->app->make(Client::class)->getConfig();

        $this->assertEquals(['api_key' => 'my_api_key', 'api_secret' => 'my_secret'], $credentials->asArray());
    }
}
