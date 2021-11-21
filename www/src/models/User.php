<?php


declare(strict_types=1);

namespace Sports\Betting\Models;

use Sports\Betting\Libs\Database;

use Exception;
use stdClass;

class User extends AbstractModel
{
    private $_id;
    private $_db;

    public function __construct()
    {
        $this->_db = new Database();
    }

    public function setUser(string $login): void
    {
        $count = $this->_db
            ->query('SELECT user_id FROM users WHERE user_login=:login')
            ->bind(':login', $login)
            ->execute()
            ->getCount();

        if ($count === 0) {
            throw new Exception("Not found user {$login}", 404);
        }
        $user = $this->_db->getRow();
        $this->_id =  $user->user_id;
    }

    public function login($login, $password): bool
    {
        $user = $this->_db
            ->query('SELECT * FROM users WHERE user_login=:login')
            ->bind(':login', $login)
            ->execute()
            ->getRow();

        return $this->_verify($password, $user->user_pswd_hash);
    }

    //verifycation for example password_verify($password, $hashed_password)
    private function _verify($password, $hashed_password): bool
    {
        return true;
    }


    private function _validCurency(string $currency): bool
    {
        if (!in_array($currency, ['usd', 'eur', 'rub'])) {
            throw new Exception("Currency \"{$currency}\" is incorrect", 400);
        }
        return true;
    }

    public function getBalance(string $currency): float
    {
        $this->_validCurency($currency);
        $user = $this->_db
            ->query(
                'SELECT balance_value FROM balance 
                 WHERE user_id =:id
                 AND balance_currency =:currency'
            )
            ->bind(':id', $this->_id)
            ->bind(':currency', $currency)
            ->execute()
            ->getRow();

        return (float) ($user->balance_value ?? 0.00);
    }

    public function setBalance(string $currency, float $money)
    {
        $this->_validCurency($currency);

        $this->_db
            ->query(
                'UPDATE balance SET balance_value=:money 
                 WHERE user_id =:id 
                 AND balance_currency =:currency'
            )
            ->bind(':id', $this->_id)
            ->bind(':currency', $currency)
            ->bind(':money', $money)
            ->execute();
    }

    public function addBalance(string $currency, float $money)
    {
        $this->_validCurency($currency);
        $this->_db
            ->query(
                'UPDATE balance SET balance_value=balance_value+:money
                 WHERE user_id =:id
                 AND balance_currency =:currency'
            )
            ->bind(':id', $this->_id)
            ->bind(':currency', $currency)
            ->bind(':money', $money)
            ->execute();
    }

    public function getUserInfo(): array
    {
        $user = $this->_db
            ->query('SELECT user_id, user_login, user_name FROM users WHERE user_id=:id')
            ->bind(':id', $this->_id)
            ->execute()
            ->getRow(true);

        return $user;
    }


    public function getUsers(): array
    {
        $users = $this->_db
            ->query('SELECT user_login, user_name FROM users')
            ->execute();

        if ($users->getCount() === 0) {
            return [];
        }

        return $users->getAll();
    }
}
