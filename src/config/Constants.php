<?php

namespace DirSync\config;


class Constants
{
    const SYNC_CREATE_ONLY = 'SYNC_CREATE_ONLY';
    const SYNC_REMOVE_ONLY = 'SYNC_REMOVE_ONLY';
    const SYNC_ACTIONS_ONLY = 'SYNC_ACTIONS_ONLY';
    const ACTION_SYMBOL = '#';
    public static $optionsAllowed = [
        self::SYNC_ACTIONS_ONLY,
        self::SYNC_CREATE_ONLY,
        self::SYNC_REMOVE_ONLY
    ];
    const ACTION_COPY = 'Copy';
    const ACTION_REMOVE = 'Remove';
    public static $allowedActions = [self::ACTION_COPY, self::ACTION_REMOVE];
}