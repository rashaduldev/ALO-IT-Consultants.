<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::query()
            ->with('customer')
            ->orderByDesc('order_date')
            ->orderByDesc('id')
            ->paginate(15);

        return view('orders.index', ['orders' => $orders]);
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
