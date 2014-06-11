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
                dataType: 'text',
                success: function(data) {
                    var status = parseInt(data);
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
});
