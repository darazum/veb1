<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 28.07.2019
 * Time: 11:59
 */
class User
{
    private $_id;
    private $_name;
    private $_password;
    private $_email;

    public function __construct(string $name, string $password, string $email)
    {
        $this->_name = trim($name);
        $this->_password = $password;
        $this->_email = $email;
    }

    public function checkRegister(string &$error = ''): bool
    {
        if (!$this->_name) {
            $error = 'Имя не может быть пустым';
            return false;
        }

        if (strlen($this->_password) <= 6) {
            $error = 'Пароль должен содержать более 6 символов';
            return false;
        }

        if (strpos($this->_email, '@') === false) {
            $error = 'Некорректный email';
            return false;
        }

        if ($user = self::getByEmail($this->_email)) {
            $error = 'Пользователь с таким email уже зарегистрирован';
            return false;
        }

        return true;
    }

    public function addToDb()
    {
        $db = DB::instance();
        $insert = "INSERT INTO users (`name`, email, password)
                    VALUES(
                      :name, :email, :password
                    )";
        $db->exec($insert, __METHOD__, [
            'name' => $this->_name,
            'email' => $this->_email,
            'password' => self::getPasswordHash($this->_password)
        ]);

        $id = $db->getLastInsertId();
        $this->_id = $id;
        return $id;
    }

    public static function getPasswordHash(string $password)
    {
        return sha1($password . 'ids,.d,285');
    }

    /**
     * @param string $email
     * @return bool|User
     */
    public static function getByEmail(string $email)
    {
        $db = DB::instance();
        $select = "SELECT * FROM users WHERE email = :email";
        $data = $db->fetchOne($select, __METHOD__, ['email' => $email]);
        if (!$data) {
            return false;
        }

        $user = new self($data['name'], $data['password'], $data['email']);
        $user->_setId((int)$data['id']);
        return $user;
    }

    /**
     * @param int[] $ids
     * @return User[]|false
     */
    public static function getUsersByIds(array $ids)
    {
        $db = DB::instance();
        $idsStr = implode(',', $ids);
        $select = "SELECT * FROM users WHERE id IN($idsStr)";
        $data = $db->fetchAll($select, __METHOD__);
        if (!$data) {
            return false;
        }
        $ret = [];
        foreach ($data as $elem) {
            $ret[$elem['id']] = new self($elem['name'], $elem['password'], $elem['email']);
        }

        return $ret;
    }

    private function _setId(int $id)
    {
        $this->_id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->_name;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->_password;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->_email;
    }

}