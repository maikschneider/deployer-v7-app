<?php

namespace Deployer;

set('db_compress_suffix', '.gz');

set('db_compress_command', [
    '{{local/bin/gzip}} --force --name {{databaseStorageAbsolutePath}}/*dumpcode={{dumpcode}}*.sql --suffix ' . get('db_compress_suffix')
]);

set('db_decompress_command', [
    '{{local/bin/gzip}} --force --name --uncompress ' . ' --suffix ' . get('db_compress_suffix') . ' {{databaseStorageAbsolutePath}}/*dumpcode={{dumpcode}}*' . get('db_compress_suffix')
]);
/**
 * @TODO: check this command, add example to doc
 */
set('db_process_commands', '');

set('db_export_mysqldump_options_structure', '--no-data=true --default-character-set=utf8 --no-tablespaces');

set('db_export_mysqldump_options_data',
    '--opt --skip-lock-tables --single-transaction --no-create-info --default-character-set=utf8 --no-tablespaces');

set('db_import_mysql_options_structure', '--default-character-set=utf8');

set('db_import_mysql_options_data', '--default-character-set=utf8');

set('db_storage_path', function () {
    $path = has('deploy_path') ? get('deploy_path') : getcwd();
    return $path . '/.dep/database/dumps';
});

set('db_storage_path_local', function () {
    $path = localhost()->has('deploy_path') ? localhost()->get('deploy_path') : getcwd();
    return $path . '/.dep/database/dumps';
});

set('bin/deployer', function () {
    return './vendor/bin/dep';
});

set('bin/mysqldump', function () {
    return which('mysqldump');
});

set('bin/mysql', function () {
    return which('mysql');
});

set('bin/gzip', function () {
    return which('gzip');
});

