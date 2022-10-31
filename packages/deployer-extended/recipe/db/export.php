<?php

namespace Deployer;

use Xima\DeployerExtended\Utility\ArrayUtility;
use Xima\DeployerExtended\Utility\DatabaseUtility;
use Xima\DeployerExtended\Utility\FileUtility;

task('db:export', function () {

    $dumpCode = has('dumpcode') ? get('dumpcode') : md5(microtime(true) . random_int(0, 10000));

    if (!preg_match('/^\w+$/', $dumpCode)) {
        throw error('dumpcode can only be a-z, A-Z, 0-9');
    }

    if (!has('db_databases')) {
        throw error('No "db_databases" configured');
    }

    run('mkdir -p {{db_storage_path}}');

    foreach (get('db_databases') as $databaseIdentifier => $databaseConfig) {
        $filenameParts = [
            'dateTime' => date('Y-m-d_H-i-s'),
            'server' => 'server=' . FileUtility::normalizeFilename(currentHost()->getAlias() ?? ''),
            'dbcode' => 'dbcode=' . FileUtility::normalizeFilename($databaseIdentifier),
            'dumpcode' => 'dumpcode=' . FileUtility::normalizeFilename($dumpCode),
            'type' => '',
        ];
        $mysqlDumpArgs = [
            'host' => escapeshellarg($databaseConfig['host']),
            'port' => escapeshellarg((isset($databaseConfig['port']) && $databaseConfig['port']) ? $databaseConfig['port'] : 3306),
            'user' => escapeshellarg($databaseConfig['user']),
            'absolutePath' => '',
            'options' => '',
            'ignore-tables' => '',
            'dbname' => escapeshellarg($databaseConfig['dbname']),
        ];

        if (isset($databaseConfig['ignore_tables_out']) && is_array($databaseConfig['ignore_tables_out'])) {
            $ignoreTables = ArrayUtility::filterWithRegexp(
                $databaseConfig['ignore_tables_out'],
                DatabaseUtility::getTables($databaseConfig)
            );
            if (!empty($ignoreTables)) {
                $mysqlDumpArgs['ignore-tables'] = '--ignore-table=' . $databaseConfig['dbname'] . '.' .
                    implode(' --ignore-table=' . $databaseConfig['dbname'] . '.', $ignoreTables);
            }
        }

        // dump database structure
        $filenameParts['type'] = 'type=structure';
        $mysqlDumpArgs['options'] = get('db_export_mysqldump_options_structure');
        $mysqlDumpArgs['absolutePath'] = escapeshellarg(FileUtility::normalizeFolder(get('db_storage_path')) . implode('#',
                $filenameParts) . '.sql');
        run('{{local/bin/mysqldump}} -p%secret% ' . vsprintf('-h%s -P%s -u%s -r%s %s %s %s', $mysqlDumpArgs),
            secret: escapeshellarg($databaseConfig['password']));

        // dump database data
        $filenameParts['type'] = 'type=data';
        $mysqlDumpArgs['options'] = get('db_export_mysqldump_options_data');
        $mysqlDumpArgs['absolutePath'] = escapeshellarg(FileUtility::normalizeFolder(get('db_storage_path'))
            . implode('#', $filenameParts) . '.sql');
        run('{{local/bin/mysqldump}} -p%secret% ' . vsprintf('-h%s -P%s -u%s -r%s %s %s %s', $mysqlDumpArgs),
            secret: escapeshellarg($databaseConfig['password']));
    }
});
