<?php
/**
 * User: alec
 * Date: 16.10.18
 * Time: 0:26
 */

namespace AlecRabbit;


use AlecRabbit\Contracts\OutputInterface;
use React\EventLoop\LoopInterface;
use React\Stream\WritableResourceStream;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;

class Output implements OutputInterface
{
    /** @var WritableResourceStream */
    protected $stream;

    /** @var int */
    protected $verbosity;

    /**@var OutputFormatterInterface */
    private $formatter;

    /**@var ConsoleColour */
    private $color;

    /** @var string */
    protected $timeFormat;

    public function __construct(?LoopInterface $loop = null,
                                ?int $verbosity = null,
                                OutputFormatterInterface $formatter = null,
                                $force256Colors = null)
    {
        $this->verbosity = $verbosity ?? OutputInterface::VERBOSITY_NORMAL;
        $this->formatter = $formatter ?? new OutputFormatter();
        $this->color = new ConsoleColour($force256Colors);
        $this->formatter->setDecorated($this->color->isSupported());
        $this->timeFormat = OutputInterface::TIME_FORMAT;

        if ($loop)
            $this->stream = new WritableResourceStream(STDOUT, $loop);
    }

    public function getVerbosity(): int
    {
        return $this->verbosity;
    }

    public function setVerbosity(int $verbosity): void
    {
        $this->verbosity = $verbosity;
    }

    public function showVerbosity(int $verbosity = OutputInterface::VERBOSITY_VERBOSE, bool $timestamp = true)
    {
        $this->dark(
            'Verbosity level: ' . OutputInterface::VERBOSITY_STRINGS[$this->verbosity],
            $verbosity,
            $timestamp
        );
    }

    /**
     * Write a tinted string as information output.
     *
     * @param mixed $message
     * @param null|int $verbosity
     * @param mixed $timestamp
     * @return void
     */
    public function dark($message, $verbosity = null, $timestamp = false)
    {
        $this->line($message, OutputInterface::DARK, $verbosity, $timestamp);
    }

    /**
     * @param $message
     * @param mixed $style
     * @param int|null $verbosity
     * @param mixed $timestamp
     * @param bool $newLine
     * @throws
     */
    public function line($message,
                         $style = null,
                         $verbosity = null,
                         $timestamp = false,
                         $newLine = true)
    {
        $verbosity = $verbosity ?? OutputInterface::VERBOSITY_NORMAL;

        if ($this->verbosity >= $verbosity) {
            if (is_integer($message)) {
                $count = bounds($message, 0, 5);
                for ($i = 0; $i < $count; $i++)
                    $this->line('', $style, $verbosity, $timestamp);
                return;
            }
            if (is_array($message)) {
                foreach ($message as $item) {
                    $this->line($item, $style, $verbosity, $timestamp);
                }
                return;
            }
            if (empty($message))
                $message = '  ';

            if ($timestamp) {
                if (!empty($message) && $message != '  ')
                    if (is_string($timestamp)) {
                        $message = ' ' . $timestamp . ' ' . $message;
                    } elseif ($timestamp instanceof \DateTime) {
                        $message = ' ' . $timestamp->format($this->timeFormat) . ' ' . $message;
                    } else {
                        $message = ' ' . now()->format($this->timeFormat) . ' ' . $message;
                    }
            }

            if (is_array($style)) {
                $message = $this->color->apply($style, $message);
            } else {
                switch ($style) {
                    case OutputInterface::DEBUG:
                    case OutputInterface::DARK:
                        $message = $this->color->apply(['dark'], $message);
                        break;
                    case OutputInterface::MESSAGE:
                        $message = $this->color->apply(['light_green'], $message);
                        break;
                    case OutputInterface::NOTICE:
                        $message = $this->color->apply(['light_yellow'], $message);
                        break;
                    case OutputInterface::WARNING:
                        $message = $this->color->apply(['light_yellow', 'bold'], $message);
                        break;
                    case OutputInterface::COMMENT:
                        $message = tag($message, OutputInterface::COMMENT);
                        break;
                    case OutputInterface::INFO:
                        $message = tag($message, OutputInterface::INFO);
                        break;
                    case OutputInterface::ERROR:
                        if ($message == '  ')
                            $message = '';
                        $message = $this->color->apply(['white', 'bg_red'], $message);
                        break;
                    case OutputInterface::ATTENTION:
                        if ($message == '  ')
                            $message = '';
                        $message = $this->color->apply(['light_yellow', 'bg_black', 'bold'], $message);
                        break;
                }
            }
            $message = $this->formatter->format($message);

            $this->raw($message . ($newLine ? PHP_EOL : ''));
        }
    }

    public function raw($data)
    {
        if ($this->stream && $this->stream->isWritable()) {
            $this->stream->write($data);
        } else {
            echo $data;
        }
    }

    /**
     * Write a debug string as information output.
     *
     * @param mixed $message
     * @param null|int $verbosity
     * @param mixed $timestamp
     * @return void
     */
    public function debug($message,
                          ?int $verbosity = null,
                          $timestamp = true)
    {
        $this->line($message, OutputInterface::DEBUG, $verbosity ?? OutputInterface::VERBOSITY_DEBUG, $timestamp);
    }

