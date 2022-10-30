<?php
namespace Deployer;

require 'recipe/cakephp.php';

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

after('deploy:failed', 'deploy:unlock');
