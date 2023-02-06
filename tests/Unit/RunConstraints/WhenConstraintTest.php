<?php

use Spatie\ShortSchedule\RunConstraints\WhenConstraint;

it('will run when the closure allows it', function () {
    $constraint = new WhenConstraint(fn () => true);
    expect($constraint->shouldRun())->toBeTrue();

    $constraint = new WhenConstraint(fn () => false);
    expect($constraint->shouldRun())->toBeFalse();
});
