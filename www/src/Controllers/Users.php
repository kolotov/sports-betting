<?php


declare(strict_types=1);

namespace Sports\Betting\Controllers;

use Error;
use Sports\Betting\Models\User;

class Users extends AbstractController
{
    private User $_user;

    public function __construct()
    {
        $this->_user = $this->loadModel('User');
    }


    //API /api/users/{login}/action/bet + POST DATA
    public function putUsersBet(int $id, array $bet, int $winner): array
    {
        $this->_user->setUser($id);

        if ((is_numeric($bet['sum']) === false)
            || (is_numeric($bet['ratio']) === false)
            || (is_numeric($bet['result']) === false)
        ) {
            throw new Error('Incorrect args', 403);
        }

        $sum = (int) $bet['sum'];
        $ratio = round($bet['ratio'], 2);
        $result = (int) $bet['result'];


        if ($sum < 1 || $sum > 500) {
            throw new Error('The bet amount must be in the range from 1 to 500', 403);
        }

        if ($ratio < 1.01 || $ratio > 40.40) {
            throw new Error('Ratio must range from 1.01 to 40.00', 403);
        }



        //check balance
        $balance = $this->_user->getBalance($bet['currency']);

        if ($balance < $sum) {
            throw new Error('Not enough money in the account', 403);
        }

        //checking who winner
        $change_balance = ($result === $winner) ?
            //calc money if win
            $sum * $ratio - $sum :

            //calc money if losing
            -1 * $sum;

        $this->_user->addBalance($bet['currency'], $change_balance);

        $location = "/api/users/{$id}/action/balance/{$bet['currency']}";
        return ['result' => true, 'location' => $location];
    }

    //API /api/users/{login}/action/balance/{$currency}
    public function getUsersBalance(int $id, string $currency): array
    {
        $this->_user->setUser($id);
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
    public function getUsersInfo(int $id): array
    {
        $this->_user->setUser($id);
        $user = $this->_user->getUserInfo();

        return ['result' => $user];
    }
}
