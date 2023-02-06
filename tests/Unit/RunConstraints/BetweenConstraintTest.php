<?php

use Spatie\ShortSchedule\RunConstraints\BetweenConstraint;
use Spatie\TestTime\TestTime;

it('will constraint execution based on time', function () {
    TestTime::freeze('Y-m-d H:i:s', '2020-01-01 08:59:59');

    $constraint = new BetweenConstraint('09:00', '17:00');

    expect($constraint->shouldRun())->toBeFalse();

    TestTime::addSecond();
    expect($constraint->shouldRun())->toBeTrue();

    TestTime::freeze('Y-m-d H:i:s', '2020-01-01 17:00:00');
    expect($constraint->shouldRun())->toBeTrue();

    TestTime::addSecond();
    expect($constraint->shouldRun())->toBeFalse();
});

test('the constraint will work for overflowing hours', function () {
    TestTime::freeze('Y-m-d H:i:s', '2020-01-01 20:59:59');

    $constraint = new BetweenConstraint('21:00', '01:00');

    expect($constraint->shouldRun())->toBeFalse();

    TestTime::addSecond();
    expect($constraint->shouldRun())->toBeTrue();

    TestTime::freeze('Y-m-d H:i:s', '2020-01-02 01:00:00');
    expect($constraint->shouldRun())->toBeTrue();

    TestTime::addSecond();
    expect($constraint->shouldRun())->toBeFalse();
});
