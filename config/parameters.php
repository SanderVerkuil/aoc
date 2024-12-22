<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function(ContainerConfigurator $container): void {
    $container->parameters()
        ->set('app.name', '%env(resolve:APP_NAME)%')
        ->set('app.version', '%env(APP_VERSION)%');
};
