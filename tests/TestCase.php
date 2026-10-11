<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        // The tests rebuild the database. Refuse to run against anything but
        // the in-memory SQLite database from phpunit.xml, so they can never
        // wipe the demo content.
        if (env('DB_CONNECTION') !== 'sqlite' || env('DB_DATABASE') !== ':memory:') {
            throw new RuntimeException('Tests must run on the in-memory SQLite database (see phpunit.xml).');
        }

        parent::setUp();
    }
}
