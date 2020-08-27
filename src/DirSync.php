<?php

namespace DirSync;

use DirSync\action\ActionInterface;
use DirSync\config\Constants;

class DirSync implements DirSyncInterface
{
    private $rootDir;
    private $jsonPath;
    private $jsonInput;
    private $input;
    private $options;

    public function __construct()
    {
        $this->rootDir = defined('__root__') ? __root__ : '/';
        $this->jsonPath = null;
        $this->jsonInput = '';
        $this->input = [];
    }

    /**
     * Will set the root directory in which the directory
     * sync will be applied.
     * If the root directory is not set the Instance should look for
     * constant "__root__"; if the constant is not provided
     * then the root is the system root.
     * @param string $path A valid path to a existing directory
     * @return self
     */
    public function setRootDir($path)
    {
        if (is_dir($path)) {
            $this->rootDir = $path;
        }

        return $this;
    }

    /**
     * Will read the JSON string directly from a file path;
     *
     * @param string $filePath A valid json file path
     * @throws \DirSync\Exception
     * @return self
     */
    public function fromFile($filePath)
    {
        if (file_exists($filePath) && $this->input = json_decode(file_get_contents($filePath), true)) {
            $this->jsonPath = $filePath;
        } else {
            throw new Exception();
        }
        return $this;
    }

    /**
     * Will provide the library with the JSON input
     *
     * @param string $JSON A raw string JSON
     * @throws \DirSync\Exception
     * @return self
     */
    public function setJsonInput($JSON)
    {
        $this->jsonInput = $JSON;
        $this->input = json_decode($JSON, true);
        if (!json_last_error() === JSON_ERROR_NONE) {
            throw new Exception();
        }

        return $this;
    }

    /**
     * Simply return the previously given JSON data.
     * @throws \DirSync\Exception
     * @return string Return a string JSON data.
     */
    public function getJsonInput()
    {
        if (empty($this->jsonInput)) {

            throw new Exception();
        }
        return $this->jsonInput;
    }

    /**
     * Will begin the process of the synchronization.
     * The process can have the following options:
     *
     *  \DirSync::SYNC_CREATE_ONLY - creating directories only;<br>
     *  \DirSync::SYNC_REMOVE_ONLY - only removing directories;<br>
     *  \DirSync::SYNC_ACTIONS_ONLY - just run the action but do
     *  not change the directory tree in any way;<br>
     *
     * @param mixed [optional] Additional options for the directory sync process
     * @throws \DirSync\Exception
     * @return self|array
     */
    public function sync($options = null)
    {
        if (!in_array($options, Constants::$optionsAllowed, true)) {
            $options = null;
        } else {
            $this->options = $options;
        }
        $this->recSync($this->input, $this->rootDir);
        return $this;
    }

    private function recSync($input, $currentPath)
    {
        /** REMOVE ADDITIONAL ITEMS */
        foreach (array_diff(scandir($currentPath), array('.', '..')) as $existingItem) {
            if ($this->removeAllowed()
                && substr($existingItem, 0, 1) !== Constants::ACTION_SYMBOL
                && $input !== false
                && !isset($input[$existingItem])) {
                $itemToBeDeleted = $currentPath . DIRECTORY_SEPARATOR . $existingItem;
                if (is_dir($itemToBeDeleted)) {
                    self::rmdirRec($itemToBeDeleted);
                }
            }
        }
        if (is_array($input)) {
            /** CREATE FOLDERS */
            foreach ($input as $key => $value) {
                if ($this->actionAllowed() && substr($key, 0, 1) === Constants::ACTION_SYMBOL) {
                    $this->performAction(ltrim($key, '#'), $value, $currentPath);
                } elseif ($this->createAllowed() && $key !== '') {
                    $newDirPath = $currentPath . DIRECTORY_SEPARATOR . $key;
                    if (is_dir($newDirPath) || mkdir($newDirPath)) {
                        $this->recSync($value, $newDirPath);
                    }
                }
            }
        }

    }

    private function performAction($actionName, $options, $currentPath)
    {
        $actionName = ucfirst(strtolower($actionName));
        if (!in_array($actionName, Constants::$allowedActions)) {
            return;
        }
        /** @var ActionInterface $actionInstance */
        $actionName = '\DirSync\action\\' . $actionName . 'Action';
        $actionInstance = new $actionName();
        $actionInstance->run($currentPath, $options);
    }

    private static function rmdirRec($dir)
    {
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? self::rmdirRec("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

    private function actionAllowed()
    {
        return $this->options === null || $this->options === Constants::SYNC_ACTIONS_ONLY;
    }

    private function createAllowed()
    {
        return $this->options === null || $this->options === Constants::SYNC_CREATE_ONLY;
    }

    private function removeAllowed()
    {
        return $this->options === null || $this->options === Constants::SYNC_REMOVE_ONLY;
    }
}