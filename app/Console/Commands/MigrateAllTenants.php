<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Stancl\Tenancy\Facades\Tenancy;

class MigrateAllTenants extends Command
{
    protected $signature = 'tenants:migrate-all {--force : Force the operation}';

    protected $description = 'Run pending migrations on all tenant databases (safe for production deploy)';

    public function handle(): int
    {
        $tenants = \Stancl\Tenancy\Database\Models\Tenant::all();

        if ($tenants->isEmpty()) {
            $this->info('No tenants found. Skipping.');
            return self::SUCCESS;
        }

        $this->info("Migrating {$tenants->count()} tenant(s)...");

        foreach ($tenants as $tenant) {
            $this->line("  → Tenant: <fg=cyan>{$tenant->id}</>");

            try {
                tenancy()->initialize($tenant);

                \Illuminate\Support\Facades\Artisan::call('migrate', [
                    '--path'  => 'database/migrations/tenant',
                    '--force' => true,
                ]);

                $output = \Illuminate\Support\Facades\Artisan::output();
                if (trim($output)) {
                    $this->line('    ' . str_replace("\n", "\n    ", trim($output)));
                } else {
                    $this->line('    <fg=green>✓ Nothing to migrate</>');
                }
            } catch (\Throwable $e) {
                $this->error("    ✗ Error on tenant [{$tenant->id}]: " . $e->getMessage());
            } finally {
                tenancy()->end();
            }
        }

        $this->info('All tenants migrated successfully.');
        return self::SUCCESS;
    }
}
