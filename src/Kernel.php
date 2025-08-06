<?php

declare(strict_types=1);

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

/**
 * @SuppressWarnings(PMD.UnusedPrivateMethod)
 */
class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    private const ROUTES_YAML_FILE = '/routes.yaml';

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import(__DIR__.'/../config/{packages}/*.yaml');
        $container->import(__DIR__.'/../config/{packages}/'.$this->environment.'/*.yaml');
        $container->import(__DIR__.'/../config/services.yaml');
        if (file_exists(__DIR__.'/services.yaml')) {
            $container->import(__DIR__.'/services.yaml');
        }
    }

    private function configureRoutes(RoutingConfigurator $routes): void
    {
        $configDir = $this->getConfigDir();

        $routes->import($configDir.'/{routes}/'.$this->environment.'/*.{php,yaml}');
        $routes->import($configDir.'/{routes}/*.{php,yaml}');

        if (is_file($configDir.self::ROUTES_YAML_FILE)) {
            $routes->import($configDir.self::ROUTES_YAML_FILE);
        } else {
            $routes->import($configDir.'/{routes}.php');
        }

        if (file_exists(__DIR__.self::ROUTES_YAML_FILE)) {
            $routes->import(__DIR__.self::ROUTES_YAML_FILE);
        }
    }
}
