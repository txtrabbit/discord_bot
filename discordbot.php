<?php

include __DIR__.'/vendor/autoload.php';

use Discord\Discord;

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();


$con = mysqli_connect(getenv('host'),getenv('mysql_user'), getenv('root_password'));

// if (mysqli_connect_errno($con)) {
//   printf("Connect failed: %s\n", mysqli_connect_error());
//   exit();
// }
mysqli_select_db($con, getenv('mysql_db'));


$discord = new Discord([
	'token' => getenv('MY_TOKEN'),
]);

$discord->on('ready', function ($discord) use ($con) {
	echo "Bot is ready!", PHP_EOL;

	// Listen for messages.
  $discord->on('message', function ($message, $discord) use ($con) {
    $time = $message->timestamp;
    $user = $message->author->username;
    $text = $message->content;
		$channel_id = $message->channel_id;

	  echo "$user: $text",PHP_EOL;

    $time = mysqli_real_escape_string($con, $time);
    $text = mysqli_real_escape_string($con, $text);
    $user = mysqli_real_escape_string($con, $user);
    $channel_id = mysqli_real_escape_string($con, $channel_id);

    $sql = "INSERT INTO messages (`time`, `nickname`, `message` `channel_id`) VALUES ('$time', '$user', '$text', '$channel_id')";

    echo $sql . "\n";

		if (!mysqli_query($con, $sql)) {
		  echo "Errormessage: " . mysqli_error($con);
    }
	});
});

$discord->run();
