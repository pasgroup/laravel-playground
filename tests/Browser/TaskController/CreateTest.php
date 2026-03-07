<?php

namespace Tests\Browser\TaskController;

use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class CreateTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');
    }

    #[Test]
    public function itDisplaysCreatePageWithForm(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('tasks.create'))
                ->assertPresent('@create-heading')
                ->assertPresent('@create-back-link')
                ->assertPresent('@create-form')
                ->assertPresent('@create-title-input')
                ->assertPresent('@create-submit-btn');
        });
    }

    #[Test]
    public function itNavigatesBackToIndexFromCreate(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('tasks.create'))
                ->click('@create-back-link')
                ->assertRouteIs('tasks.index');
        });
    }
}
