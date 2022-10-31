<?php

namespace Deployer;

task('db:decompress', function () {

    if (!has('dumpcode')) {
        throw error('No dumpcode given');
    }

    $markersArray = [];
    $markersArray['{{databaseStorageAbsolutePath}}'] = get('db_storage_path');
    $markersArray['{{dumpcode}}'] = get('dumpcode');

    foreach (get('db_decompress_command') ?? [] as $dbProcessCommand) {
        run(str_replace(
            array_keys($markersArray),
            $markersArray,
            $dbProcessCommand
        ));
    }
});
