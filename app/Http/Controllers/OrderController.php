<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Exceptions\OrderCompletionException;
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
            ->when($selectedStatus, fn ($query) => $query->where('status', $selectedStatus->value))
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

    public function show(Order $order): View
    {
        $order->load(['customer', 'items.product']);

        return view('orders.show', ['order' => $order]);
    }

    public function store(StoreOrderRequest $request, OrderService $orderService): RedirectResponse
    {
        $order = $orderService->createOrder($request->validated());

        return redirect()
            ->route('orders.index')
            ->with('success', "Order #{$order->id} was created as pending.");
    }

    public function complete(Order $order, OrderService $orderService): RedirectResponse
    {
        try {
            $completedOrder = $orderService->completeOrder($order);
        } catch (OrderCompletionException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('orders.show', $completedOrder)
            ->with('success', "Order #{$completedOrder->id} was completed and stock was deducted.");
    }
}
