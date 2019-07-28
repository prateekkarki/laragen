<?php

namespace Prateekkarki\Laragen\Models;
use Symfony\Component\Filesystem\Filesystem as SymphonyFilesystem;

class FileSystem extends SymphonyFilesystem
{
    public function clone($src, $dest){
        $src = $this->getFullSourcePath($src);

        $dest = base_path($dest);

        if (is_dir($src)) {
            $this->mirror($src, $dest."/".basename($src));
        } else {
            $this->copy($src, $dest);
        }
    }

    public function getFullSourcePath($path) {
        return realpath(__DIR__."/../resources/".$path);
    }
}
