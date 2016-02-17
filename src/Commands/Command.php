<?php

namespace SadnessDeployer\Commands;

use Illuminate\Support\Fluent;
use Symfony\Component\Process\PhpExecutableFinder;

/**
 * @property string $command
 * @property string $sanitized
 * @property string $output
 * @property int    $status
 * @property bool   $done
 */
class Command extends Fluent
{
    /**
     * {@inheritdoc}
     */
    public function __construct($attributes)
    {
        // Default attributes
        if (is_string($attributes)) {
            $attributes = [
                'command' => $attributes,
                'status' => null,
                'output' => null,
                'done' => false,
            ];
        }

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
        $storagePath = realpath(__DIR__.'/../../cache/composer');

        $command = is_array($command) ? implode(' ', $command) : $command;
        $command = str_replace('composer ', 'COMPOSER_HOME='.$storagePath.' php composer.phar ', $command);
        $command = str_replace('artisan ', 'php artisan ', $command);
        $command = str_replace('php ', $php.' ', $command);

        return $command;
    }
}
