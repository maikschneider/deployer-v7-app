<?php

namespace Deployer;

task('db:download', function () {

    if (!has('dumpcode')) {
        throw error('No dumpcode given');
    }

    if (!test('[ -d {{db_storage_path}} ]')) {
        throw error('Directory "{{db_storage_path}} does not exist');
    }

    $searchResult = run('find {{db_storage_path}}/ -regex .*dumpcode={{dumpcode}}.*') ?? '';
    $files = array_filter(explode("\n", $searchResult));

    if (!count($files)) {
        throw error('Could not find database exports with dumpcode "{{dumpcode}}"');
    }

    runLocally('mkdir -p {{db_storage_path_local}}');

    foreach($files as $file) {
        download($file, get('db_storage_path_local'));
    }
});
