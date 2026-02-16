<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the user's orders.
     */
    public function index(Request $request)
    {
        $query = Order::where('user_id', Auth::id())
            ->with('resource')
            ->latest();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%");
            });
        }

        // Payment status filter
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Order status filter
        if ($request->filled('order_status')) {
            $query->where('order_status', $request->order_status);
        }

        // Date range filter
        if ($request->filled('date_range')) {
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
                case 'year':
                    $query->whereYear('created_at', now()->year);
                    break;
            }
        }

        $orders = $query->paginate(15);

        return view('orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     */
    public function show($order)
    {
        if($order instanceof Order){
            $order = $order->load(['resource', 'user']);
        }else{
            $order = Order::with(['resource', 'user'])
                ->where('order_number', $order)
                ->where('user_id', Auth::id())
                ->firstOrFail();
        }
        // Ensure user can only view their own orders
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Load relationships
        $order->load(['resource', 'user']);

        return view('orders.show', compact('order'));
    }

    /**
     * Get order statistics for the dashboard
     */
    public static function getStats()
    {
        $userId = Auth::id();

        return [
            'total_orders' => Order::where('user_id', $userId)->count(),
            'total_spent' => Order::where('user_id', $userId)
                ->where('payment_status', 'paid')
                ->sum('total'),
            'pending_orders' => Order::where('user_id', $userId)
                ->where('payment_status', 'pending')
                ->count(),
            'recent_orders' => Order::where('user_id', $userId)
                ->with('resource')
                ->latest()
                ->limit(5)
                ->get(),
        ];
    }
}