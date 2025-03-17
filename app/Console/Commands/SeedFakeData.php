<?php

namespace App\Console\Commands;

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
        User::factory()
            ->count(20)
            ->create();
    }
}
