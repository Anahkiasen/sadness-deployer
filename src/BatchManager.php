<?php

namespace SadnessDeployer;

use Illuminate\Support\Collection;
use SadnessDeployer\Commands\Command;

class BatchManager
{
    /**
     * @var string
     */
    protected $folder;

    /**
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->folder = $configuration->get('paths.cache').'/batches';
    }

    /**
     * @param Collection|Command[] $commands
     *
     * @return string
     */
    public function set($commands)
    {
        // Unwrap collections
        if ($commands instanceof Collection) {
            $commands = $commands->all();
        }

        $hash = md5(serialize($commands));
        $filename = $this->folder.'/'.$hash;

        file_put_contents($filename, serialize($commands));

        return $hash;
    }

    /**
     * @param string $hash
     *
     * @return Command[]
     */
    public function get($hash)
    {
        $filename = $this->folder.'/'.$hash;
        if (!file_exists($filename)) {
            return [];
        }

        $contents = file_get_contents($filename);
        $contents = unserialize($contents);

        return $contents;
    }
}
