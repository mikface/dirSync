<?php

namespace DirSync\config;


use DirSync\DirSync;

class Constants
{
    const ACTION_SYMBOL = '#';
    public static $optionsAllowed = [
        DirSync::SYNC_ACTIONS_ONLY,
        DirSync::SYNC_CREATE_ONLY,
        DirSync::SYNC_REMOVE_ONLY
    ];
    const ACTION_COPY = 'Copy';
    const ACTION_REMOVE = 'Remove';
    public static $allowedActions = [self::ACTION_COPY, self::ACTION_REMOVE];
}