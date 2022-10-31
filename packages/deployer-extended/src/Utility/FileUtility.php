<?php

namespace Xima\DeployerExtended\Utility;

use Deployer\Deployer;

class FileUtility
{

    public static function normalizeFilename(string $filename): string
    {
        return preg_replace('/^\W+$/', '', $filename);
    }

    public static function normalizeFolder($folder): string
    {
        return rtrim($folder, '/') . '/';
    }
}
