<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Tạo các quyền (permissions) và vai trò (roles) cho hệ thống
     * Website quản lý nhà thuốc y học cổ truyền gia đình AmaTrung.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // -------------------------------------------------------
        // 1. Original 13 permissions (KEEP ALL)
        // -------------------------------------------------------
        $permissions = [
            'manage_users',
            'manage_roles_permissions',
            'view_patients',
            'create_patients',
            'edit_patients',
            'create_medical_records',
            'create_prescriptions',
            'dispense_prescriptions',
            'manage_inventory',
            'manage_articles',
            'moderate_comments',
            'view_statistics',
            'use_ai_suggestion',
        ];

        // -------------------------------------------------------
        // 2. New permissions required by controllers
        // -------------------------------------------------------
        $newPermissions = [
            // MedicalRecordController middleware
            'medical_records.view',
            'medical_records.create',
            'medical_records.edit',
            'medical_records.delete',

            // PrescriptionController middleware
            'prescriptions.view',
            'prescriptions.create',
            'prescriptions.delete',

            // Medical record attachments
            'view_medical_record_attachments',
            'upload_medical_record_attachments',

            // InventoryController – batch import
            'import_inventory_batches',
        ];

        // Merge all permissions and create using findOrCreate (idempotent)
        $allPermissions = array_merge($permissions, $newPermissions);

        foreach ($allPermissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // -------------------------------------------------------
        // Create roles and assign permissions
        // -------------------------------------------------------

        // 1. Admin – gets ALL permissions
        $roleAdmin = Role::findOrCreate('admin');
        $roleAdmin->syncPermissions(Permission::all());

        // 2. Practitioner – clinical + inventory + AI
        $rolePractitioner = Role::findOrCreate('practitioner');
        $rolePractitioner->syncPermissions([
            // Patient permissions
            'view_patients',
            'create_patients',
            'edit_patients',
            // Medical record permissions (original + new)
            'create_medical_records',
            'medical_records.view',
            'medical_records.create',
            'medical_records.edit',
            'medical_records.delete',
            // Prescription permissions (original + new)
            'create_prescriptions',
            'prescriptions.view',
            'prescriptions.create',
            'prescriptions.delete',
            // Dispensing & inventory
            'dispense_prescriptions',
            'manage_inventory',
            'import_inventory_batches',
            // Medical record attachments
            'view_medical_record_attachments',
            'upload_medical_record_attachments',
            // AI
            'use_ai_suggestion',
        ]);

        // 3. Staff – front-desk & dispensing duties
        $roleStaff = Role::findOrCreate('staff');
        $roleStaff->syncPermissions([
            'view_patients',
            'create_patients',
            'dispense_prescriptions',
            'manage_inventory',
            'view_medical_record_attachments',
        ]);

        // 4. User – no backend permissions (standard frontend access only)
        $roleUser = Role::findOrCreate('user');
        // Users might not have explicit backend permissions, just standard frontend access.
    }
}
