<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LegacyToNewInventoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:migrate-legacy {--dry-run : Only show what will be migrated} {--execute : Execute the migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate legacy medicinal_herbs and packaged_products to the new inventory system.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $isExecute = $this->option('execute');

        if (!$isDryRun && !$isExecute) {
            $this->error('You must specify either --dry-run or --execute.');
            return;
        }

        $this->info('Starting legacy inventory migration (Idempotent Phase 1C)...');

        $hasHerbs = DB::getSchemaBuilder()->hasTable('medicinal_herbs');
        $hasPackaged = DB::getSchemaBuilder()->hasTable('packaged_products');

        $herbs = $hasHerbs ? DB::table('medicinal_herbs')->get() : collect();
        $packaged = $hasPackaged ? DB::table('packaged_products')->get() : collect();

        $stats = [
            'total_items' => 0,
            'oral_items' => 0,
            'external_items' => 0,
            'qty_positive' => 0,
            'qty_zero' => 0,
            'qty_negative' => 0,
            'total_batches' => 0,
            'available_batches' => 0,
            'unknown_expiry_batches' => 0,
            'no_unit' => 0,
            'skipped_already_migrated' => 0,
        ];

        $reportRows = [];
        $duplicates = [];
        $externalItemsList = [];
        $noUnitItemsList = [];

        $migratedHerbs = DB::table('inventory_items')->where('legacy_source_table', 'medicinal_herbs')->pluck('legacy_source_id')->toArray();
        $migratedPkgs = DB::table('inventory_items')->where('legacy_source_table', 'packaged_products')->pluck('legacy_source_id')->toArray();

        // Process Herbs
        foreach ($herbs as $herb) {
            if (in_array($herb->id, $migratedHerbs)) {
                $stats['skipped_already_migrated']++;
                continue;
            }

            $stats['total_items']++;
            
            $qty = (float) ($herb->stock_quantity ?? 0);
            if ($qty > 0) $stats['qty_positive']++;
            elseif ($qty < 0) $stats['qty_negative']++;
            else $stats['qty_zero']++;
            
            $unit = $herb->unit;
            if (empty($unit)) {
                $stats['no_unit']++;
                $noUnitItemsList[] = $herb->name;
            }

            // Mapping Logic
            $nameLower = mb_strtolower($herb->name);
            $category = mb_strtolower($herb->category ?? '');
            $usageType = mb_strtolower($herb->usage_type ?? '');

            $isExternal = false;
            if ($category === 'thuốc dùng ngoài' || $usageType === 'đắp ngoài' || $usageType === 'xoa bóp' ||
                str_contains($nameLower, 'bó thuốc') || str_contains($nameLower, 'xoa bóp') || str_contains($nameLower, 'thuốc tắm')) {
                $isExternal = true;
            }

            $itemType = $isExternal ? 'external_product' : 'herb';
            $usageRoute = $isExternal ? 'external' : 'oral';
            $warningNote = $isExternal ? "Dùng ngoài da theo chỉ định của thầy thuốc. Không được uống." : null;

            if ($isExternal) {
                $stats['external_items']++;
                $externalItemsList[] = $herb->name;
            } else {
                $stats['oral_items']++;
            }

            $expiry = $herb->expiry_date ?? null;
            $batchStatus = $expiry ? 'available' : 'unknown_expiry';
            
            if ($qty > 0 || $batchStatus === 'unknown_expiry') {
                $stats['total_batches']++;
                if ($batchStatus === 'available') $stats['available_batches']++;
                else $stats['unknown_expiry_batches']++;
            }

            $reportRows[] = [
                'Source' => 'medicinal_herbs',
                'Legacy ID' => $herb->id,
                'Name' => $herb->name,
                'Type' => $itemType,
                'Route' => $usageRoute,
                'Unit' => $unit ?? 'g',
                'Qty' => $qty,
                'Expiry' => $expiry ?? 'NULL',
                'Batch Status' => $batchStatus,
                'Warning' => $warningNote ? 'Yes' : ''
            ];

            if ($isExecute && $qty >= 0) {
                DB::beginTransaction();
                try {
                    $itemId = DB::table('inventory_items')->insertGetId([
                        'name' => $herb->name,
                        'item_type' => $itemType,
                        'usage_route' => $usageRoute,
                        'unit' => $unit ?? 'g',
                        'warning_note' => $warningNote,
                        'description' => $herb->description ?? null,
                        'is_active' => true,
                        'legacy_source_table' => 'medicinal_herbs',
                        'legacy_source_id' => $herb->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Only create batch if quantity > 0 or it's an unknown_expiry that needs manual review
                    $batchId = DB::table('inventory_batches')->insertGetId([
                        'inventory_item_id' => $itemId,
                        'batch_number' => 'LEGACY-H-' . date('Ymd') . '-' . $herb->id,
                        'expiry_date' => $expiry,
                        'quantity_remaining' => $qty,
                        'status' => $batchStatus,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    if ($qty > 0) {
                        DB::table('stock_movements')->insert([
                            'inventory_batch_id' => $batchId,
                            'movement_type' => 'opening_balance',
                            'quantity' => $qty,
                            'note' => 'Opening balance from legacy medicinal_herbs',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->error("Failed to migrate herb ID {$herb->id}: " . $e->getMessage());
                }
            }
        }

        // Process Packaged Products
        foreach ($packaged as $prod) {
            if (in_array($prod->id, $migratedPkgs)) {
                $stats['skipped_already_migrated']++;
                continue;
            }

            $stats['total_items']++;
            
            $qty = (float) ($prod->stock_quantity ?? 0);
            if ($qty > 0) $stats['qty_positive']++;
            elseif ($qty < 0) $stats['qty_negative']++;
            else $stats['qty_zero']++;
            
            $unit = $prod->unit;
            if (empty($unit)) {
                $stats['no_unit']++;
                $noUnitItemsList[] = $prod->name;
            }

            $nameLower = mb_strtolower($prod->name);
            $isExternal = false;
            if (str_contains($nameLower, 'bó') || str_contains($nameLower, 'xoa bóp') || str_contains($nameLower, 'tắm') || mb_strtolower($prod->category) === 'thuốc dùng ngoài') {
                $isExternal = true;
            }

            $itemType = $isExternal ? 'external_product' : 'prepared_product';
            $usageRoute = $isExternal ? 'external' : 'oral';
            $warningNote = $isExternal ? "Chỉ dùng ngoài da, không được uống." : null;

            if ($isExternal) {
                $stats['external_items']++;
                $externalItemsList[] = $prod->name;
            } else {
                $stats['oral_items']++;
            }

            $expiry = $prod->expiry_date ?? null;
            $batchStatus = $expiry ? 'available' : 'unknown_expiry';
            
            $stats['total_batches']++;
            if ($batchStatus === 'available') $stats['available_batches']++;
            else $stats['unknown_expiry_batches']++;

            $reportRows[] = [
                'Source' => 'packaged_products',
                'Legacy ID' => $prod->id,
                'Name' => $prod->name,
                'Type' => $itemType,
                'Route' => $usageRoute,
                'Unit' => $unit ?? 'hộp',
                'Qty' => $qty,
                'Expiry' => $expiry ?? 'NULL',
                'Batch Status' => $batchStatus,
                'Warning' => $warningNote ? 'Yes' : ''
            ];

            if ($isExecute && $qty >= 0) {
                DB::beginTransaction();
                try {
                    $itemId = DB::table('inventory_items')->insertGetId([
                        'name' => $prod->name,
                        'item_type' => $itemType,
                        'usage_route' => $usageRoute,
                        'unit' => $unit ?? 'hộp',
                        'warning_note' => $warningNote,
                        'description' => $prod->description ?? null,
                        'is_active' => true,
                        'legacy_source_table' => 'packaged_products',
                        'legacy_source_id' => $prod->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $batchId = DB::table('inventory_batches')->insertGetId([
                        'inventory_item_id' => $itemId,
                        'batch_number' => 'LEGACY-P-' . date('Ymd') . '-' . $prod->id,
                        'expiry_date' => $expiry,
                        'quantity_remaining' => $qty,
                        'status' => $batchStatus,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    if ($qty > 0) {
                        DB::table('stock_movements')->insert([
                            'inventory_batch_id' => $batchId,
                            'movement_type' => 'opening_balance',
                            'quantity' => $qty,
                            'note' => 'Opening balance from legacy packaged_products',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->error("Failed to migrate packaged product ID {$prod->id}: " . $e->getMessage());
                }
            }
        }

        // Output report
        $this->info("=== MIGRATION REPORT ===");
        $this->table(
            ['Source', 'Legacy ID', 'Name', 'Type', 'Route', 'Unit', 'Qty', 'Expiry', 'Batch Status', 'Warning'],
            $reportRows
        );

        $this->info("=== SUMMARY ===");
        $this->info("Total Items: {$stats['total_items']}");
        $this->info("  - Oral Items: {$stats['oral_items']}");
        $this->info("  - External Items: {$stats['external_items']}");
        $this->info("Total Batches: {$stats['total_batches']} (Available: {$stats['available_batches']}, Unknown Expiry: {$stats['unknown_expiry_batches']})");
        $this->info("Quantities: >0: {$stats['qty_positive']} | ==0: {$stats['qty_zero']} | <0: {$stats['qty_negative']}");
        $this->info("Skipped (already migrated): {$stats['skipped_already_migrated']}");
        
        if ($stats['qty_negative'] > 0) {
            $this->error("WARNING: Found {$stats['qty_negative']} items with negative quantity. Migration --execute will be blocked for these items.");
        }

        if (count($externalItemsList) > 0) {
            $this->info("\nExternal Items successfully mapped:");
            foreach ($externalItemsList as $name) {
                $this->line(" - $name");
            }
        }

        if (count($noUnitItemsList) > 0) {
            $this->info("\nItems missing unit:");
            foreach ($noUnitItemsList as $name) {
                $this->line(" - $name");
            }
        }

        if ($isDryRun) {
            $this->warn("\nThis was a DRY-RUN. No database changes were made.");
        } else {
            $this->info("\nMigration executed successfully.");
        }
    }
}
