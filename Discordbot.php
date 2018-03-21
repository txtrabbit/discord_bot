<?php

include __DIR__.'/vendor/autoload.php';

use Discord\Discord;

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$discord = new Discord([
	'token' => getenv('MY_TOKEN'),
]);

$discord->on('ready', function ($discord) {
	echo "Bot is ready!", PHP_EOL;

	// Listen for messages.
$discord->on('message', function ($message, $discord) {
	echo "{$message->author->username}: {$message->content}",PHP_EOL;
  $filename = 'somefile.txt';
  $text = "{$message->author->username}: {$message->content}\n";
//записываем текст в файл
file_put_contents($filename, $text, FILE_APPEND);
	});
});

$discord->run();
