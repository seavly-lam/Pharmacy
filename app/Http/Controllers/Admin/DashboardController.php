<?php

namespace App\Http\Controllers\Admin;

use App\Models\Sale;
use App\Models\Category;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $title = 'dashboard';

        // Count total purchases excluding those expiring exactly today
        $total_purchases = Purchase::where('expiry_date', '!=', Carbon::now())->count();
        $total_categories = Category::count();
        $total_suppliers = Supplier::count();
        $total_sales = Sale::count();

        // Pie chart for purchases, suppliers and sales
        $pieChart = app()->chartjs
            ->name('pieChart')
            ->type('pie')
            ->size(['width' => 400, 'height' => 200])
            ->labels(['Total Purchases', 'Total Suppliers', 'Total Sales'])
            ->datasets([
                [
                    'backgroundColor' => ['#FF6384', '#36A2EB', '#7bb13c'],
                    'hoverBackgroundColor' => ['#FF6384', '#36A2EB', '#7bb13c'],
                    'data' => [$total_purchases, $total_suppliers, $total_sales]
                ]
            ])
            ->options([]);

        // Count total expired products today
        $total_expired_products = Purchase::whereDate('expiry_date', '=', Carbon::now())->count();

        // Latest sales made today
        $latest_sales = Sale::whereDate('created_at', '=', Carbon::now())->get();

        // Sum of sales total price today
        $today_sales = Sale::whereDate('created_at', '=', Carbon::now())->sum('total_price');

        // New: Popular sales in last 7 days
        $days = 7;
        $startDate = now()->subDays($days)->startOfDay();

        $popularSales = \DB::table('sales')
            ->join('products', 'sales.product_id', '=', 'products.id')
            ->join('purchases', 'products.purchase_id', '=', 'purchases.id')
            ->select(
                'purchases.product as product_name',
                \DB::raw('SUM(sales.quantity) as total_quantity'),
                \DB::raw('SUM(sales.total_price) as total_sales')
            )
            ->where('sales.created_at', '>=', $startDate)
            ->groupBy('sales.product_id', 'purchases.product')
            ->orderByDesc('total_quantity')
            ->get();

        return view('admin.dashboard', compact(
            'title',
            'pieChart',
            'total_expired_products',
            'latest_sales',
            'today_sales',
            'total_categories',
            'popularSales'  // Pass popular sales data to dashboard view
        ));
    }

    /**
     * Return JSON data for line chart of medicine sales trend over last 7 days (default)
     */
    public function lineChartData(Request $request)
    {
        $days = $request->days ?? 7; // default 7 days
        $startDate = now()->subDays($days)->startOfDay();

        // Get total quantity sold grouped by date and product
        $salesData = Sale::selectRaw('DATE(created_at) as date, product_id, SUM(quantity) as total_quantity')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date', 'product_id')
            ->orderBy('date', 'asc')
            ->get();

        // Unique product IDs involved in sales
        $productIds = $salesData->pluck('product_id')->unique();

        // Get product info with purchase relation for product names
        $products = Product::with('purchase')->whereIn('id', $productIds)->get()->keyBy('id');

        // Create an array of dates for the chart labels
        $dates = [];
        for ($i = 0; $i <= $days; $i++) {
            $dates[] = now()->subDays($days - $i)->format('Y-m-d');
        }

        $datasets = [];

        foreach ($productIds as $productId) {
            $data = [];
            foreach ($dates as $date) {
                $record = $salesData->firstWhere(function ($item) use ($date, $productId) {
                    return $item->date == $date && $item->product_id == $productId;
                });
                $data[] = $record ? (int) $record->total_quantity : 0;
            }

            $datasets[] = [
                'label' => $products[$productId]->purchase->product ?? 'Unknown',
                'data' => $data,
                'fill' => false,
                'borderColor' => $this->randomColor(),
                'tension' => 0.1,
            ];
        }

        return response()->json([
            'labels' => $dates,
            'datasets' => $datasets,
        ]);
    }

    /**
     * Generate random hex color for datasets line colors
     */
    private function randomColor()
    {
        return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    }
}
