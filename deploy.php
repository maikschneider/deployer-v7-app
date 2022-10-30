<?php
namespace Deployer;

require_once(__DIR__ . '/vendor/sourcebroker/deployer-loader/autoload.php');

new \SourceBroker\DeployerLoader\Load([
    ['path' => 'vendor/sourcebroker/deployer-extended/deployer'],
    ['path' => 'vendor/sourcebroker/deployer-instance/deployer'],
    ['path' => 'vendor/sourcebroker/deployer-extended-database/deployer'],
]);

// Config
set('repository', 'git@github.com:maikschneider/deployer-v7-app.git');

add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

// Hosts
host('schnupfspruch')
    ->set('hostname', '84.38.66.101')
    ->set('port', '24327')
    ->set('remote_user', 'web0')
    ->set('deploy_path', '/home/web0/vhosts/deployer-v7.maik-tailor.de/htdocs');

// Hooks
//after('deploy:failed', 'deploy:unlock');

// Tasks
task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'deploy:publish',
])->desc('Deploy your project');
