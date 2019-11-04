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

    public function removeDir($dir) {
        if (! is_dir($dir)) {
            throw new InvalidArgumentException("$dir must be a directory");
        }
        if (substr($dir, strlen($dir) - 1, 1) != '/') {
            $dir .= '/';
        }
        $files = glob($dir . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                $this->removeDir($file);
            } else {
                $this->remove($file);
            }
        }
        $this->remove($dir);
    }
}
