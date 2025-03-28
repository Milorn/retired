<?php

namespace App\Console\Commands;

use App\Enums\UserType;
use App\Models\Claim;
use App\Models\User;
use Illuminate\Console\Command;

class SeedFakeData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seed-fake-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed with fake data for testing purposes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $agents = User::factory()
            ->count(5)
            ->type(UserType::Agent)
            ->create();

        $retirees = User::factory()
            ->count(30)
            ->type(UserType::Retired)
            ->create();

        $retirees->each(fn ($retired) => Claim::factory()->count(3)->for($retired)->create());
    }
}
