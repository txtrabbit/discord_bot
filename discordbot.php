<?php

include __DIR__.'/vendor/autoload.php';

use Discord\Discord;

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();


$con = mysqli_connect(getenv('host'),getenv('mysql_user'), getenv('root_password'));

if (mysqli_connect_errno($con)) {
  printf("Connect failed: %s\n", mysqli_connect_error());
  exit();
}

mysqli_select_db($con, getenv('mysql_db'));


$discord = new Discord([
  'token' => getenv('MY_TOKEN'),
]);

$discord->on('ready', function ($discord) use ($con) {
  echo "Bot is ready!", PHP_EOL;

  $discord->on('message', function ($message, $discord) use ($con) {
    $time = mysqli_real_escape_string($con, $message->timestamp);
    $user = mysqli_real_escape_string($con, $message->author->username);
    $text = mysqli_real_escape_string($con, $message->content);
    $channel_id = mysqli_real_escape_string($con, $message->channel_id);
    $id = mysqli_real_escape_string($con, $message->id);
    $server_id = mysqli_real_escape_string($con, $message->author->guild_id);
    $server_name = mysqli_real_escape_string($con, $discord->guilds[$server_id]["name"]);
    echo "$user: $text",PHP_EOL;

    $sql = "INSERT INTO messages (`time`, `nickname`, `message`, `channel_id`, `id`, `server_id`, `server_name`) VALUES ('$time', '$user', '$text', '$channel_id', '$id', '$server_id', '$server_name')";

    echo $sql . "\n";

    if (!mysqli_query($con, $sql)) {
      echo "Errormessage: " . mysqli_error($con);
      mysqli_connect(getenv('host'),getenv('mysql_user'), getenv('root_password'));
      mysqli_query($con, $sql);
    }
  });
});
$discord->run();
