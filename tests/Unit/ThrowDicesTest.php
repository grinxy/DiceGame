<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Http\Controllers\Api\GameController;

class ThrowDicesTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_throw_dices(): void
    {
        $gameController = new GameController();

        $result = $gameController->throwDices();

        $this->assertArrayHasKey('dice1_value', $result);
        $this->assertArrayHasKey('dice2_value', $result);
        $this->assertArrayHasKey('sum', $result);
        $this->assertArrayHasKey('result', $result);


        // valores dados entre 1 y 6
        $this->assertGreaterThanOrEqual(1, $result['dice1_value']);
        $this->assertGreaterThanOrEqual(1, $result['dice2_value']);

        $this->assertLessThanOrEqual(6, $result['dice1_value']);
        $this->assertLessThanOrEqual(6, $result['dice2_value']);



        // Verificar resultado válido
        $this->assertContains($result['result'], ['won', 'lost']);
    }

}
