<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('optimize:clear');

        Artisan::call('migrate', ['--seed' => true]);
        Artisan::call('passport:install'); //al rehacer migraciones se borran las keys

    }
}
