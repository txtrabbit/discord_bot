<?php
namespace Deployer;

require 'recipe/common.php';

// Project name
set('application', 'discordbot');

// Project repository
set('repository', 'git@github.com:txtrabbit/discord_bot.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

// Shared files/dirs between deploys
set('shared_files', ['.env']);
set('shared_dirs', []);

// Writable dirs by web server
set('writable_dirs', []);


// Hosts

host('discord')
    ->set('deploy_path', '~/')
    ->user('discordbot');


// Tasks

desc('Deploy your project');
task('test', function () {
    writeln('Hello world');
});

task('pwd', function () {
    $result = run('pwd');
    writeln("Current dir: $result");
});

task('php-fpm:reload', 'sudo service php7.2-fpm reload');

task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:writable',
    'deploy:vendors',
    'deploy:clear_paths',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'success'
]);

// [Optional] If deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
before('success', 'php-fpm:reload');
