<?php

namespace Sander\AdventOfCode;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Logger
{
    private static ?self $instance = null;

    public function __construct(
        private LoggerInterface $logger,
    )
    {
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