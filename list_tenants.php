<?php
use App\Models\Tenant;
$tenants = Tenant::with('domains')->get();
foreach($tenants as $t) {
    echo 'Tenant: ' . $t->id . ' | Empresa: ' . $t->company_name . ' | Dominio: ' . ($t->domains->first() ? $t->domains->first()->domain : 'none') . PHP_EOL;
}
