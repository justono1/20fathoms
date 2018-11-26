(function($){
  console.log('yo');
  $('form.checkout').on( 'change', 'input[name^="payment_method"]', function() {
    console.log('yo');
    var payment_method = $(this).val();
    $.ajax({
        url: wp_ajax.ajax_url,
        dataType: 'json',
        method: 'post',
        data: {
            action: 'payment_method_changed',
            payment_method: payment_method
        },
        success(data) {
            console.log(data);
            $(document.body).trigger('update_checkout')
        }
    });
  });
})(jQuery);