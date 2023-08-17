<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class TestRelation extends Command
{
    protected $signature = 'rel:db';

    protected $description = 'Command description';

    public function handle()
    {
        // $this->attach();
        // $this->sync();
        // $this->detach();
        $this->syncWithoutDetaching();
    }

    public function attach()
    {
        $user = User::find(1);

        $user->roles()->attach([3]);
    }
    public function sync()
    {
        $user = User::find(1);

        $user->roles()->sync([1, 2]);
    }
    public function detach()
    {
        $user = User::find(1);

        $user->roles()->detach();
    }
    public function syncWithoutDetaching()
    {
        $user = User::find(1);

        $user->roles()->syncWithoutDetaching([1, 2]);
    }
}
