<?php

namespace Illuminate\Notifications\Facades;

use Illuminate\Support\Facades\Facade;
use ClickSend\Api\SMSApi as Client;

class ClickSend extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() : string
    {
        return Client::class;
    }
}
