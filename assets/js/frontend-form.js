jQuery(document).ready(function($) {
    var $form = $('#bytesis-donation-form');
    var $amountBtns = $('.bytesis-preset-btn:not(.custom-amount-btn)');
    var $customBtn = $('.custom-amount-btn');
    var $customWrapper = $('.bytesis-custom-amount-wrapper');
    var $customInput = $('#bytesis_custom_amount');
    var $finalAmount = $('#bytesis_final_amount');
    var $submitBtn = $('#bytesis_submit_btn');

    // Handle preset amount click
    $amountBtns.on('click', function() {
        // Reset states
        $amountBtns.removeClass('active');
        $customBtn.removeClass('active');
        $customWrapper.slideUp(200);
        $customInput.removeAttr('required');

        // Set active state
        $(this).addClass('active');
        $finalAmount.val($(this).data('amount'));
    });

    // Handle custom amount click
    $customBtn.on('click', function() {
        $amountBtns.removeClass('active');
        $(this).addClass('active');
        $customWrapper.slideDown(200);
        $customInput.attr('required', 'required');
        $finalAmount.val($customInput.val());
    });

    // Update final amount when custom input changes
    $customInput.on('input', function() {
        if ($customBtn.hasClass('active')) {
            $finalAmount.val($(this).val());
        }
    });

    // Form submission validation
    $form.on('submit', function(e) {
        if (!$finalAmount.val() || parseFloat($finalAmount.val()) <= 0) {
            e.preventDefault();
            alert('Please select or enter a valid donation amount.');
            return false;
        }

        // Disable button to prevent double submission
        $submitBtn.prop('disabled', true).text('Processing...');
    });
});
