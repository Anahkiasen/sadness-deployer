<?php

namespace SadnessDeployer\Commands;

use Illuminate\Support\Fluent;
use Symfony\Component\Process\PhpExecutableFinder;

/**
 * @property string $command
 * @property string $sanitized
 * @property string $output
 * @property int $status
 * @property bool $done
 */
class Command extends Fluent
{
    /**
     * {@inheritdoc}
     */
    public function __construct($attributes)
    {
        parent::__construct($attributes);

        $this->sanitized = $this->sanitizeCommand($this->command);
    }

    /**
     * @param string|array $command
     *
     * @return string
     */
    protected function sanitizeCommand($command)
    {
        $php = (new PhpExecutableFinder())->find(false);

        $command = is_array($command) ? implode(' ', $command) : $command;
        $command = str_replace('composer ', 'COMPOSER_HOME='.storage_path('composer').' php composer.phar ', $command);
        $command = str_replace('artisan ', 'php artisan ', $command);
        $command = str_replace('php ', $php.' ', $command);

        return $command;
    }
}
