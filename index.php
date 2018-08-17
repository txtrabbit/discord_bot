<?php
require __DIR__.'/vendor/autoload.php';

use JasonGrimes\Paginator;

function connect_to_mysql() {
  $con = mysqli_connect(getenv('host'),getenv('mysql_user'), getenv('root_password'));

  if (mysqli_connect_errno()) {
      printf("Connect failed: %s\n", mysqli_connect_error());
      exit();
  }

  //TODO: проверить были ли ошибки при выборе базы
  mysqli_select_db($con, getenv('mysql_db'));

  return $con;
}

function messages_count($con, $server_id) {
  $messages_count = "SELECT count(*) from  messages WHERE server_id = $server_id";
  $result = mysqli_query($con, $messages_count);
  $row = mysqli_fetch_row($result);
  return (int) $row[0];
}

function messages($con, $page, $per_page, $server_id) {
  if($page < 1) $page = 1;
  $offset = ($page - 1) * $per_page;
  $result = mysqli_query($con, "SELECT * FROM messages WHERE server_id = $server_id ORDER BY time DESC LIMIT $offset, $per_page");
  $messages = [];
  while ($messages[] = mysqli_fetch_assoc($result));
  array_pop($messages);
  return $messages;
}

function server_list($con) {
  $result = mysqli_query($con, "SELECT DISTINCT server_id FROM messages");
  $sever_list = [];
  while ($sever_list[] = mysqli_fetch_assoc($result));
  array_pop($sever_list);
  return $sever_list;
}

define("PER_PAGE", 10);

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$server_id = (int) $_GET["server_id"];
$page = (int) $_GET["page"];
$con = connect_to_mysql();
$messages_count = messages_count($con, $server_id);
$pages_count = ceil($messages_count / PER_PAGE);
$messages = messages($con, $page, PER_PAGE, $server_id);
$sever_list = server_list($con);

$totalItems = $messages_count;
$itemsPerPage = PER_PAGE;
$currentPage = $page;
$urlPattern = "?server_id=$server_id&page=(:num)";

$paginator = new Paginator($totalItems, $itemsPerPage, $currentPage, $urlPattern);
?>

<head>
  <title>Chat history</title>

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
  integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
  integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</head>

<body>
<?php if ($_GET["page"] == 0): ?>
    <?php foreach ($sever_list as $server): ?>
      <p><a href="/<?= "?server_id={$server[server_id]}&"?>page=1"> <?= htmlspecialchars($server["server_id"])?><a><p>
    <?php endforeach;?>
<?php else: ?>
  <?php foreach ($messages as $message): ?>
    <p> <?= htmlspecialchars($message["time"]) ?> <?= htmlspecialchars($message["nickname"]) ?>: <?= htmlspecialchars($message["message"]) ?> </p>
  <?php endforeach;?>

  <br>
  <br>
  <?php echo $paginator?>
  <br>
  Всего сообщений: <?= $messages_count  ?>
  <br>
  Всего страниц: <?= $pages_count ?>
<?php endif ?>
</body>
