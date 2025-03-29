<?php

namespace App\Console\Commands;

use App\Models\Agent;
use App\Models\Claim;
use App\Models\Retiree;
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
        $agents = Agent::factory()
            ->count(10)
            ->create();

        $retirees = Retiree::factory()
            ->count(50)
            ->create();

        $retirees->each(fn ($retiree) => Claim::factory()->count(3)->for($retiree)->create());
    }
}
