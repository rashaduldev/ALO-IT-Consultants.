<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $selectedStatus = OrderStatus::tryFrom($request->string('status')->toString());

        $orders = Order::query()
            ->with('customer')
            ->when($selectedStatus, fn ($query) => $query->where('status', $selectedStatus))
            ->orderByDesc('order_date')
            ->orderByDesc('id')
            ->paginate(15);

        return view('orders.index', [
            'orders' => $orders,
            'selectedStatus' => $selectedStatus,
        ]);
    }

    public function create(): View
    {
        $customers = Customer::query()->orderBy('name')->get(['id', 'name']);
        $products = Product::query()->orderBy('name')->get(['id', 'sku', 'name', 'price', 'stock_quantity']);

        return view('orders.create', [
            'customers' => $customers,
            'products' => $products,
        ]);
    }

    public function store(StoreOrderRequest $request, OrderService $orderService): RedirectResponse
    {
        $order = $orderService->createOrder($request->validated());

        return redirect()
            ->route('orders.index')
            ->with('success', "Order #{$order->id} was created as pending.");
    }
}
