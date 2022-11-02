<?php
namespace Deployer;

require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/vendor/xima/deployer-extended/autoload.php');
require_once(__DIR__ . '/vendor/xima/deployer-extended-typo3/autoload.php');

// Config
set('repository', 'git@github.com:maikschneider/deployer-v7-app.git');

add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

// Hosts
host('vserver')
    ->set('hostname', '84.38.66.101')
    ->set('port', '24327')
    ->set('remote_user', 'web0')
    ->set('deploy_path', '/home/web0/vhosts/deployer-v7.maik-tailor.de/htdocs');

localhost('local')
    ->set('db_databases', [
        'default' => [
            'host' => 'db',
            'dbname' => 'db',
            'password' => 'db',
            'user' => 'db',
            'port' => 3306,
        ]
    ]);
