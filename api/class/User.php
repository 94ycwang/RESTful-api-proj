<?php
require_once __DIR__ . '/Errorcode.php';
class User
{
    /**
     * database connection object
     * @var PDO
     */
    private $_db;

    /**
     * User constructor,
     * @param PDO $_db
     */
    public function __construct(PDO $_db)
    {
        $this->_db = $_db;
    }

    /**
     * Input validation
     *
     * @param $val
     * @return bool
     */
    private function notValid($val)
    {
        if ($val === 0 or $val === "0") {
            return False;
        }
        return empty($val);
    }

    /**
     * User register
     * @param $username string 
     * @param $password string 
     * @return array
     * @throws Exception
     */
    public function register($username, $password)
    {
        if ($this->notValid($username)) {
            throw new Exception("Username cannot be null!", Errorcode::USERNAME_CANNOT_NULL);
        }
        if ($this->notValid($password)) {
            throw new Exception("Password cannot be null!", Errorcode::USERPASS_CANNOT_NULL);
        }

        if ($this->isUsernameExists($username)) {
            throw new Exception("Username already existed!", Errorcode::USERNAME_EXIST);
        }

        $sql = "insert into `user`(`name`,`password`,`create_time`) values(:username,:password,:addtime)";
        $addtime = date("Y-m-d H:i:s", time());
        $sm = $this->_db->prepare($sql);

        $password = $this->_md5($password);

        $sm->bindParam(':username', $username);
        $sm->bindParam(':password', $password);
        $sm->bindParam(':addtime', $addtime);

        if (!$sm->execute()) {
            throw new Exception("Failed to register!", Errorcode::REGISTER_FAIL);
        }
        return [
            'username' => $username,
            'user_id' => $this->_db->lastInsertId(),
            'addtime' => $addtime
        ];
    }


    /**
     * Uer login
     * @param $username 
     * @param $password
     * @return mixed
     * @throws Exception
     */
    public function login($username, $password)
    {
        if ($this->notValid($username)) {
            throw new Exception("Username cannot be null!", Errorcode::USERNAME_CANNOT_NULL);
        }
        if ($this->notValid($password)) {
            throw new Exception("Password cannot be null!", Errorcode::USERPASS_CANNOT_NULL);
        }
        $sql = "select * from `user` where `name`=:username and `password` =:password";
        $password = $this->_md5($password);
        $sm = $this->_db->prepare($sql);
        $sm->bindParam(':username', $username);
        $sm->bindParam(':password', $password);
        if (!$sm->execute()) {
            throw new Exception("Failed to login!", Errorcode::LOGIN_FAIL);
        }
        $re = $sm->fetch(PDO::FETCH_ASSOC);
        if (!$re) {
            throw new Exception("Incorrect username or password!", Errorcode::USERNAME_OR_PASSWORD_ERROR);
        }
        return $re;
    }


    /**
     * Check whether username exists
     * @param $username
     * @return bool
     */
    private function isUsernameExists($username)
    {
        $sql = "select * from `user` where `name` = :username";
        $sm = $this->_db->prepare($sql);
        $sm->bindParam(':username', $username);
        $sm->execute();
        $re = $sm->fetch(PDO::FETCH_ASSOC);
        return !empty($re);
    }

    private function _md5($pass)
    {
        return md5($pass . SALT);
    }
}
