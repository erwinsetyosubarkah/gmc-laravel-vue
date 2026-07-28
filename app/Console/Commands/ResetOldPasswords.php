<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class ResetOldPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:reset-passwords {--password=12345678 : Default password for old users} {--force : Skip confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset password for old users to default password using the new hash scheme.';

    public function handle()
    {
        $password = $this->option('password');
        $force = $this->option('force');

        if (! $force) {
            if (! $this->confirm("Reset semua password user ke default '{$password}'?")) {
                $this->info('Operasi dibatalkan.');
                return 0;
            }
        }

        $hashed = hash('sha256', $password);

        $count = 0;
        User::chunk(100, function (Collection $users) use ($hashed, &$count) {
            foreach ($users as $user) {
                $user->password = Hash::make($hashed);
                $user->save();
                $count++;
            }
        });

        $this->info("Selesai. Password berhasil di-reset untuk {$count} user.");
        $this->info("Password default sekarang: {$password}");
        return 0;
    }
}
