<?php

namespace SadnessDeployer;

use SadnessDeployer\Commands\Command;

class BatchManager
{
    /**
     * @var string
     */
    protected $folder;

    /**
     * BatchManager constructor.
     */
    public function __construct()
    {
        $this->folder = __DIR__.'/../batches';
    }

    /**
     * @param Command[] $commands
     *
     * @return string
     */
    public function set(array $commands)
    {
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
        $contents = file_get_contents($filename);

        return unserialize($contents);
    }
}
