<?php

namespace Xima\DeployerExtended\Utility;

use RuntimeException;
use Symfony\Component\Dotenv\Dotenv;

/**
 * Configuration reader for data stored in .env file.
 */
class Env
{
    protected static bool $envLoaded = false;

    public function load(?string $configFile = null, ?string $envKey = 'APP_ENV'): void
    {
        if (self::$envLoaded) {
            return;
        }

        $configFile = $configFile ?? getcwd() . '/.env';

        if (!file_exists($configFile)) {
            throw new RuntimeException('Missing config file. Searching in: \n' . $configFile, 1500717945887);
        }

        $dotEnv = new Dotenv();
        if (method_exists($dotEnv, 'loadEnv')) {
            $dotEnv->loadEnv($configFile, $envKey);
        } else {
            $dotEnv->load($configFile);
        }

        self::$envLoaded = true;
    }

    public function get(string $envName): mixed
    {
        return $_ENV[$envName] ?? null;
    }
}
