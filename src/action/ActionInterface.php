<?php

namespace DirSync\action;

interface ActionInterface
{

    /**
     * @param string Current path in dir tree
     * @param array Array of options
     */
    public function run($currentPath, array $options);

}