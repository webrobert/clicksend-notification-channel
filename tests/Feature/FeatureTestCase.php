<?php

namespace Illuminate\Notifications\Tests\Feature;

use Illuminate\Notifications\ClickSendChannelServiceProvider;
use Orchestra\Testbench\TestCase;

abstract class FeatureTestCase extends TestCase
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [ClickSendChannelServiceProvider::class];
    }
}
