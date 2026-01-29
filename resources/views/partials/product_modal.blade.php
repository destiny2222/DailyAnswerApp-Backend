<div class="wsus__cart_popup_modal ">
    <!-- Image Section -->
    <div class="wsus__cart_popup_modal_image_section">
        <img src="{{ $product->images }}" alt="Product Image">
    </div>
    <!-- Details Section -->
    <div class="wsus__cart_popup_modal_details_section">
        <div class="reviews">
            <span class="in-stock">
                @if ($product->availability > 0)
                    In stock
                @else
                    Out of stock
                @endif
            </span>
        </div>
        <div class="product-title">{{ $product->title }}</div>
        @if ($product->has_variants == 1)
            <div class="variant-price price">£{{ $product->variants->first()->price }}</div>
        @else
            <div class="variant-price price">£{{ $product->price }} / {{ $product->unit }}</div>
        @endif
        <div class="description">
            {!! $product->description !!}
        </div>

        @if ($product->has_variants == 1)
           <div class="label">Size:</div>
            <div class="sizes">
                @foreach ($product->variants as $variant)
                    <button type="button" class="size-option" data-price="{{ $variant->price }}" data-size="{{ $variant->size }}">
                        {{ $variant->size }}
                    </button>
                @endforeach
            </div>
        @else
           <div class="mt-3 mb-4">
                <p>Weight:  <span>{{ $product->weight }}</span></p>
           </div>
        @endif

       <form class="add-to-cart-form" data-has-variants="{{ $product->has_variants }}" data-default-price="{{ $product->price }}">
            <input type="hidden" name="size_variant" id="selected_size" value="">
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="quantity" id="selected_quantity" value="1">
            <input type="hidden" id="selected_price" name="price" value="">
        </form>

        <div class="quantity-wrapper">
            <button type="button" class="qty-btn minus">−</button>
            <div class="qty-display">1</div>
            <button type="button" class="qty-btn plus">+</button>
        </div>

        <div class="buttons">
            <button class="add-cart">Add To Cart</button>
        </div>
    </div>
</div>


<script>
(function($) {
    "use strict";
    $(document).ready(function() {
        const form = $('.add-to-cart-form');
        const hasVariants = parseInt(form.data('has-variants')) == 1;
        const defaultPrice = form.data('default-price'); // Get the default price from data attribute

        console.log("Has Variants?", hasVariants);
        console.log("Default Price:", defaultPrice);

        if (hasVariants) {
            // Handle variant selection
            $(document).on('click', '.size-option', function() {
                
                // Remove active class from all options
                $('.size-option').removeClass('active');
                // Add active class to clicked option
                $(this).addClass('active');

                const size = $(this).data('size');
                const price = $(this).data('price');

                // Update hidden form inputs
                const container = $(this).closest('.wsus__cart_popup_modal');
                container.find('#selected_size').val(size);
                container.find('#selected_price').val(price);

                // Update displayed price
                $('.variant-price').text('£' + price);

            });
            
            // Set default values if no variant is selected
            $('#selected_price').val('');
        } else {
            // For products without variants, set the base price immediately
            const container = $('.wsus__cart_popup_modal');
            container.find('#selected_price').val(defaultPrice);
            console.log("Set default price:", defaultPrice);
        }

        // Quantity buttons
        let quantity = 1;
        $('.plus').click(function() {
            const container = $(this).closest('.wsus__cart_popup_modal');
            quantity++;
            container.find('.qty-display').text(quantity);
            container.find('#selected_quantity').val(quantity);
        });
        
        $('.minus').click(function() {
            if (quantity > 1) {
                const container = $(this).closest('.wsus__cart_popup_modal');
                quantity--;
                container.find('.qty-display').text(quantity);
                container.find('#selected_quantity').val(quantity);
            }
        });

        // Add to Cart button
        $('.add-cart').click(function(e) {
            e.preventDefault();
            
            const container = $(this).closest('.wsus__cart_popup_modal'); 
            const form = container.find('.add-to-cart-form');

            const size = container.find('#selected_size').val();
            const price = container.find('#selected_price').val();
            const quantity = container.find('#selected_quantity').val();
            const productId = container.find('input[name="product_id"]').val();
            const hasVariants = parseInt(form.data('has-variants')) === 1;

            console.log("Form data:", {size, price, quantity, productId, hasVariants});

            // Validation for products with variants
            if (hasVariants && (!size || size === '')) {
                toastr.error("Please select a size.");
                return;
            }

            // Validation for price
            if (!price || price === '' || price === 'undefined') {
                toastr.error("Price not selected. Please try again.");
                return;
            }

            const button = $(this);
            button.prop('disabled', true).text('Adding...');

            // Create form data manually as backup
            const formData = {
                size_variant: size,
                product_id: productId,
                quantity: quantity,
                price: price
            };
            
            console.log("Sending form data:", formData);

            $.ajax({
                type: 'POST',
                url: "{{ route('cart.add') }}",
                data: formData, // Using manual form data instead of serialize()
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        Livewire.dispatch('cartUpdated');
                        if (response.cart_count !== undefined) {
                            $('#cart-count').text(response.cart_count).addClass('animate__animated animate__pulse');
                            $('#cart-counts').text(response.cart_count);
                            setTimeout(() => {
                                $('#cart-count').removeClass('animate__animated animate__pulse');
                            }, 1000);
                        }
                        updateMiniCart();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    toastr.error("Something went wrong. Try again.");
                    console.log("AJAX Error:", xhr.responseText);
                },
                complete: function() {
                    button.prop('disabled', false).text('Add To Cart');
                }
            });
        });
    });
})(jQuery);
</script>
<script>
function updateMiniCart() {
    $.ajax({
        url: '/cart/mini',
        type: 'GET',
        success: function (response) {
            $('.wsus__menu_cart_area').html(response.html);
        }
    });
}
</script>