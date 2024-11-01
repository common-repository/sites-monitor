<?php

use WPSitesMonitor\Controllers\Api\ApiController;
use WPSitesMonitor\Controllers\Api\MonitorApiController;
use WPSitesMonitor\Controllers\Api\SiteApiController;
use WPSitesMonitor\Controllers\BaseController;
use WPSitesMonitor\Controllers\SettingsController;
use WPSitesMonitor\Interfaces\Controllers\Api\ApiControllerInterface;
use WPSitesMonitor\Interfaces\Controllers\Api\MonitorApiControllerInterface;
use WPSitesMonitor\Interfaces\Controllers\Api\SiteApiControllerInterface;
use WPSitesMonitor\Interfaces\Controllers\BaseControllerInterface;
use WPSitesMonitor\Interfaces\Controllers\SettingsControllerInterface;
use WPSitesMonitor\Interfaces\Providers\ApiServiceProviderInterface;
use WPSitesMonitor\Interfaces\Providers\AppServiceProviderInterface;
use WPSitesMonitor\Interfaces\Providers\SettingsServiceProviderInterface;
use WPSitesMonitor\Interfaces\Services\EventServiceInterface;
use WPSitesMonitor\Interfaces\Services\LifeCycleServiceInterface;
use WPSitesMonitor\Interfaces\Services\ResourceServiceInterface;
use WPSitesMonitor\Providers\ApiServiceProvider;
use WPSitesMonitor\Providers\AppServiceProvider;
use WPSitesMonitor\Providers\SettingsServiceProvider;
use WPSitesMonitor\Services\EventService;
use WPSitesMonitor\Services\LifeCycleService;
use WPSitesMonitor\Services\ResourceService;

use WPSitesMonitor\Providers\MonitorServiceProvider;
use WPSitesMonitor\Interfaces\Providers\MonitorServiceProviderInterface;
use WPSitesMonitor\Providers\SiteServiceProvider;
use WPSitesMonitor\Interfaces\Providers\SiteServiceProviderInterface;

return array(
	// Controllers.
	BaseControllerInterface::class          => WPSitesMonitor\Vendor_Prefixed\DI\autowire( BaseController::class ),
	SettingsControllerInterface::class      => WPSitesMonitor\Vendor_Prefixed\DI\autowire( SettingsController::class ),
	// -- Api Controllers.
	ApiControllerInterface::class           => WPSitesMonitor\Vendor_Prefixed\DI\autowire( ApiController::class ),
	MonitorApiControllerInterface::class    => WPSitesMonitor\Vendor_Prefixed\DI\autowire( MonitorApiController::class ),
	SiteApiControllerInterface::class       => WPSitesMonitor\Vendor_Prefixed\DI\autowire( SiteApiController::class ),

	// Providers.
	ApiServiceProviderInterface::class      => WPSitesMonitor\Vendor_Prefixed\DI\autowire( ApiServiceProvider::class ),
	AppServiceProviderInterface::class      => WPSitesMonitor\Vendor_Prefixed\DI\autowire( AppServiceProvider::class ),
	MonitorServiceProviderInterface::class  => WPSitesMonitor\Vendor_Prefixed\DI\autowire( MonitorServiceProvider::class ),
	SettingsServiceProviderInterface::class => WPSitesMonitor\Vendor_Prefixed\DI\autowire( SettingsServiceProvider::class ),
	SiteServiceProviderInterface::class     => WPSitesMonitor\Vendor_Prefixed\DI\autowire( SiteServiceProvider::class ),

	// Services.
	EventServiceInterface::class            => WPSitesMonitor\Vendor_Prefixed\DI\autowire( EventService::class ),
	LifeCycleServiceInterface::class        => WPSitesMonitor\Vendor_Prefixed\DI\autowire( LifeCycleService::class ),
	ResourceServiceInterface::class         => WPSitesMonitor\Vendor_Prefixed\DI\autowire( ResourceService::class ),
);
