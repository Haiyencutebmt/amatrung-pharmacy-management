<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MedicinalHerb;
use App\Models\PackagedProduct;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    /**
     * Trang tổng hợp Quản lý kho (Dược liệu + Thuốc dùng ngoài/Trà thảo mộc)
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'herbs'); // 'herbs' hoặc 'products'

        // ── Dữ liệu Kho dược liệu ─────────────────────────────
        $herbQuery = MedicinalHerb::query();

        if ($request->filled('search') && $tab === 'herbs') {
            $search = $request->search;
            $herbQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('usage_type')) {
            $herbQuery->where('usage_type', $request->usage_type);
        }

        if ($request->filled('filter')) {
            $filter = $request->filter;
            if ($filter === 'warning') {
                $herbQuery->where(function ($q) {
                    $q->where('stock_quantity', '<=', 0)
                      ->orWhere(function ($sub) {
                          $sub->where('stock_quantity', '>', 0)
                              ->where(function ($inner) {
                                  $inner->where(function ($bocThuoc) {
                                      $bocThuoc->where('category', 'Dược liệu bốc thuốc')
                                               ->where('stock_quantity', '<', 500);
                                  })->orWhere(function ($others) {
                                      $others->where(function ($notBocThuoc) {
                                          $notBocThuoc->where('category', '!=', 'Dược liệu bốc thuốc')
                                                      ->orWhereNull('category');
                                      })->where('stock_quantity', '<=', 10);
                                  });
                              });
                      });
                });
            } elseif ($filter === 'expired') {
                $herbQuery->whereNotNull('expiry_date')
                          ->where('expiry_date', '<', now());
            }
        }

        $herbs = $herbQuery->orderBy('name')->paginate(20, ['*'], 'herb_page');

        // Stats dược liệu
        $totalHerbs        = MedicinalHerb::count();
        $outOfStockCount   = MedicinalHerb::where('stock_quantity', '<=', 0)->count();
        $warningStockCount = MedicinalHerb::where('stock_quantity', '>', 0)
            ->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->where('category', 'Dược liệu bốc thuốc')
                        ->where('stock_quantity', '<', 500);
                })->orWhere(function ($sub) {
                    $sub->where(function ($notBocThuoc) {
                        $notBocThuoc->where('category', '!=', 'Dược liệu bốc thuốc')
                                    ->orWhereNull('category');
                    })->where('stock_quantity', '<=', 10);
                });
            })->count();
        $expiredCount = MedicinalHerb::whereNotNull('expiry_date')->where('expiry_date', '<', now())->count();

        // ── Dữ liệu Thuốc dùng ngoài/Trà thảo mộc ─────────────────────────
        $productQuery = PackagedProduct::query();

        if ($request->filled('search') && $tab === 'products') {
            $search = $request->search;
            $productQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $productQuery->where('status', $request->status);
        }

        $products       = $productQuery->orderByDesc('id')->paginate(20, ['*'], 'product_page');
        $totalProducts  = PackagedProduct::count();
        $activeProducts = PackagedProduct::where('status', 'active')->where('stock_quantity', '>', 0)->count();
        $lowStock       = PackagedProduct::where('status', 'active')
            ->where('stock_quantity', '>', 0)
            ->where('stock_quantity', '<=', 10)
            ->count();

        return view('admin.warehouse.index', compact(
            'tab',
            'herbs', 'totalHerbs', 'outOfStockCount', 'warningStockCount', 'expiredCount',
            'products', 'totalProducts', 'activeProducts', 'lowStock'
        ));
    }
}
