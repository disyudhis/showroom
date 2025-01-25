<?php

use Livewire\Volt\Volt;

it('can render', function () {
    $component = Volt::test('input-data.store');

    $component->assertSee('');
});
