<?php

// the base path under which the application is exposed. for example, if you are
// accessing the application at
// "http://localhost:8888/comem-archidep-php-todo-exercise/", then base_url
// should be "/comem-archidep-php-todo-exercise/". if you are accessing the
// application at "http://localhost:8888", then base_url should be "/".
define('base_url', getenv('todolist_base_url')?:'/');

// database connection parameters.
define('db_user', getenv('todolist_db_user')?: 'todolist');
define('db_pass', getenv('todolist_db_pass'));
define('db_name', getenv('todolist_db_name')?:'todolist');
define('db_host', getenv('todolist_db_host')?:'localhost');
define('db_port', getenv('todolist_db_port')?: 3306);


$db = new pdo('mysql:host='.db_host.';port='.db_port.';dbname='.db_name, db_user, db_pass);
$items = array();

if (isset($_post['action'])) {
  switch($_post['action']) {

    /**
     * insert a new task into the database, then redirect to the base url.
     */
    case 'new':

      $title = $_post['title'];
      if ($title && $title !== '') {
        $insertquery = 'insert into todo values(null, \''.$title.'\', false, current_timestamp)';
        if (!$db->query($insertquery)) {
          die(print_r($db->errorinfo(), true));
        }
      }

      header('location: '.base_url);
      die();

    /**
     * toggle a task (i.e. if it is done, undo it; if it is not done, mark it as done),
     * then redirect to the base url.
     */
    case 'toggle':

      $id = $_post['id'];
      if(is_numeric($id)) {
        $currentstate = $db->query('select done from todo where id = '. $id);
        $currentstate->execute();
        $currentstate = $currentstate->fetch(pdo::fetch_assoc); 
        $newstate = $currentstate['done'] == 1 ? 0:1;
        $updatequery = 'update todo set done = '. $newstate .' where id = '. $id;   
        if(!$db->query($updatequery)) {
          die(print_r($db->errorinfo(), true));
        }
      }

      header('location: '.base_url);
      die();

    /**
     * delete a task, then redirect to the base url.
     */
    case 'delete':

      $id = $_post['id'];
      if(is_numeric($id)) {
        $deletequery = 'delete from todo where id like '. $id; // implement me
        if(!$db->query($deletequery)) {
          die(print_r($db->errorinfo(), true));
        }
      }

      header('location: '.base_url);
      die();

    default:
      break;
  }
}

/**
 * select all tasks from the database.
 */
$selectquery = 'select * from todo'; // implement me
$items = $db->query($selectquery);
?>

<html>
  <head>
    <title>todolist</title>

    <!-- bootstrap css -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-mcw98/sfnge8fjt3gxweongsv7zt27nxfoaoapmym81iuxopkfojwj8erdknlpmo" crossorigin="anonymous">

    <!-- custom css -->
    <style>
      button {
        cursor: pointer;
      }
      form {
        margin: 0;
      }
    </style>
  </head>
  <body>

    <!-- navbar -->
    <header>
      <div class="navbar navbar-dark bg-dark shadow-sm">
        <div class="container d-flex justify-content-between">
          <a href="#" class="navbar-brand d-flex align-items-center">
            <strong>todolist</strong>
          </a>
        </div>
      </div>
    </header>

    <main role="main" class='offset-3 col-6 mt-3'>

      <!-- todo item creation form -->
      <form action='<?= base_url ?>' method='post' class='form-inline justify-content-center'>
        <input type='hidden' name='action' value='new' />

        <div class='form-group'>
          <label for='task-title' class='sr-only'>title</label>
          <input id='task-title' class='form-control' name='title' type='text' placeholder='task title'>
        </div>

        <button type='submit' class='btn btn-primary ml-2'>add</button>
      </form>

      <!-- todo list -->
      <div class='list-group mt-3'>

        <!-- todo items -->
        <?php foreach($items as $item): ?>
          <div class='list-group-item d-flex justify-content-between align-items-center<?php if($item['done']): ?> list-group-item-success<?php else: ?> list-group-item-warning<?php endif;?>'>

            <div class='title'><?= $item['title'] ?></div>

            <!-- todo item controls -->
            <form action='<?= base_url ?>' method='post'>
              <input type='hidden' name='id' value='<?= $item['id'] ?>' />

              <div class='btn-group btn-group-sm'>

                <!-- todo item toggle button -->
                <button type='submit' name='action' value='toggle' class='btn btn-primary'>
                  <?php if ($item['done']) { ?>
                    undo
                  <?php } else { ?>
                    done
                  <?php } ?>
                </button>

                <!-- todo item delete button -->
                <button type='submit' name='action' value='delete' class='btn btn-danger'>
                  x
                </button>

              </div>
            </form>

          </div>
        <?php endforeach; ?>

      </div>

    </main>

    <!-- bootstrap javascript & dependencies -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/x+965dzo0rt7abk41jstqiaqvgrvzpbzo5smxkp4yfrvh+8abtte1pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-zmp7rvo3miykv+2+9j3uj46jbk0wlauadn689acwoqbbjisnjak/l8wvcwpipm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-chfqqxuzucnjsk3+mxmpniye6zbwh2imqe241ryiqjxymiz6ow/jmzq5stweulty" crossorigin="anonymous"></script>

  </body>
</html>
