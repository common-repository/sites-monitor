<?php
/**
 * @license MIT
 *
 * Modified by verdantstudio on 27-October-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace WPSitesMonitor\Vendor_Prefixed\DI\Definition;

use WPSitesMonitor\Vendor_Prefixed\Psr\Container\ContainerInterface;

/**
 * Describes a definition that can resolve itself.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
interface SelfResolvingDefinition
{
    /**
     * Resolve the definition and return the resulting value.
     *
     * @return mixed
     */
    public function resolve(ContainerInterface $container);

    /**
     * Check if a definition can be resolved.
     */
    public function isResolvable(ContainerInterface $container) : bool;
}
