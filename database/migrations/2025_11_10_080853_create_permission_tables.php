<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Migration ini dibuat otomatis oleh package Spatie Laravel Permission
     * Untuk membuat sistem Role & Permission yang fleksibel
     */
    public function up(): void
    {
        // Mengambil konfigurasi dari file config/permission.php
        $teams = config('permission.teams');                    // Apakah menggunakan fitur teams/multi-tenancy
        $tableNames = config('permission.table_names');         // Nama-nama tabel yang akan dibuat
        $columnNames = config('permission.column_names');       // Nama-nama kolom custom
        $pivotRole = $columnNames['role_pivot_key'] ?? 'role_id';           // Kolom foreign key untuk role
        $pivotPermission = $columnNames['permission_pivot_key'] ?? 'permission_id'; // Kolom foreign key untuk permission

        // Validasi: Pastikan config sudah di-load
        throw_if(empty($tableNames), Exception::class, 'Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        throw_if($teams && empty($columnNames['team_foreign_key'] ?? null), Exception::class, 'Error: team_foreign_key on config/permission.php not loaded. Run [php artisan config:clear] and try again.');

        // 1. Tabel 'permissions' - Menyimpan daftar permission/izin akses
        // Contoh: create-post, edit-post, delete-post, view-users
        Schema::create($tableNames['permissions'], static function (Blueprint $table) {
            $table->bigIncrements('id');     // ID permission (auto increment)
            $table->string('name');          // Nama permission (contoh: 'create-appointment')
            $table->string('guard_name');    // Guard yang digunakan (contoh: 'web', 'api')
            $table->timestamps();            // created_at & updated_at

            $table->unique(['name', 'guard_name']); // Kombinasi name + guard_name harus unik
        });

        // 2. Tabel 'roles' - Menyimpan daftar role/peran
        // Contoh: admin, doctor, patient
        Schema::create($tableNames['roles'], static function (Blueprint $table) use ($teams, $columnNames) {
            $table->bigIncrements('id');     // ID role (auto increment)
            
            // Jika menggunakan fitur teams (multi-tenancy)
            if ($teams || config('permission.testing')) {
                $table->unsignedBigInteger($columnNames['team_foreign_key'])->nullable(); // ID team/organisasi
                $table->index($columnNames['team_foreign_key'], 'roles_team_foreign_key_index');
            }
            
            $table->string('name');          // Nama role (contoh: 'admin', 'doctor', 'patient')
            $table->string('guard_name');    // Guard yang digunakan (contoh: 'web', 'api')
            $table->timestamps();            // created_at & updated_at

            // Unique constraint berdasarkan teams atau tidak
            if ($teams || config('permission.testing')) {
                $table->unique([$columnNames['team_foreign_key'], 'name', 'guard_name']);
            } else {
                $table->unique(['name', 'guard_name']); // Kombinasi name + guard_name harus unik
            }
        });

        // 3. Tabel 'model_has_permissions' - RelasiMany-to-Many antara Model (User) dan Permission
        // Untuk memberikan permission langsung ke user tertentu (bypass role)
        Schema::create($tableNames['model_has_permissions'], static function (Blueprint $table) use ($tableNames, $columnNames, $pivotPermission, $teams) {
            $table->unsignedBigInteger($pivotPermission);        // ID permission

            $table->string('model_type');                        // Tipe model (contoh: 'App\Models\User')
            $table->unsignedBigInteger($columnNames['model_morph_key']); // ID model (contoh: user_id)
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_permissions_model_id_model_type_index');

            // Foreign key ke tabel permissions
            $table->foreign($pivotPermission)
                ->references('id')
                ->on($tableNames['permissions'])
                ->onDelete('cascade'); // Jika permission dihapus, relasi ikut terhapus

            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key']);
                $table->index($columnNames['team_foreign_key'], 'model_has_permissions_team_foreign_key_index');

                $table->primary([$columnNames['team_foreign_key'], $pivotPermission, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary');
            } else {
                // Primary key gabungan (composite key)
                $table->primary([$pivotPermission, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary');
            }
        });

        // 4. Tabel 'model_has_roles' - Relasi Many-to-Many antara Model (User) dan Role
        // User bisa punya banyak role, Role bisa dimiliki banyak user
        Schema::create($tableNames['model_has_roles'], static function (Blueprint $table) use ($tableNames, $columnNames, $pivotRole, $teams) {
            $table->unsignedBigInteger($pivotRole);              // ID role

            $table->string('model_type');                        // Tipe model (contoh: 'App\Models\User')
            $table->unsignedBigInteger($columnNames['model_morph_key']); // ID model (contoh: user_id)
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_roles_model_id_model_type_index');

            // Foreign key ke tabel roles
            $table->foreign($pivotRole)
                ->references('id')
                ->on($tableNames['roles'])
                ->onDelete('cascade'); // Jika role dihapus, relasi ikut terhapus

            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key']);
                $table->index($columnNames['team_foreign_key'], 'model_has_roles_team_foreign_key_index');

                $table->primary([$columnNames['team_foreign_key'], $pivotRole, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary');
            } else {
                // Primary key gabungan (composite key)
                $table->primary([$pivotRole, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary');
            }
        });

        // 5. Tabel 'role_has_permissions' - Relasi Many-to-Many antara Role dan Permission
        // Role bisa punya banyak permission, Permission bisa dimiliki banyak role
        Schema::create($tableNames['role_has_permissions'], static function (Blueprint $table) use ($tableNames, $pivotRole, $pivotPermission) {
            $table->unsignedBigInteger($pivotPermission);        // ID permission
            $table->unsignedBigInteger($pivotRole);              // ID role

            // Foreign key ke tabel permissions
            $table->foreign($pivotPermission)
                ->references('id')
                ->on($tableNames['permissions'])
                ->onDelete('cascade');

            // Foreign key ke tabel roles
            $table->foreign($pivotRole)
                ->references('id')
                ->on($tableNames['roles'])
                ->onDelete('cascade');

            // Primary key gabungan
            $table->primary([$pivotPermission, $pivotRole], 'role_has_permissions_permission_id_role_id_primary');
        });

        // Clear cache permission setelah migration
        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));
    }

    /**
     * Reverse the migrations.
     * Menghapus semua tabel yang dibuat di method up()
     */
    public function down(): void
    {
        $tableNames = config('permission.table_names');

        throw_if(empty($tableNames), Exception::class, 'Error: config/permission.php not found and defaults could not be merged. Please publish the package configuration before proceeding, or drop the tables manually.');

        // Hapus tabel dalam urutan terbalik (untuk menghindari foreign key constraint error)
        Schema::drop($tableNames['role_has_permissions']);    // 5. Hapus pivot role-permission
        Schema::drop($tableNames['model_has_roles']);         // 4. Hapus pivot user-role
        Schema::drop($tableNames['model_has_permissions']);   // 3. Hapus pivot user-permission
        Schema::drop($tableNames['roles']);                   // 2. Hapus tabel roles
        Schema::drop($tableNames['permissions']);             // 1. Hapus tabel permissions
    }
};