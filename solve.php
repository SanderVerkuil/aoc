#!/usr/bin/env php
<?php
require __DIR__ . '/vendor/autoload.php';

use Psr\Log\LoggerInterface;
use Sander\AdventOfCode\Kernel;
use Sander\AdventOfCode\Logger;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;


if (!is_file(__DIR__ . '/vendor/autoload.php')) {
    throw new LogicException('Composer autoload missing. Try running `composer update`.');
}

require_once __DIR__ . '/vendor/autoload.php';

if (!function_exists('tagged_iterator')) {
    function tagged_iterator(
        string  $tag,
        ?string $indexAttribute = null,
        ?string $defaultIndexMethod = null,
        ?string $defaultPriorityMethod = null): TaggedIteratorArgument
    {
        return new TaggedIteratorArgument(
            $tag,
            $indexAttribute,
            $defaultIndexMethod,
            false,
            $defaultPriorityMethod
        );
    }
}

$dotEnv = new Dotenv();
$dotEnv->bootEnv(__DIR__ . '/.env');

try {
    $containerCacheClassName = 'AppContainerCache';
    $containerCacheFile = __DIR__ . '/var/cache/' .
        $_ENV['APP_ENV'] . '/' . $containerCacheClassName . '.php';
    $containerConfigCache = new ConfigCache($containerCacheFile, $_ENV['APP_DEBUG']);
    if (true || !$containerConfigCache->isFresh()) {
        $containerBuilder = new ContainerBuilder();
        $loader = new PhpFileLoader($containerBuilder, new FileLocator(__DIR__ . '/config'));
        try {
            $loader->load('services.php');
            $loader->load('parameters.php');
        } catch (Throwable $e) {
            echo $e->getMessage() . "\n";
            exit(Command::FAILURE);
        }

        $containerBuilder->registerForAutoconfiguration(Command::class)
            ->addTag('console.command');

        $containerBuilder->register('kernel', Kernel::class)
            ->setPublic(true)
            ->setArguments(
                [
                    tagged_iterator('console.command'),
                    $containerBuilder->getParameter('app.name'),
                    $containerBuilder->getParameter('app.version'),
                    $_ENV['APP_ENV'],
                    $_ENV['APP_DEBUG'],
                ]
            );

        $containerBuilder->register(ParameterBagInterface::class, ParameterBag::class)
            ->setPublic(false)
            ->setArguments([$containerBuilder->getParameterBag()->all()])
        ;

        $containerBuilder->compile(true);

        $dumper = new PhpDumper($containerBuilder);
        $containerConfigCache->write(
            $dumper->dump(['class' => $containerCacheClassName]),
            $containerBuilder->getResources()
        );
    }
    require_once $containerCacheFile;
    $container = new $containerCacheClassName();
    exit($container->get('kernel')->run());
} catch (Throwable $error) {
    echo $error->getMessage() . "\n";
    exit(Command::FAILURE);
}

function logger(): LoggerInterface
{
    return Logger::get();
}
