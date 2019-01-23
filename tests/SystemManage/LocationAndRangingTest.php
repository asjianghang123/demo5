<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Mongs\SiteLte;
use App\User;

class LocationAndRangingTest extends TestCase
{
    use DatabaseMigrations;

    public function testGetCoordinateByCell()
    {
        $mock = factory(SiteLte::class)->create();
        $user = User::create(['type' => 'admin']);
        $this->actingAs($user)->json('post', '/locationAndRanging/getCoordinateByCell', ['cell' => $mock->cellName])
            ->seeJson(['cellName' => $mock->cellName, 'siteName' => $mock->siteName,
                'tac' => $mock->tac, 'lng' => $mock->longitudeBD, 'lat' => $mock->latitudeBD,
                'dir' => $mock->dir, 'band' => $mock->band]);
    }

}
