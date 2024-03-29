<?php

require_once ("common/page.php");
require_once ("common/a_content.php");
require_once ("common/db_helper.php");
class the_content extends \common\a_content {

    public function __construct(){
        $this->isProtected = false;
        parent::__construct();
        $this->check_user_data();
    }

    private bool $try_login = false;
    private string $raw_user = '';
    private string $raw_password = '';

    private function identify(): bool{
        return \common\db_helper::get_instance()->user_exists($this->raw_user) &&
                \common\db_helper::get_instance()->auth_ok($this->raw_user, $this->raw_password);
    }
    private function check_user_data(): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' || isset($_GET['exit'])) {
			if (isset($_GET['exit']) && $_GET['exit']==1){
                unset($_SESSION['user']);
				unset($_SESSION['user_id']);
            } else {
                if (isset($_POST['login'])) {
                    $this->try_login = true;
                    $user = $_POST['login'];
                    $this->raw_user = htmlspecialchars($user);
                    if (isset($_POST['password']))
                        $this->raw_password = htmlspecialchars($_POST['password']);
                    if ($this->identify()) {
                        $_SESSION['user'] = htmlspecialchars($user);
						$_SESSION['user_id']=\common\db_helper::get_instance()->get_user_id($_SESSION['user']);
                        header("Location: index.php");
                    }
                }
            }
        }
    }

    private function show_login_error(){
        ?>
        <div class="alert alert-danger fw-bold text-center">
            Неверное имя пользователя или пароль!
        </div>
        <?php
    }
    public function show_content(): void
    {
        if (!isset($_SESSION['user'])){
            if ($this->try_login)
                $this->show_login_error();
            ?>
            <div class="m-auto card p-2 bg-primary bg-gradient bg-opacity-25" style="width: 500px;">
            <form action="login.php" method="post">
                <div class="row p-2 mb-2">
                    <div class="col-2 align-self-center">
                        <label for="login" class="text-center">Имя:</label>
                    </div>
                    <div class="col align-self-center">
                        <input class="form-control form-control-md" type="text" value="<?php print $this->raw_user;?>" placeholder="Введите логин" name="login" id="login">
                    </div>
                </div>

                <div class="row p-2 mb-2">
                    <div class="col-2 align-self-center">
                        <label for="password" class="text-center">Пароль:</label>
                    </div>
                    <div class="col align-self-center">
                        <input class="form-control form-control-md" type="password" value="<?php print $this->raw_password;?>" placeholder="Введите пароль" name="password" id="password">
                    </div>
                </div>

                <div class="row mb-2 mt-4">
                    <div class="col">
                        <input type="submit" value="Отправить" class="form-control-color btn btn-primary w-50">
                    </div>
                </div>
            </form>
            </div>
            <?php
        } 
    }
}

$content = new the_content();
new \common\page($content);
