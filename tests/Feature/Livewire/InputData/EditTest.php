<?php

use Livewire\Volt\Volt;

it('can render', function () {
    $component = Volt::test('input-data.edit');

    $component->assertSee('');
});
