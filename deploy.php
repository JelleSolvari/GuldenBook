<?php

namespace Deployer;

require 'recipe/laravel.php';

// Config
set('repository', 'https://bramvanberkel@github.com/BramVanBerkel/GuldenBook.git');

add('shared_files', [
    '.env',
]);

add('shared_dirs', [
    'binaries',
]);

add('writable_dirs', []);

// Hosts
host('guldenbook.com')
    ->set('deploy_path', '/var/www/guldenbook')
    ->set('testnet', false);
host('testnet.guldenbook.com')
    ->set('deploy_path', '/var/www/guldenbook-testnet')
    ->set('tesnet', true);

// Tasks
task('build:frontend', function () {
    $testnet = (get('testnet')) ? 'true' : 'false';
    cd('{{release_path}}');
    run('npm ci');
    run("MIX_TESTNET=$testnet npm run prod");
});

after('deploy:update_code', 'build:frontend');

after('deploy:failed', 'deploy:unlock');
