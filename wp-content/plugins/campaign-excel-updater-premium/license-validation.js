jQuery(document).ready(function ($) {
    $('#activate-license').click(function (e) {
        e.preventDefault();
        
        let licenseKey = $('#license_key').val();
        if (!licenseKey || licenseKey.length !== 14) {
            alert('Please enter a valid 14-character license key.');
            return;
        }

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'validate_license',
                license_key: licenseKey,
                security: campaign_excel_license_nonce
            },
            success: function (response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload(); // Reload to reflect activation
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            error: function () {
                alert('An error occurred while validating the license.');
            }
        });
    });
});
