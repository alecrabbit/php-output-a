<?php
/**
 * User: alec
 * Date: 16.10.18
 * Time: 0:51
 */

namespace AlecRabbit\Contracts;


use Symfony\Component\Console\Output\OutputInterface as SymfonyOutputInterface;

interface OutputInterface
{
    public const VERBOSITY_QUIET = SymfonyOutputInterface::VERBOSITY_QUIET;
    public const VERBOSITY_NORMAL = SymfonyOutputInterface::VERBOSITY_NORMAL;
    public const VERBOSITY_VERBOSE = SymfonyOutputInterface::VERBOSITY_VERBOSE;
    public const VERBOSITY_VERY_VERBOSE = SymfonyOutputInterface::VERBOSITY_VERY_VERBOSE;
    public const VERBOSITY_DEBUG = SymfonyOutputInterface::VERBOSITY_DEBUG;

    public const VERBOSITY_STRINGS =
        [
            self::VERBOSITY_QUIET => 'QUIET',
            self::VERBOSITY_NORMAL => 'NORMAL',
            self::VERBOSITY_VERBOSE => 'VERBOSE',
            self::VERBOSITY_VERY_VERBOSE => 'VERY VERBOSE',
            self::VERBOSITY_DEBUG => 'DEBUG',
        ];

    public const DEBUG = 'debug';
    public const DARK = 'dark';
    public const MESSAGE = 'message';
    public const LINE = 'line';
    public const INFO = 'info';
    public const COMMENT = 'comment';
    public const NOTICE = 'notice';
    public const WARNING = 'warning';
    public const ERROR = 'error';
    public const ALERT = 'alert';
    public const EMERGENCY = 'emergency';
    public const ATTENTION = 'attention';
    
    public const TIME_FORMAT = '[Y-m-d H:i:s]';
    public const DEBUG_TIME_FORMAT = '[Y-m-d H:i:s.u]';
    
}