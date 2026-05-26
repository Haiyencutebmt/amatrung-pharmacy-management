<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class Phase1DatabaseTest extends TestCase
{
    /**
     * Test the required tables exist.
     */
    public function test_required_tables_exist(): void
    {
        $this->assertTrue(Schema::hasTable('users'));
        $this->assertTrue(Schema::hasTable('patients'));
        $this->assertTrue(Schema::hasTable('patient_user_links'));
        $this->assertTrue(Schema::hasTable('medical_records'));
        $this->assertTrue(Schema::hasTable('prescriptions'));
        $this->assertTrue(Schema::hasTable('prescription_items'));
        $this->assertTrue(Schema::hasTable('inventory_items'));
        $this->assertTrue(Schema::hasTable('inventory_batches'));
        $this->assertTrue(Schema::hasTable('stock_movements'));
        $this->assertTrue(Schema::hasTable('ai_suggestion_logs'));
        $this->assertTrue(Schema::hasTable('articles'));
        $this->assertTrue(Schema::hasTable('comments'));
        
        // Legacy tables should still exist
        $this->assertTrue(Schema::hasTable('medicinal_herbs'));
        $this->assertTrue(Schema::hasTable('packaged_products'));
    }
    
    public function test_medical_records_has_new_columns(): void
    {
        $this->assertTrue(Schema::hasColumn('medical_records', 'treatment_direction'));
        $this->assertTrue(Schema::hasColumn('medical_records', 'status'));
    }
    
    public function test_prescriptions_has_status(): void
    {
        $this->assertTrue(Schema::hasColumn('prescriptions', 'status'));
    }
}
