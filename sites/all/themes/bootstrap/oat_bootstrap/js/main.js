jQuery(function($) {
    // Add to cart
    $('.add-to-cart a').click(function(e) {
        e.preventDefault();
        var nid = $(this).data('nid');
        var quantity = $(this).parents('.add-to-cart:first').find('input').val();
        if($.isNumeric(quantity) && quantity > 0) {
            $.ajax({
                data: {
                    nid: nid,
                    quantity: quantity
                },
                url: '/cart/add',
                type: 'post',
                dataType: 'json',
                success: function(data) {
                    var status = parseInt(data.result);
                    switch(status) {
                        case -1:
                            // TODO: show popup "Товар уже в корзине"
                            alert('Товар уже в корзине.');
                            break;
                        case 0:
                            // TODO: show popup "Невозможно добавить товар."
                            alert('Невозможно добавить товар.');
                            break;
                        default:
                            // TODO: show popup "Товар добавлен в корзину."
                            alert('Товар добавлен в корзину.');
                            break;
                    }
                },
                error: function() {
                    // TODO: show popup "Невозможно добавить товар."
                    alert('Невозможно добавить товар.');
                }
            });
        } else {
            // TODO: show popup "Невозможно добавить товар."
            alert('Неверно указано количество товара.');
        }
    });

    // Remove item from cart
    $('.product-remove').click(function(e) {
        var $parent_tr = $(this).parents('tr:first');
        e.preventDefault();
        $.ajax({
            data: {nid: $parent_tr.data('nid')},
            url: '/cart/remove',
            type: 'post',
            dataType: 'json',
            success: function(data) {
                var status = parseInt(data.result);
                switch(status) {
                    case 1:
                        // TODO: show popup "Невозможно добавить товар."
                        $parent_tr.hide('slow', function(){ $parent_tr.remove(); });
                        recalculate();
                        alert('Товар удален из корзины.');
                        break;
                    case 0:
                    default:
                        // TODO: show popup "Товар добавлен в корзину."
                        alert('Невозможно удалить товар.');
                        break;
                }
            },
            error: function() {
                // TODO: show popup "Невозможно добавить товар."
                alert('Невозможно удалить товар.');
            }
        });
    });

    // Check product quantity value
    $('.product-quantity').keydown(function(event) {
        var numbersKeys = [48, 49, 50, 51, 52, 53, 54 , 55, 56, 57,
            96, 97, 98, 99, 100, 101, 102, 103, 104, 105];
        if ($.inArray(event.which, $.merge([8, 9, 46], numbersKeys)) == -1) {
            event.stopPropagation();
            return false;
        }
    });

    // Recalculate cart if on cart page
    $('.product-quantity').keyup(function(event) {
        if($('.cart-items').length > 0) {
            recalculate();
        }
    });

    // Changed saved address
    $('#oat-commerce-address-form').submit(function(e) {
        $('.address-field').each(function() {
            var last_value = $(this).data('val');
            if(last_value != $(this).find('input').val()) {
                $('.address-changed').val(1);
            }
        });
    });

    // Click on image preview on product page
    $('.product-image-preview').click(function(e) {
        // TODO: show popup with big image
        var image_url = $(this).data('url');
        alert(image_url);
    });

    // Recalculate cart
    function recalculate() {
        var sum = 0;

        $('.cart-items .cart-item').each(function() {
            var item_cost = $(this).data('cost');
            var quantity = $(this).find('.product-quantity').val();
            $(this).find('.cart-item-cost').text(item_cost * quantity);
            sum += item_cost * quantity;
        });

        $('.order-sum').text(sum);
    }
});