    public function setStream(WritableResourceStream $stream)
    {
        $this->stream = $stream;
    }

    /**
     * Write a string as information output.
     *
     * @param mixed $message
     * @param null|int $verbosity
     * @param mixed $timestamp
     * @return void
     */
    public function message($message, $verbosity = null, $timestamp = false)
    {
        $this->line($message, OutputInterface::MESSAGE, $verbosity, $timestamp);
    }

    /**
     * Write a string as information output.
     *
     * @param mixed $message
     * @param null|int $verbosity
     * @param mixed $timestamp
     * @return void
     */
    public function info($message, $verbosity = null, $timestamp = false)
    {
        $this->line($message, OutputInterface::INFO, $verbosity, $timestamp);
    }

    /**
     * Write a string as information output.
     *
     * @param mixed $message
     * @param null|int $verbosity
     * @param mixed $timestamp
     * @return void
     */
    public function notice($message, $verbosity = null, $timestamp = false)
    {
        $this->line($message, OutputInterface::NOTICE, $verbosity, $timestamp);
    }

    /**
     * Write a string as warning output.
     *
     * @param mixed $message
     * @param null|int $verbosity
     * @param mixed $timestamp
     * @return void
     */
    public function warning($message, $verbosity = null, $timestamp = false)
    {
        $this->line($message, OutputInterface::WARNING, $verbosity, $timestamp);
    }

    /**
     * Write a string as error output.
     *
     * @param string $string
     * @param null|int $verbosity
     * @return void
     */
    public function error(string $string, $verbosity = null)
    {
        $s = str_repeat(' ', strlen($string) + 4);
        $this->line([$s, '  ' . $string . '  ', $s], OutputInterface::ERROR, $verbosity);
    }

    /**
     * Write a string as error output.
     *
     * @param \Throwable $e
     * @param null|int $verbosity
     * @return void
     */
    public function exception(\Throwable $e, $verbosity = null)
    {
        $string = '  ' . $e->getMessage() . '  ';
        $length = strlen($string);
        $class = '  [' . get_class($e) . ']  ';
        $eClassLength = strlen($class);
        $length = $length > $eClassLength ? $length : $eClassLength;

        $s = str_repeat(' ', $length);
        $this->line([1, $s, str_pad($class, $length), str_pad($string, $length), $s, 1], OutputInterface::ERROR, $verbosity);
        $this->dark($e->getTraceAsString(), OutputInterface::VERBOSITY_DEBUG);
    }

    /**
     * Write a string as attention output.
     *
     * @param string $string
     * @param null|int $verbosity
     * @return void
     */
    public function attention(string $string, $verbosity = null)
    {
        $string = ' ' . $string . '  ';
        $length = strlen($string);
        $attention = '  [!!! ATTENTION !!!]  ';
        $attLength = strlen($attention);
        $length = $length > $attLength ? $length : $attLength;


        $s = str_repeat(' ', $length);
        $this->line([1, $s, str_pad($attention, $length), str_pad($string, $length), $s, 1], OutputInterface::ATTENTION, $verbosity);
    }

    /**
     * Write a string in an alert box.
     *
     * @param mixed $message
     * @param null|int $verbosity
     * @return void
     */
    public function alert($message, $verbosity = null)
    {
        $length = 0;
        $rpt = 2;
        if (is_string($message)) {
            $message = [$message];
            $rpt = 1;
        }
        if (is_array($message)) {
            foreach ($message as &$item) {
                $length = max($length, strlen($item) + 12);
                $item = str_repeat(' ', 6) . $item;
            }
            $s = str_repeat('*', $length);
            $this->comment([1, $s, $message, $s, $rpt], $verbosity);
        }
    }

    /**
     * Write a string as comment output.
     *
     * @param mixed $message
     * @param null|int $verbosity
     * @param mixed $timestamp
     * @return void
     */
    public function comment($message, $verbosity = null, $timestamp = false)
    {
        $this->line($message, OutputInterface::COMMENT, $verbosity, $timestamp);
    }

    /**
     * Write a string as error output.
     *
     * @param mixed $message
     * @param null|int $verbosity
     * @return void
     */
    public function emergency($message, $verbosity = null)
    {
        $symbol = '+';
        $line = ' ' . str_repeat($symbol, strlen($message) + 20) . ' ';
        $line2 = str_repeat(' ', 9);

        $this->line($line, OutputInterface::ERROR, $verbosity);
        $this->line(' ' . $symbol . $line2 . $message . $line2 . $symbol . ' ', OutputInterface::ERROR, $verbosity);
        $this->line($line, OutputInterface::ERROR, $verbosity);

    }

    public function quit()
    {
        $this->stream->end('   ' . PHP_EOL);
    }

    /**
     * @param $char
     * @param null $style
     * @param null $verbosity
     */
    public function char($char,
                         $style = null,
                         $verbosity = null)
    {
        $this->line($char, $style, $verbosity, false, false);
    }

    /**
     * @param string $timeFormat
     */
    public function setTimeFormat(string $timeFormat): void
    {
        $this->timeFormat = $timeFormat;
    }

}