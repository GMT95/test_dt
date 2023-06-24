<?php

namespace Tests\Unit;

use Carbon\Carbon;
use DTApi\Helpers\TeHelper;
use PHPUnit\Framework\TestCase;

class willExpireAtTest extends TestCase
{
    /**
     *
     * @dataProvider datesDataProvider
     * @return void
     */
    public function test_diff_hours($due_time, $created_at, $expected)
    {

        $this->assertEquals($expected, TeHelper::willExpireAt($due_time, $created_at));
    }

    public function datesDataProvider()
    {
        return [
            [Carbon::create(2023, 06, 21), Carbon::create(2023, 06, 20), "2023-06-20 16:00:00"],
            [Carbon::create(2023, 06, 22), Carbon::create(2023, 06, 20), "2023-06-20 01:30:00"],
            [Carbon::create(2023, 06, 25), Carbon::create(2023, 06, 20), "2023-06-23 00:00:00"],
            [Carbon::create(2023, 06, 23, 18), Carbon::create(2023, 06, 20), "2023-06-23 18:00:00"],
            [Carbon::create(2023, 06, 23, 16, 32), Carbon::create(2023, 06, 20), "2023-06-23 16:32:00"],
        ];
    }
}
