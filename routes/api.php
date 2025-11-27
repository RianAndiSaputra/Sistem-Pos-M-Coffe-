<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LabaRugiController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\RawMaterialController;
use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\PrintTemplateController;
use App\Http\Controllers\ProductRecipeController;
use App\Http\Controllers\SalesForecastController;
use App\Http\Controllers\InventoryHistoryController;
use App\Http\Controllers\RawMaterialStockController;
use App\Http\Controllers\RawMaterialPurchaseController;
use App\Http\Controllers\CashRegisterTransactionController;

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    
    Route::controller(AuthController::class)->group(function () {
        Route::get('/me', 'me');
        Route::post('/logout', 'logout');
        Route::get('/validate-token', 'validateToken');
    });

    Route::controller(OutletController::class)->group(function(){
        Route::get('/outlets/{outlet}', 'show');
    });

    Route::controller(ShiftController::class)->group(function(){
        Route::get('/shifts/{shift}', 'show');
    });

    Route::get('/products/barcode-image/{code}', [ProductController::class, 'generateBarcodeImage']);
    
    Route::middleware('role:admin,supervisor')->group(function () {
        
        Route::controller(AuthController::class)->prefix('user')->group(function () {
            Route::post('/register', 'register')->middleware('role:admin');
            Route::get('/all/{outletId}', 'getAllUsers');
            Route::put('/update/{user}', 'update')->middleware('role:admin');
            Route::delete('/delete/{user}', 'destroy')->middleware('role:admin');
        });

        Route::controller(OutletController::class)->group(function () {
            Route::get('/outlets', 'index');
            Route::post('/outlets', 'store')->middleware('role:admin');
            Route::post('/outlets/{outlet}', 'update')->middleware('role:admin'); 
            Route::delete('/outlets/{outlet}', 'destroy')->middleware('role:admin');
        });

        Route::controller(CategoryController::class)->group(function () {
            Route::get('/categories', 'index');
            Route::post('/categories', 'store')->middleware('role:admin');
            Route::get('/categories/{category}', 'show');
            Route::put('/categories/{category}', 'update')->middleware('role:admin');
            Route::delete('/categories/{category}', 'destroy')->middleware('role:admin');
        });

        Route::controller(MemberController::class)->prefix('members')->group(function () {
            // Route::get('/', 'index');
            Route::post('/', 'store')->middleware('role:admin');
            Route::put('/{member}', 'update')->middleware('role:admin');
            Route::delete('/{member}', 'destroy')->middleware('role:admin');
        });

        Route::controller(ProductController::class)->group(function () {
            Route::get('/products', 'index');
            Route::post('/products', 'store')->middleware('role:admin');
            Route::get('/products/{product}', 'show');
            Route::post('/products/{product}', 'update')->middleware('role:admin');
            Route::delete('/products/{product}', 'destroy')->middleware('role:admin');
            Route::get('/products/outlet/{outletId}', 'getOutletProducts');
            Route::get('/products/barcode/{barcode}', 'findByBarcode')->middleware('role:kasir');
            Route::get('/products/generate-barcode', 'generateBarcode')->middleware('role:admin');
            Route::get('/outlets/{outletId}/products/barcode/{barcode}', 'posFindByBarcode')->middleware('role:kasir');
            Route::get('products/{product}/detail', [ProductController::class, 'getProductDetail']);
        });

        Route::controller(InventoryController::class)->group(function () {
            Route::get('/inventories', 'index');
            Route::post('/inventories/transfer', 'transferStock')->middleware('role:admin');
            Route::get('/inventories/listAll', 'listAllInventories');
            Route::post('/inventories', 'store')->middleware('role:admin');
            Route::get('/inventories/{inventory}', 'show');
            Route::put('/inventories/{inventory}', 'update')->middleware('role:admin');
            Route::delete('/inventories/{inventory}', 'destroy')->middleware('role:admin');
        });

        Route::controller(InventoryHistoryController::class)->group(function () {
            Route::get('/inventory-histories', 'index');
            Route::post('/inventory-histories', 'store')->middleware('role:admin');
            Route::post('/inventory-histories/approval', 'adminApprovStock')->middleware('role:admin');
            Route::post('/inventory-histories/reject', 'adminRejectStock')->middleware('role:admin');
            Route::get('/inventory-histories/{inventoryHistory}', 'show');
            Route::put('/inventory-histories/{inventoryHistory}', 'update')->middleware('role:admin');
            Route::delete('/inventory-histories/{inventoryHistory}', 'destroy')->middleware('role:admin');
            Route::get('/inventory-histories/stock/{outletId}', 'getStock');
            Route::get('/inventory-histories/outlet/{outletId}', 'getHistoryByOutlet');
            Route::get('/inventory-histories/type/{outletId}', 'getInventoryHistoryByType');
            Route::get('/notifications/stock-adjustments/{outletId}', 'getPendingStockAdjustments');
        });

        Route::controller(ShiftController::class)->group(function () {
            Route::get('/shifts', 'index');
            Route::post('/shifts', 'store')->middleware('role:admin');
            Route::put('/shifts/{shift}', 'update')->middleware('role:admin');
            Route::delete('/shifts/{shift}', 'destroy')->middleware('role:admin');
        });

        Route::controller(ReportController::class)->prefix('reports')->group(function () {
            Route::get('/daily-sales/{outlet}', 'dailySales');
            Route::get('/monthly-sales/{outlet}', 'listProductsByDateRange');
            Route::get('/monthly-inventory/{outlet}', 'inventoryReport');
            Route::get('/inventory-by-date/{outlet}', 'inventoryByDate');
            Route::get('/shift-report/{outlet}', 'shiftReport');
            Route::get('/dashboard-summary/{outlet}', 'dashboardSummary');

            Route::get('/sales-by-category/{outlet}', 'salesByCategory');
            Route::get('/inventory-approvals/{outlet}',  'inventoryApprovals');
            Route::get('/sales-by-member/{outlet}', 'listProductByMember');
        });

        Route::controller(RawMaterialController::class)->group(function(){
            Route::get('/raw-material', 'index');
            Route::post('/raw-material', 'store');
            Route::delete('/material-delete/{id}', 'destroy');
        });

        Route::controller(ProductRecipeController::class)->group(function(){
            Route::get('/product-recipe', 'index');
            Route::post('/product-recipe', 'store');
        });

        Route::controller(RawMaterialPurchaseController::class)->group(function(){
            Route::get('/material-purchase', 'index');
            Route::post('/material-purchase', 'store');
        });

        Route::controller(RawMaterialStockController::class)->group(function(){
            Route::get('/raw-stock', 'index');
            Route::get('/get-low-stock', 'getLowStock');
            Route::get('/get-stock-history', 'getStockHistory');
        });

        Route::controller(LabaRugiController::class)->group(function(){
            Route::get('/laba-rugi', 'index');
        });

        Route::post('/print-template', [PrintTemplateController::class, 'store']);

        Route::get('/admin', function () {
            return response()->json([
                'message' => 'Ini untuk admin'
            ]);
        });

        Route::prefix('forecasts')->group(function () {
            // Get forecasts
            Route::get('/', [SalesForecastController::class, 'index']);
            Route::get('/daily', [SalesForecastController::class, 'daily']);
            Route::get('/weekly', [SalesForecastController::class, 'weekly']);
            Route::get('/monthly', [SalesForecastController::class, 'monthly']);
            
            // Dashboard data
            Route::get('/dashboard', [SalesForecastController::class, 'dashboard']);
            
            // Generate new forecasts
            Route::post('/generate', [SalesForecastController::class, 'generate']);
            
            // Model accuracy
            Route::get('/accuracy', [SalesForecastController::class, 'accuracy']);
        });
    }); 
    
    Route::middleware('role:kasir,admin,supervisor')->group(function () {
        
        Route::get('/products/barcode/{barcode}', [ProductController::class, 'findByBarcode'])->middleware('role:kasir');

        Route::get('/print-template/{outlet_id}', [PrintTemplateController::class, 'show']);
        Route::post('/update-profile', [AuthController::class, 'updateProfile']);
        Route::get('/members', [MemberController::class, 'index']);
        Route::post('/print-receipt', [PrintController::class, 'printReceipt']);
        Route::post('/test-printer', [PrintController::class, 'testPrinter']);

        Route::get('/kasir-admin', function () {
            return response()->json([
                'message' => 'Ini untuk kasir dan admin'
            ]);
        });

        Route::controller(ProductController::class)->group(function () {
            Route::get('/products/outlet/pos/{outletId}', 'getOutletProductsPos');
            Route::get('/products/outlet/{outletId}', 'getOutletProducts');
            Route::get('/outlets/{outletId}/products/barcode/{barcode}', 'posFindByBarcode');
        });

        Route::get('/categories', [CategoryController::class, 'index']);

        Route::controller(OrderController::class)->group(function () {
            Route::post('/orders', 'store');
            Route::get('/orders/revenue/{outletId}', 'oneMonthRevenue');
            Route::post('/orders/cancel/{orderId}', 'cancelOrder');
            Route::get('/orders/history', 'orderHistory');
            Route::get('/orders/history/admin', 'orderAdmin');
        });

        Route::controller(CashRegisterController::class)->group(function () {
            Route::get('/cash-registers', 'index');
            Route::get('/cash-registers/{outlet_id}', 'show');
        });

        Route::controller(InventoryHistoryController::class)->group(function() {
            Route::post('/adjust-inventory', 'cashierAdjustStock');
            Route::get('/adjust-inventory/{outlet_id}', 'showCashierInventoryHistories');
        });

        Route::controller(CashRegisterTransactionController::class)->prefix('cash-register-transactions')->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::get('/{id}', 'show');
            Route::get('/cash-register/{id}', 'getByCashRegister');
            Route::get('/shift/{shiftId}', 'getByShift');
            Route::get('/type/{type}', 'getType');
            Route::get('/balance/{id}', 'getBalance');
            Route::post('/add-cash', 'addCash');
            Route::post('/subtract-cash', 'subtractCash');
        });
    });

    Route::get('/print-template', function () {
        $outlet = auth()->user()->outlet; // Asumsi user terkait dengan outlet
        $template = [
            'company_name' => config('app.name'),
            'company_slogan' => 'Pelayanan Terbaik untuk Anda',
            'logo_url' => $outlet->logo_url ?? asset('images/logo.png'),
            'footer_message' => 'Barang yang sudah dibeli tidak dapat ditukar atau dikembalikan',
        ];
        
        return response()->json([
            'success' => true,
            'data' => $template
        ]);
    });
});
