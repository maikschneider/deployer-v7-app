<?php

namespace Deployer;

set('db_process_commands', '');

set('db_export_mysqldump_options_structure', '--no-data=true --default-character-set=utf8 --no-tablespaces');

set('db_export_mysqldump_options_data',
    '--opt --skip-lock-tables --single-transaction --no-create-info --default-character-set=utf8 --no-tablespaces');

set('db_storage_path', function () {
    $path = has('deploy_path') ? get('deploy_path') : getcwd();
    return $path . '/.dep/database/dumps';
});

set('local/bin/deployer', function () {
    return './vendor/bin/dep';
});

set('local/bin/mysqldump', function () {
    return run('command -v mysqldump || which mysqldump');
});

set('local/bin/mysql', function () {
    return run('command -v mysql || which mysql');
});

set('local/bin/gzip', function () {
    return run('command -v gzip || which gzip');
});

