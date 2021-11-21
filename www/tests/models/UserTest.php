<?php

declare(strict_types=1);

namespace Sports\Betting\Tests\Models;

use Sports\Betting\Models\User;
use PHPUnit\Framework\TestCase;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

class UserTest extends TestCase
{
    private $_user;

    public function setUp(): void
    {
        $this->_user = new User();
        $login = 'bagration';
        $this->_user->setUser($login);
    }

    /**
     * @dataProvider balanceProvider
     * */
    public function testBalance(string $currency, float $balance, float $add_money): void
    {
        $this->_user->setBalance($currency, $balance);
        $this->_user->addBalance($currency, $add_money);

        $expected_money = $balance + $add_money;
        $this->assertEquals($expected_money, $this->_user->getBalance($currency));
    }

    public function balanceProvider(): array
    {
        return [
            'set 1000.90 usd and add 100.10 '  => ['usd', 1000.90, 100.10],
            'set 555.11 eur and add -0.90' => ['eur', 555.11, -0.90],
            'set 300 rub and add 200' => ['rub', 300, 200],
        ];
    }
}
