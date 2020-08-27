<?php

namespace DirSync\action;

/**
 * Class RemoveAction
 * Removes files with given extension (options)
 * @package DirSync\action
 */
class RemoveAction implements ActionInterface
{

    /**
     * @inheritdoc
     */
    public function run($currentPath, array $options)
    {
        if (!isset($options[0])) {
            return;
        }
        $extension = strtolower($options[0]);
        foreach (array_diff(scandir($currentPath), array('.', '..')) as $item) {
            if (!is_dir($item) && substr(strtolower($item), -(strlen($extension))) === $extension) {
                unlink($currentPath . DIRECTORY_SEPARATOR . $item);
            }
        }
    }
}