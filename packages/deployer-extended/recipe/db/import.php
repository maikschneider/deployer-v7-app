<?php

namespace Deployer;

task('db:import', function () {

    if (!has('dumpcode')) {
        throw error('No dumpcode given');
    }

    if (!test('[ -d {{db_storage_path}} ]')) {
        throw error('Directory "{{db_storage_path}} does not exist');
    }

    if (!has('db_databases')) {
        throw error('No "db_databases" configured');
    }

    $filesToImport = [];

    foreach (get('db_databases') ?? [] as $databaseIdentifier => $databaseConfig) {

        $searchResult = run(
            'find {{db_storage_path}}/ -regex .*dbcode=$databaseIdentifier.*dumpcode={{dumpcode}}.*.sql',
            env: ['databaseIdentifier' => $databaseIdentifier]
        );

        $filesToImport[$databaseIdentifier] = array_filter(explode("\n", $searchResult));

        if (count($filesToImport[$databaseIdentifier]) !== 2) {
            throw error('Expected 2 sql files (structure+data) for import (dumpcode: {{dumpcode}}) into database "' . $databaseIdentifier . '", found ' . count($filesToImport[$databaseIdentifier]));
        }
    }

    foreach (get('db_databases') ?? [] as $databaseIdentifier => $databaseConfig) {

    }
});
