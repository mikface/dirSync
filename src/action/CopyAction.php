<?php

namespace DirSync\action;

class CopyAction implements ActionInterface
{
    const THIS_FOLDER = './';

    /**
     * @inheritdoc
     */
    public function run($currentPath, array $options)
    {
        if (!isset($options[0])) {
            return;
        }
        $src = $options[0];
        $dest = isset($options[1]) ? $options[1] : self::THIS_FOLDER;
        self::recursiveCopy($currentPath . DIRECTORY_SEPARATOR . $src, $currentPath . DIRECTORY_SEPARATOR . $dest);
    }

    private static function recursiveCopy($src, $dest)
    {
        $dir = opendir($src);
        @mkdir($dest);
        while (($file = readdir($dir))) {
            if (($file !== '.') && ($file !== '..')) {
                if (is_dir($src . '/' . $file)) {
                    self::recursiveCopy($src . '/' . $file, $dest . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dest . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
}