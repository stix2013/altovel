<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Role; // Make sure Role model is used

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('email'); // Add new role_id column
        });

        // Data migration: Populate role_id based on existing string roles
        if (Schema::hasColumn('users', 'role') && Role::count() > 0) {
            $adminRole = Role::where('name', 'Admin')->first();
            $editorRole = Role::where('name', 'Editor')->first();
            $viewerRole = Role::where('name', 'Viewer')->first();

            if ($adminRole) {
                User::where('role', 'admin')->update(['role_id' => $adminRole->id]);
            }
            if ($editorRole) {
                User::where('role', 'editor')->update(['role_id' => $editorRole->id]);
            }
            if ($viewerRole) {
                User::where('role', 'viewer')->update(['role_id' => $viewerRole->id]);
                // Update users with no role or other roles to viewer by default
                User::whereNull('role_id')->whereNotNull('role')->update(['role_id' => $viewerRole->id]);
                User::whereNull('role')->update(['role_id' => $viewerRole->id]);
            }
        }

        Schema::table('users', function (Blueprint $table) {
            // Add foreign key constraint AFTER data migration
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');

            // Drop the old role column only if it exists
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->nullable()->after('email'); // Add back old role column
        });

        // Data migration: Populate string role based on role_id
        if (Schema::hasColumn('users', 'role_id') && Role::count() > 0) {
            $adminRole = Role::where('name', 'Admin')->first();
            $editorRole = Role::where('name', 'Editor')->first();
            $viewerRole = Role::where('name', 'Viewer')->first();

            if ($adminRole) {
                User::where('role_id', $adminRole->id)->update(['role' => 'admin']);
            }
            if ($editorRole) {
                User::where('role_id', $editorRole->id)->update(['role' => 'editor']);
            }
            if ($viewerRole) {
                User::where('role_id', $viewerRole->id)->update(['role' => 'viewer']);
            }
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role_id')) { // Check if column exists before trying to drop foreign key
                // Need to get the foreign key name to drop it by name if it's not the default users_role_id_foreign
                // For simplicity, we assume default naming or that it can be dropped by column name array
                try {
                    $table->dropForeign(['role_id']);
                } catch (\Exception $e) {
                    echo "Could not drop foreign key 'users_role_id_foreign'. It might not exist or have a different name. Error: " . $e->getMessage() . "\n";
                }
                $table->dropColumn('role_id');
            }
        });
    }
};
