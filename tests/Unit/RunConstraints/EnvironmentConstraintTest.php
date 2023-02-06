<?php

use Spatie\ShortSchedule\RunConstraints\EnvironmentConstraint;

uses(Spatie\ShortSchedule\Tests\TestCase::class);

it('will constraint execution based on the environment', function () {
    $constraint = new EnvironmentConstraint(['local', 'production']);
    expect($constraint->shouldRun())->toBeFalse();

    $constraint = new EnvironmentConstraint(['local', 'production', 'testing']);
    expect($constraint->shouldRun())->toBeTrue();
});
