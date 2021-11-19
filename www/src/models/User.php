<?php


declare(strict_types=1);

namespace App\Models;

use App\Libs\Database;
use Exception;

class User
{
    private $_db;
    private $_login;

    public function __construct()
    {
        $this->_db = new Database();
    }

    public function setUser(string $login): void
    {
        $this->_login = $login;
    }

    public function login($login, $password): bool
    {
        $user = $this->_db
            ->query('SELECT * FROM users WHERE user_login=:login')
            ->bind(':login', $login)
            ->execute()
            ->getRow();

        $hashed_password = $user->user_pswd_hash;

        if ($this->_verify($password, $hashed_password)) {
            $this->_setToken();
            return true;
        } else {
            return false;
        }
    }

    //verifycation for example password_verify($password, $hashed_password)
    private function _verify($password, $hashed_password): bool
    {
        return true;
    }

    /**
     * set token on client side (for example signature key)
     * */
    private function _setToken(): void
    {
    }

    /**
     * chek user token on client side
    * */
    private function _isValidToken(): bool
    {
        return true;
    }

    private function _validCurency(string $currency): void
    {
        if (!in_array($currency, ['usd', 'eur', 'rub'])) {
            throw new Exception("Currency \"{$currency}\" is incorrect");
        }
    }

    public function getBalance(string $currency): float
    {
        $user = $this->_db
            ->query(
                'SELECT balance_value FROM balance 
                 WHERE user_id IN (SELECT user_id FROM users WHERE user_login=:login)
                 AND balance_currency =:currency'
            )
            ->bind(':login', $this->_login)
            ->bind(':currency', $currency)
            ->execute()
            ->getRow();
        return (float) $user->balance_value ?? 0.00;
    }

    public function setBalance(string $currency, float $money)
    {
        $this->_db
            ->query(
                'UPDATE balance SET balance_value=:money
                 WHERE user_id IN (SELECT user_id FROM users WHERE user_login=:login) 
                 AND balance_currency =:currency'
            )
            ->bind(':login', $this->_login)
            ->bind(':currency', $currency)
            ->bind(':money', $money)
            ->execute();
    }

    public function addBalance(string $currency, float $money)
    {
        $this->_db
            ->query(
                'UPDATE balance SET balance_value=balance_value+:money
                 WHERE user_id IN (SELECT user_id FROM users WHERE user_login=:login)
                 AND balance_currency =:currency'
            )
            ->bind(':login', $this->_login)
            ->bind(':currency', $currency)
            ->bind(':money', $money)
            ->execute();
    }
}
