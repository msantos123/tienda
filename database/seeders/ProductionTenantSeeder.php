<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ProductionTenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Obtener el dominio central de producción desde la configuración
        $centralDomain = parse_url(config('app.url'), PHP_URL_HOST);
        
        if (!$centralDomain || $centralDomain === 'localhost') {
            $this->command->error("Por favor, asegúrate de que APP_URL esté configurado correctamente en tus variables de entorno antes de ejecutar este seeder.");
            return;
        }

        $tenantsData = [
            [
                'id' => 'tenant1',
                'company_name' => 'Empresa Uno S.A.S',
                'admin_name' => 'Admin Uno',
                'admin_email' => 'admin@tenant1.com',
                'admin_password' => 'password123', // Puedes cambiar esta contraseña antes de hacer push
            ],
            [
                'id' => 'tenant2',
                'company_name' => 'Empresa Dos S.A.S',
                'admin_name' => 'Admin Dos',
                'admin_email' => 'admin@tenant2.com',
                'admin_password' => 'password123', // Puedes cambiar esta contraseña antes de hacer push
            ],
            [
                'id' => 'vertice',
                'company_name' => 'Vertice Studio',
                'admin_name' => 'Manuel Santos',
                'admin_email' => 'msantos.luizaga@gmail.com',
                'admin_password' => 'password123', // Puedes cambiar esta contraseña antes de hacer push
            ],
        ];

        foreach ($tenantsData as $data) {
            // Verificar si el tenant ya existe en la central
            if (Tenant::where('id', $data['id'])->exists()) {
                $this->command->info("El tenant {$data['id']} ya existe. Saltando...");
                continue;
            }

            // Crear el tenant
            $tenant = Tenant::create([
                'id' => $data['id'],
                'company_name' => $data['company_name'],
            ]);

            // Asociar el dominio dinámico de producción (ej: id.tu-app.laravel.run)
            $tenant->domains()->create([
                'domain' => $data['id'] . '.' . $centralDomain,
            ]);

            // Inicializar para crear el administrador dentro de su base de datos/esquema
            tenancy()->initialize($tenant);

            User::create([
                'name' => $data['admin_name'],
                'email' => $data['admin_email'],
                'password' => Hash::make($data['admin_password']),
            ]);

            tenancy()->end();

            $this->command->info("Tenant {$data['id']} ({$data['company_name']}) creado con dominio: {$data['id']}.{$centralDomain}");
        }
    }
}
