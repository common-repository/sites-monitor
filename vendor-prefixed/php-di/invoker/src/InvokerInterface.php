<?php
/**
 * @license MIT
 *
 * Modified by verdantstudio on 27-October-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */ declare(strict_types=1);

namespace WPSitesMonitor\Vendor_Prefixed\Invoker;

use WPSitesMonitor\Vendor_Prefixed\Invoker\Exception\InvocationException;
use WPSitesMonitor\Vendor_Prefixed\Invoker\Exception\NotCallableException;
use WPSitesMonitor\Vendor_Prefixed\Invoker\Exception\NotEnoughParametersException;

/**
 * Invoke a callable.
 */
interface InvokerInterface
{
    /**
     * Call the given function using the given parameters.
     *
     * @param callable|array|string $callable Function to call.
     * @param array $parameters Parameters to use.
     * @return mixed Result of the function.
     * @throws InvocationException Base exception class for all the sub-exceptions below.
     * @throws NotCallableException
     * @throws NotEnoughParametersException
     */
    public function call($callable, array $parameters = []);
}
