<?php


declare(strict_types=1);

namespace Sports\Betting\Controllers;

use Sports\Betting\Models\User;
use Exception;
use stdClass;

class Users extends AbstractController
{
    private User $_user;

    public function __construct()
    {
        $this->_user = $this->loadModel('User');
    }


    //API /api/users/{login}/action/bet + POST DATA
    public function putUsersBet(string $login, array $bet, int $winner): array
    {
        $this->_user->setUser($login);

        //check balance
        $balance = $this->_user->getBalance($bet['currency']);

        if ($balance < $bet['sum']) {
            throw new Exception('Not enough money in the account', 403);
        }

        //checking who winner
        if ($bet['result'] === $winner) {
            //calc money if win
            $change_balance= $bet['sum'] * $bet['ratio'] - $bet['sum'];
        } else {
            //calc money if losing
            $change_balance = -1 * $bet['sum'];
        }


        print_r($balance. "\n");
        print_r($change_balance);

        //$this->_user->addBalance('usd', 1000);

        $this->_user->setBalance('usd', $balance + $change_balance);

        $location = "/api/users/{$login}/action/balance/{$bet['currency']}";
        return ['result' => true, 'location' => $location];
    }

    //API /api/users/{login}/action/balance/{$currency}
    public function getUsersBalance($login, $currency): array
    {
        $this->_user->setUser($login);
        $balance = $this->_user->getBalance($currency);
        return ['result' => $balance];
    }

    //API /api/users/action/list
    public function getUsersList(): array
    {
        $users = $this->_user->getUsers();

        return ['result' => $users];
    }

    //API /api/users/{login}/action/info
    public function getUsersInfo($login): array
    {
        $this->_user->setUser($login);
        $user = $this->_user->getUserInfo();

        return ['result' => $user];
    }
}
