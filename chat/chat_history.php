<head>
  <title>Chat history</title>

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
  integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
  integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</head>

<body>
  <nav class="nav nav-tabs">
    <?php foreach ($sever_list as $server): ?>
      <a class="nav-item nav-link <?php if ($server_id == $server["server_id"]):?>active<?php endif?>" href="/<?= "{$server["server_id"]}"?>/1"> <?= htmlspecialchars($server["server_name"])?><a>
    <?php endforeach;?>
  </nav>

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

</body>
