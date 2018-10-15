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
    const VERBOSITY_QUIET = SymfonyOutputInterface::VERBOSITY_QUIET;
    const VERBOSITY_NORMAL = SymfonyOutputInterface::VERBOSITY_NORMAL;
    const VERBOSITY_VERBOSE = SymfonyOutputInterface::VERBOSITY_VERBOSE;
    const VERBOSITY_VERY_VERBOSE = SymfonyOutputInterface::VERBOSITY_VERY_VERBOSE;
    const VERBOSITY_DEBUG = SymfonyOutputInterface::VERBOSITY_DEBUG;

    const VERBOSITY_STRINGS =
        [
            self::VERBOSITY_QUIET => 'QUIET',
            self::VERBOSITY_NORMAL => 'NORMAL',
            self::VERBOSITY_VERBOSE => 'VERBOSE',
            self::VERBOSITY_VERY_VERBOSE => 'VERY VERBOSE',
            self::VERBOSITY_DEBUG => 'DEBUG',
        ];

    const DEBUG = 'debug';
    const DARK = 'dark';
    const MESSAGE = 'message';
    const LINE = 'line';
    const INFO = 'info';
    const COMMENT = 'comment';
    const NOTICE = 'notice';
    const WARNING = 'warning';
    const ERROR = 'error';
    const ALERT = 'alert';
    const EMERGENCY = 'emergency';
    const ATTENTION = 'attention';
    
    const TIME_FORMAT = '[Y-m-d H:i:s]';
    const DEBUG_TIME_FORMAT = '[Y-m-d H:i:s.u]';
    
}