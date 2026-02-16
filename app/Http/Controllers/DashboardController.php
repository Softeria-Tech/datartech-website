<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\UserDownload;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $totalOrders = Order::where('user_id', $user->id)->count();
        $activeSubscriptions = Subscription::where('user_id', $user->id)
            ->active()
            ->count();
        $totalDownloads = UserDownload::where('user_id', $user->id)->count();
        $pendingOrders = Order::where('user_id', $user->id)
            ->where('payment_status', 'pending')
            ->count();
        $recentOrders = Order::where('user_id', $user->id)
            ->with('resource')
            ->latest()
            ->limit(5)
            ->get();
        $subscriptions = Subscription::where('user_id', $user->id)
            ->with('membershipPackage')
            ->latest()
            ->get();
        $recentDownloads = UserDownload::where('user_id', $user->id)
            ->with(['resource', 'order'])
            ->latest('downloaded_at')
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'totalOrders',
            'activeSubscriptions',
            'totalDownloads',
            'pendingOrders',
            'recentOrders',
            'subscriptions',
            'recentDownloads'
        ));
    }

    function logout(Request $request){
        Auth::logout();
        return redirect('');
    }
}
