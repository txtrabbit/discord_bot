<?php
require __DIR__.'/vendor/autoload.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use JasonGrimes\Paginator;

$app = new \Slim\App();

$app->get('/[{server_id}[/{page}]]', function ($request, $response, $args) {

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
    $result = mysqli_query($con, "SELECT DISTINCT server_id, server_name FROM messages");
    $sever_list = [];
    while ($sever_list[] = mysqli_fetch_assoc($result));
    array_pop($sever_list);
    return $sever_list;
  }

  define("PER_PAGE", 10);

  $dotenv = new Dotenv\Dotenv(__DIR__);
  $dotenv->load();

  $server_id = (int) $args["server_id"];
  $page = (int) $args["page"];
  $con = connect_to_mysql();

  $sever_list = server_list($con);
  if ($server_id == 0) $server_id = (int) $sever_list[0]["server_id"];

  $messages_count = messages_count($con, $server_id);
  $pages_count = ceil($messages_count / PER_PAGE);
  $messages = messages($con, $page, PER_PAGE, $server_id);

  $totalItems = $messages_count;
  $itemsPerPage = PER_PAGE;
  $currentPage = $page;

  if ($page == 0) {
    $urlPattern = "$server_id/(:num)";
  } else {
  $urlPattern = "(:num)";
}
  $paginator = new Paginator($totalItems, $itemsPerPage, $currentPage, $urlPattern);

  require __DIR__.'/chat/chat_history.php';
});

$app->run();
?>
