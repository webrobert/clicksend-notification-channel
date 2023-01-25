<?php

namespace Illuminate\Notifications\Tests\Unit\Channels;

use Illuminate\Notifications\Channels\ClickSendSmsChannel;
use Illuminate\Notifications\Messages\ClickSendMessage;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Notifiable;
use ClickSend\Model\SmsMessage as SMS;
use ClickSend\Api\SMSApi as Client;
use PHPUnit\Framework\TestCase;
use Hamcrest\Core\IsEqual;
use Mockery as m;

class ClickSendSmsChannelTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testSmsIsSentViaClickSend()
    {
        $notification = new NotificationClickSendSmsChannelTestNotification;
        $notifiable = new NotificationClickSendSmsChannelTestNotifiable;

        $channel = new ClickSendSmsChannel(
            $clickSend = m::mock(Client::class), '4444444444'
        );

        $mockSms = (new SMS([
            'to' => '5555555555',
            'from' =>'4444444444',
            'body' => 'this is my message',
            'source' => 'laravel'
        ]));

        $clickSend->shouldReceive('sms->send')
               ->with(IsEqual::equalTo($mockSms))
               ->once();

        $channel->send($notifiable, $notification);
    }

    public function testSmsIsSentViaClickSendWithCustomClient()
    {
        $customClickSend = m::mock(Client::class);
        $customClickSend->shouldReceive('sms->send')
             ->with(IsEqual::equalTo(new SMS([
                 'to' => '5555555555',
                 'from' =>'4444444444',
                 'body' => 'this is my message',
                 'source' => 'laravel'
             ])))
             ->once();

        $notification = new NotificationClickSendSmsChannelTestCustomClientNotification($customClickSend);
        $notifiable = new NotificationClickSendSmsChannelTestNotifiable;

        $channel = new ClickSendSmsChannel(
            $clickSend = m::mock(Client::class), '4444444444'
        );

        $clickSend->shouldNotReceive('sms->send');

        $channel->send($notifiable, $notification);
    }

    public function testSmsIsSentViaClickSendWithCustomFrom()
    {
        $notification = new NotificationClickSendSmsChannelTestCustomFromNotification;
        $notifiable = new NotificationClickSendSmsChannelTestNotifiable;

        $channel = new ClickSendSmsChannel(
            $clickSend = m::mock(Client::class), '4444444444'
        );

        $mockSms = (new SMS([
            'to' => '5555555555',
            'from' =>'5554443333',
            'body' => 'this is my message',
            'source' => 'laravel'
        ]));

        $clickSend->shouldReceive('sms->send')
               ->with(IsEqual::equalTo($mockSms))
               ->once();

        $channel->send($notifiable, $notification);
    }

    public function testSmsIsSentViaClickSendWithCustomFromAndClient()
    {
        $customClickSend = m::mock(Client::class);

        $mockSms = new SMS([
            'to' => '5555555555',
            'from' =>'5554443333',
            'body' => 'this is my message',
            'source' => 'laravel'
        ]);

        $customClickSend->shouldReceive('sms->send')
                     ->with(IsEqual::equalTo($mockSms))
                     ->once();

        $notification = new NotificationClickSendSmsChannelTestCustomFromAndClientNotification($customClickSend);
        $notifiable = new NotificationClickSendSmsChannelTestNotifiable;

        $channel = new ClickSendSmsChannel(
            $clickSend = m::mock(Client::class), '4444444444'
        );

        $clickSend->shouldNotReceive('sms->send');

        $channel->send($notifiable, $notification);
    }

    public function testSmsIsSentViaClickSendWithCustomFromAndCustomString()
    {
        $notification = new NotificationClickSendSmsChannelTestCustomFromAndClientRefNotification;
        $notifiable = new NotificationClickSendSmsChannelTestNotifiable;

        $channel = new ClickSendSmsChannel(
            $clickSend = m::mock(Client::class), '4444444444'
        );

        $mockSms = new SMS([
            'to' => '5555555555',
            'from' =>'5554443333',
            'body' => 'this is my message',
            'source' => 'laravel'
        ]);

        $mockSms->setCustomString('11');

        $clickSend->shouldReceive('sms->send')
               ->with(IsEqual::equalTo($mockSms))
               ->once();

        $channel->send($notifiable, $notification);
    }

    public function testSmsIsSentViaClickSendWithCustomClientFromAndClientRef()
    {
        $customClickSend = m::mock(Client::class);

        $mockSms = new SMS([
            'to' => '5555555555',
            'from' =>'5554443333',
            'body' => 'this is my message',
            'source' => 'laravel'
        ]);

        $mockSms->setCustomString('11');

        $customClickSend->shouldReceive('sms->send')
             ->with(IsEqual::equalTo($mockSms))
             ->once();

        $notification = new NotificationClickSendSmsChannelTestCustomClientFromAndClientRefNotification($customClickSend);
        $notifiable = new NotificationClickSendSmsChannelTestNotifiable;

        $channel = new ClickSendSmsChannel(
            $clickSend = m::mock(Client::class), '4444444444'
        );

        $clickSend->shouldNotReceive('sms->send');

        $channel->send($notifiable, $notification);
    }
}

class NotificationClickSendSmsChannelTestNotifiable
{
    use Notifiable;

    public $phone_number = '5555555555';

    public function routeNotificationForClickSend($notification)
    {
        return $this->phone_number;
    }
}

class NotificationClickSendSmsChannelTestNotification extends Notification
{
    public function toClickSend($notifiable)
    {
        return new ClickSendMessage('this is my message');
    }
}

class NotificationClickSendSmsChannelTestCustomClientNotification extends Notification
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function toClickSend($notifiable)
    {
        return (new ClickSendMessage('this is my message'))
            ->usingClient($this->client);
    }
}

class NotificationClickSendSmsChannelTestCustomFromNotification extends Notification
{
    public function toClickSend($notifiable)
    {
        return (new ClickSendMessage('this is my message'))
            ->from('5554443333')
            ->unicode();
    }
}

class NotificationClickSendSmsChannelTestCustomFromAndClientNotification extends Notification
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function toClickSend($notifiable)
    {
        return (new ClickSendMessage('this is my message'))
            ->from('5554443333')
            ->unicode()
            ->usingClient($this->client);
    }
}

class NotificationClickSendSmsChannelTestCustomFromAndClientRefNotification extends Notification
{
    public function toClickSend($notifiable)
    {
        return (new ClickSendMessage('this is my message'))
            ->from('5554443333')
            ->unicode()
            ->clientReference('11');
    }
}

class NotificationClickSendSmsChannelTestCustomClientFromAndClientRefNotification extends Notification
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function toClickSend($notifiable)
    {
        return (new ClickSendMessage('this is my message'))
            ->from('5554443333')
            ->unicode()
            ->clientReference('11')
            ->usingClient($this->client);
    }
}
