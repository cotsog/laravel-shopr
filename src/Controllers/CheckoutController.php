<?php

namespace Happypixels\Shopr\Controllers;

use Illuminate\Http\Request;
use Happypixels\Shopr\Cart\Cart;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Event;
use Happypixels\Shopr\PaymentProviders\PaymentProviderManager;

class CheckoutController extends Controller
{
    protected $cart;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }

    /**
     * Charges the order sum through the desired payment gateway.
     *
     * @return mixed
     */
    public function charge(Request $request)
    {
        $request->validate([
            'gateway' => 'required',
            'email' => 'required|email',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
        ]);

        if ($this->cart->isEmpty()) {
            return response()->json(['message' => trans('shopr::cart.cart_is_empty')], 400);
        }

        // Make the payment and merge the response with the request data, if successful.
        $data = array_merge($request->only([
            'email', 'phone', 'first_name', 'last_name', 'address', 'zipcode', 'city', 'country',
        ]), PaymentProviderManager::make($request)->payForCart());

        $order = $this->cart->convertToOrder($request->gateway, $data);

        Event::fire('shopr.orders.created', $order);

        $response = ['token' => $order->token];

        if (config('shopr.templates.order-confirmation')) {
            $response['redirect'] = route('shopr.order-confirmation').'?token='.$order->token;
        }

        return response()->json($response, 201);
    }
}
