    <div class="wsus__track_order">
        @php
            $statuses = [
                0 => 'order pending',
                1 => 'order accept',
                2 => 'order process',
                3 => 'on the way',
                4 => 'Completed',
            ];
        @endphp
        <ul>
            @foreach ($statuses as $key => $label)
                <li class="{{ $order->order_status == $key ? 'active' : ($order->order_status > $key ? 'complete' : '') }}">
                    {{ $label }}
                </li>
            @endforeach
        </ul>
    </div>


    <div class="wsus__invoice_header">
        <div class="header_address">
            <h4>invoice to</h4>

            <p> {{ $order->shippingAddress->address }} </p>
            <p> {{ $order->shippingAddress->user->name }}
                , {{ $order->shippingAddress->user->phone }}
            </p>
            <p>{{ $order->shippingAddress->user->email }}</p>

        </div>
        <div class="header_address">
            <p><b>Order ID:</b> <span> {{ $order->order_number }}</span></p>
            <p><b>date:</b> <span>{{ $order->created_at->format('d-m-Y') }}</span></p>
            <p>
                <b>Payment:</b> 
                @if ($order->payment_status == 1)
                    <span>Success</span>
                @else
                    <span>Pending</span>
                @endif
            </p>
        </div>
    </div>
    <div class="wsus__invoice_body">
        <div class="table-responsive">
            <table class="table table-striped">
                <tbody>
                    <tr class="border_none">
                        <th class="sl_no">SL</th>
                        <th class="package">item description</th>
                        <th class="price">Unit Price</th>
                        <th class="qnty">Quantity</th>
                        <th class="total">Total</th>
                    </tr>
                    <tr>
                        <td class="sl_no">1</td>
                        <td class="package">
                            <p>{{ $order->product->title }}</p>
                        </td>
                        <td class="price">
                            <b>£{{ $order->product->price }}</b>
                        </td>
                        <td class="qnty">
                            <b>{{ $order->quantity }}</b>
                        </td>
                        <td class="total">
                            <b>£{{ $order->price * $order->quantity }}</b>
                        </td>
                    </tr>

                </tbody>
                <tfoot>
                    <tr>
                        <td class="package" colspan="3">
                            <b>sub total</b>
                        </td>
                        <td class="qnty">
                            <b></b>
                        </td>
                        <td class="total">
                            <b>£{{ $order->price * $order->quantity }}</b>
                        </td>
                    </tr>
                    <tr>
                        <td class="package coupon" colspan="3">
                            <b>(-) Discount coupon</b>
                        </td>
                        <td class="qnty">
                            <b></b>
                        </td>
                        <td class="total coupon">
                            <b>£0</b>
                        </td>
                    </tr>
                    <tr>
                        <td class="package coast" colspan="3">
                            <b>(+) Delivery Charge</b>
                        </td>
                        <td class="qnty">
                            <b></b>
                        </td>
                    </tr>
                    <tr>
                        <td class="package" colspan="3">
                            <b>Grand Total</b>
                        </td>
                        <td class="qnty">
                            <b></b>
                        </td>
                        <td class="total">
                            <b>£{{($order->price * $order->quantity ?? 0) }}</b>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>