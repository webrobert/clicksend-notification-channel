<?php

namespace Illuminate\Notifications\Channels;

use Illuminate\Notifications\Messages\ClickSendMessage;
use ClickSend\Api\SMSApi as ClickSendClient;
use Illuminate\Notifications\Notification;
use ClickSend\Model\SmsMessageCollection;
use ClickSend\Model\SmsMessage;

class ClickSendSmsChannel
{
    /**
     * The ClickSend client instance.
     *
     * @var \ClickSend\Api\SMSApi
     */
    protected $client;

    /**
     * The phone number notifications should be sent from.
     *
     * @var string
     */
    protected $from;

    /**
     * Create a new ClickSend channel instance.
     *
     * @param  \ClickSend\Api\SMSApi  $client
     * @param  string  $from
     * @return void
     */
    public function __construct(ClickSendClient $client, $from)
    {
        $this->from = $from;
        $this->client = $client;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification $notification
     * @return mixed
     */
    public function send($notifiable, Notification $notification)
    {
        if (! $to = $notifiable->routeNotificationFor('clicksend', $notification)) {
            return;
        }

        $message = $notification->toClickSend($notifiable);

        if (is_string($message)) {
            $message = new ClickSendMessage($message);
        }

        $clickSendSms = new SmsMessage([
            'to' => $to,
            'body' => trim($message->content),
            'from' => $message->from ?: $this->from,
            'source' => 'laravel'
        ]);

        $clickSendSms->setCustomString($message->clientReference);

        $sms_messages = new SmsMessageCollection();
        $sms_messages->setMessages([$clickSendSms]);

        return ($message->client ?? $this->client)->smsSendPost($sms_messages);
    }
}
