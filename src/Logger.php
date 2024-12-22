<?php

namespace Sander\AdventOfCode;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\Console\Logger\ConsoleLogger;

class Logger
{
    private static ?self $instance = null;

    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(ConsoleEvent $event): void {
        static::set(new ConsoleLogger($event->getOutput()));
    }

    public static function get(): LoggerInterface
    {
        return self::instance()->logger;
    }

    public static function set(LoggerInterface $logger): void
    {
        self::instance()->logger = $logger;
    }

    private static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self(new NullLogger());
        }
        return self::$instance;
    }
}