<?php

use Livewire\Volt\Volt;

it('can render', function () {
    $component = Volt::test('dashboard.list-car');

    $component->assertSee('');
});
