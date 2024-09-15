<?php

namespace Akbardwi\Laratheme\Tests;

use GrahamCampbell\TestBench\AbstractPackageTestCase;
use Akbardwi\Laratheme\Providers\LarathemeServiceProvider;

/**
 * This is the abstract test case class.
 */
abstract class TestCase extends AbstractPackageTestCase
{
    /**
     * Get the service provider class.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return string
     */
    protected function getServiceProviderClass($app)
    {
        return LarathemeServiceProvider::class;
    }
}
