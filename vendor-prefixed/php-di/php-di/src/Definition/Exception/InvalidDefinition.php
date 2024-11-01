<?php
/**
 * @license MIT
 *
 * Modified by verdantstudio on 27-October-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace WPSitesMonitor\Vendor_Prefixed\DI\Definition\Exception;

use WPSitesMonitor\Vendor_Prefixed\DI\Definition\Definition;
use WPSitesMonitor\Vendor_Prefixed\Psr\Container\ContainerExceptionInterface;

/**
 * Invalid DI definitions.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class InvalidDefinition extends \Exception implements ContainerExceptionInterface
{
    public static function create(Definition $definition, string $message, \Exception $previous = null) : self
    {
        return new self(sprintf(
            '%s' . \PHP_EOL . 'Full definition:' . \PHP_EOL . '%s',
            $message,
            (string) $definition
        ), 0, $previous);
    }
}
