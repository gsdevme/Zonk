<?php

namespace Zonk\Monolog;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Symfony\Component\Console\Output\OutputInterface;

class OutputHandler extends AbstractProcessingHandler
{
    /** @var OutputInterface */
    private $output;

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @param array $record
     */
    protected function write(array $record)
    {
        $line = '<info>[%s]: %s</info>';

        switch ($record['level']) {
            case Logger::WARNING:
                $line = '<comment>[%s]: %s</comment>';
                break;
            case Logger::DEBUG:
            case Logger::CRITICAL:
            case Logger::ERROR:
                $line = '<error>[%s]: %s</error>';
                break;
            default:
                break;
        }

        $this->output->writeln(sprintf($line, $record['datetime']->format('Y-m-d H:i:s'), $record['message']));
    }
}
