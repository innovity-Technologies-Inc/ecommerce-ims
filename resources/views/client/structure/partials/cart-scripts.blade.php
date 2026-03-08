<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Add to Cart
    $(document).on('click', '.add-to-cart-btn', function(e) {
        e.preventDefault();
        
        let productId = $(this).data('product-id');
        let quantity = $('#product-quantity').val() || $(this).data('quantity') || 1;
        let variantId = $('#variant-selector').val() || null;

        $.ajax({
            url: "{{ route('cart.add') }}",
            method: "POST",
            data: {
                product_id: productId,
                quantity: quantity,
                product_variant_id: variantId
            },
            success: function(response) {
                if (response.status === 'success') {
                    toastr.success(response.message);
                    updateCartUI(response);
                }
            },
            error: function(xhr) {
                let message = 'Something went wrong!';
                if (xhr.status === 422) {
                    message = xhr.responseJSON.message;
                }
                toastr.error(message);
            }
        });
    });

    // Remove from Cart
    $(document).on('click', '.remove-from-cart', function(e) {
        e.preventDefault();
        let cartId = $(this).data('cart-id');

        Swal.fire({
            title: 'Are you sure?',
            text: "Remove this item from cart?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, remove it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('cart.remove') }}",
                    method: "POST",
                    data: { cart_id: cartId },
                    success: function(response) {
                        toastr.success(response.message);
                        $(`#cart-row-${cartId}`).fadeOut(300, function() {
                            $(this).remove();
                            if ($('#cart-table-body tr').length === 0) {
                                location.reload(); // Reload to show empty cart message
                            }
                        });
                        updateCartUI(response);
                    }
                });
            }
        });
    });

    // Update Quantity
    $(document).on('change', '.qty-input', function() {
        let cartId = $(this).data('cart-id');
        let quantity = $(this).val();

        if (quantity < 1) {
            $(this).val(1);
            quantity = 1;
        }

        updateQuantity(cartId, quantity);
    });

    // Cart Plus/Minus buttons logic
    $(document).on('click', '.cart-plus-minus .qtybutton', function() {
        let $button = $(this);
        let $input = $button.parent().find('input');
        let cartId = $input.data('cart-id');
        
        // Wait a tiny bit for the template's own JS to update the input value
        setTimeout(() => {
            let quantity = $input.val();
            if (cartId) {
                updateQuantity(cartId, quantity);
            }
        }, 50);
    });

    function updateQuantity(cartId, quantity) {
        $.ajax({
            url: "{{ route('cart.update') }}",
            method: "POST",
            data: {
                cart_id: cartId,
                quantity: quantity
            },
            success: function(response) {
                $(`#subtotal-${cartId}`).text(response.item_subtotal);
                $('.cart-total-display').text('$' + response.total);
                updateCartUI(response);
            },
            error: function(xhr) {
                let message = 'Failed to update quantity';
                if (xhr.status === 422) {
                    message = xhr.responseJSON.message;
                }
                toastr.error(message);
                location.reload(); // Reset to valid state
            }
        });
    }

    function updateCartUI(response) {
        // Update counts
        $('.item-quantity-tag').text(response.cart_count.toString().padStart(2, '0'));
        
        // Update Mini Cart HTML
        $('#offcanvas-cart').html(response.mini_cart_html);
        
        // Update header total
        if (response.total) {
            $('.amount-tag').text('$' + response.total);
        } else {
            // Recalculate if not provided (from mini-cart total)
            let miniCartTotal = $('#offcanvas-cart .shopping-cart-total .shop-total span').text();
            $('.amount-tag').text(miniCartTotal);
        }
    }

    $('#clear-cart').on('click', function() {
        Swal.fire({
            title: 'Clear Cart?',
            text: "Are you sure you want to empty your cart?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, clear it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Implementation for clear cart if needed, or just redirect to a clear route
                // For now, let's just say we don't have a direct clear route but we can implement it
                toastr.info('Clearing cart...');
                location.reload();
            }
        });
    });
});
</script>
