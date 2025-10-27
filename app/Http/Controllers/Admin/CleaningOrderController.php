<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CleaningOrder;
use App\Services\CleaningOrderService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CleaningOrderController extends Controller
{
    protected $orderService;

    public function __construct(CleaningOrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $orders = CleaningOrder::with(['district', 'coupon', 'transaction'])
                ->select('cleaning_orders.*');

            return DataTables::of($orders)
                ->addColumn('customer', function ($order) {
                    return view('admin.cleaning-orders.partials.customer-cell', compact('order'))->render();
                })
                ->addColumn('service', function ($order) {
                    return view('admin.cleaning-orders.partials.service-cell', compact('order'))->render();
                })
                ->addColumn('date', function ($order) {
                    return $order->preferred_date->format('M d, Y') . '<br><small class="text-muted">' . $order->preferred_time . '</small>';
                })
                ->addColumn('amount', function ($order) {
                    return '<strong>$' . number_format($order->total, 2) . '</strong>';
                })
                ->addColumn('status', function ($order) {
                    return view('admin.cleaning-orders.partials.status-badge', compact('order'))->render();
                })
                ->addColumn('payment', function ($order) {
                    $transaction = $order->transaction;
                    if ($transaction && $transaction->payment_method_brand) {
                        return ucfirst($transaction->payment_method_brand) . ' •••• ' . $transaction->payment_method_last4;
                    }
                    return '<span class="text-muted">N/A</span>';
                })
                ->addColumn('actions', function ($order) {
                    return view('admin.cleaning-orders.partials.actions', compact('order'))->render();
                })
                ->rawColumns(['customer', 'service', 'date', 'amount', 'status', 'payment', 'actions'])
                ->make(true);
        }

        // Calculate statistics
        $stats = [
            'total_orders' => CleaningOrder::count(),
            'paid_orders' => CleaningOrder::whereIn('status', ['paid', 'confirmed', 'scheduled', 'in_progress', 'completed'])->count(),
            'pending_orders' => CleaningOrder::where('status', 'pending')->count(),
            'total_revenue' => CleaningOrder::whereIn('status', ['paid', 'confirmed', 'scheduled', 'in_progress', 'completed'])->sum('total'),
        ];

        return view('admin.cleaning-orders.index', compact('stats'));
    }

    /**
     * Display the specified resource.
     *
     * @param  CleaningOrder  $cleaningOrder
     * @return \Illuminate\Http\Response
     */
    public function show(CleaningOrder $cleaningOrder)
    {
        $cleaningOrder->load(['district', 'coupon', 'transaction']);
        return view('admin.cleaning-orders.show', compact('cleaningOrder'));
    }

    /**
     * Update order status
     *
     * @param  Request  $request
     * @param  CleaningOrder  $cleaningOrder
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, CleaningOrder $cleaningOrder)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,paid,confirmed,scheduled,in_progress,completed,cancelled,refunded',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $success = $this->orderService->updateOrderStatus(
            $cleaningOrder,
            $request->status,
            $request->admin_notes
        );

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to update order status',
        ], 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  CleaningOrder  $cleaningOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy(CleaningOrder $cleaningOrder)
    {
        $cleaningOrder->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully',
        ]);
    }
}
