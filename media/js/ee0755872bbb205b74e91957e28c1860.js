var count_loading = 0;

//check empty fields
function checkEmptyFields(container)
{
	var empty = false;
	if(container.id =='billing-new-address-form') {
		if($('billing:country_id') && $('billing:country_id').value =='' && $('billing:country_id').style.display !='none' && $('billing:country_id').classList.contains('validate-select')) 
			empty = true;
		if($('billing:region_id') && $('billing:region_id').value =='' && $('billing:region_id').style.display !='none' && $('billing:region_id').classList.contains('validate-select')) 
			empty = true;
		if($('billing:region') && $('billing:region').value =='' && $('billing:region').style.display !='none' && $('billing:region').classList.contains('required-entry')) 
			empty = true; 
		if($('billing:postcode') && $('billing:postcode').value =='' && $('billing:postcode').classList.contains('required-entry')) 
			empty = true; 
		if($('billing:city') && $('billing:city').value =='' && $('billing:city').classList.contains('required-entry'))  
			empty = true;
		if($('billing:telephone') && $('billing:telephone').value =='' && $('billing:telephone').classList.contains('required-entry'))  
			empty = true;		 
	}
	if(container.id =='shipping-new-address-form') {
		if($('shipping:country_id') && $('shipping:country_id').value =='' && $('shipping:country_id').style.display !='none' && $('shipping:country_id').classList.contains('validate-select')) 
			empty = true;
		if($('shipping:region_id') && $('shipping:region_id').value =='' && $('shipping:region_id').style.display !='none' && $('shipping:region_id').classList.contains('validate-select')) 
			empty = true;
		if($('shipping:region') && $('shipping:region').value =='' && $('shipping:region').style.display !='none' && $('shipping:region').classList.contains('required-entry')) 
			empty = true;
		if($('shipping:postcode') && $('shipping:postcode').value =='' && $('shipping:postcode').classList.contains('required-entry')) 
			empty = true;
		if($('shipping:city') && $('shipping:city').value =='' && $('shipping:city').classList.contains('required-entry')) 
			empty = true;
		if($('shipping:telephone') && $('shipping:telephone').value =='' && $('shipping:telephone').classList.contains('required-entry')) 
			empty = true;		
	}
	return empty;
}
function check_valid_email(transport) {
	var response = getResponseText(transport);		
	var message = response.message;
	if (message == 'valid') {
		$('email-error-message').update('');
		$('valid_email_address_image').show();
	}
	else if (message == 'invalid'){
		$('valid_email_address_image').hide();
		$('email-error-message').update('<p>Invalid Email Address</p>');
	}
	else if (message == 'exists') {
		$('valid_email_address_image').hide();
		if (show_login_link)
			$('email-error-message').update('<p>Email address already registered. Please <a href="" onclick="login_popup.show(); return false;">log in</a> or use another email address.</p>');
		else {
			$('email-error-message').update('<p>Email address already registered. Please use another email address.</p>');
		}
	}
}

function getResponseText(transport) {
	if (transport && transport.responseText){
		try{
			response = eval('(' + transport.responseText + ')');
		}
		catch (e) {
			response = {};
		}
	}
	return response;
}

function get_billing_data(parameters) {
	var input_billing_array=$$('input[name^=billing]');
    var select_billing_array=$$('select[name^=billing]');
    var textarea_billing_array=$$('textarea[name^=billing]');
	var street_count = 0;
	
	for (var i=0; i < textarea_billing_array.length; i++) {
		var item = textarea_billing_array[i];		
		parameters[item.name] = item.value;		
	}
	
	for (var i=0; i < input_billing_array.length; i++) {
		var item = input_billing_array[i];
		if (item.type == 'checkbox') {
			if(item.checked)	{
				parameters[item.name] = item.value;
			}
		}
		else {
			if (item.name == 'billing[street][]') {
				var name = 'billing[street][' + street_count + ']';
				parameters[name] = item.value;
				street_count = street_count + 1;
			}
			else {
				parameters[item.name] = item.value;
			}
		}
	}
	
	var street_count=0;
	for (var i=0; i < select_billing_array.length; i++) {
		var item = select_billing_array[i];
		//data[item.name] = item.value;
		if (item.type == 'checkbox') {
			if(item.checked)	{
				parameters[item.name] = item.value;
			}
		}
		else {
			if (item.name == 'billing[street][]') {
				var name = 'billing[street][' + street_count + ']';
				parameters[name] = item.value;
				street_count = street_count + 1;
			}
			else {
				parameters[item.name] = item.value;
			}
		}
	}
}

function get_shipping_data(parameters) {
	var input_shipping_fields=$$('input[name^=shipping]');
    var select_shipping_fields=$$('select[name^=shipping]');
	var street_count = 0;
	for (var i=0; i < input_shipping_fields.length; i++) {
		var item = input_shipping_fields[i];
		if (item.type == 'checkbox') {
			if(item.checked)	{
				parameters[item.name] = item.value;
			}
		}
		else {
			if (item.name != 'shipping_method') {
				if (item.name == 'shipping[street][]') {
					var name = 'shipping[street][' + street_count + ']';
					parameters[name] = item.value;
					street_count = street_count + 1;
				}
				else {
					parameters[item.name] = item.value;
				}
			}
		}
	}
	
	var street_count = 0;
	for (var i=0; i < select_shipping_fields.length; i++) {
		var item = select_shipping_fields[i];
		//data[item.name] = item.value;
		if (item.type == 'checkbox') {
			if(item.checked)	{
				parameters[item.name] = item.value;
			}
		}
		else {
			if (item.name != 'shipping_method') {
				if (item.name == 'shipping[street][]') {
					var name = 'shipping[street][' + street_count + ']';
					parameters[name] = item.value;
					street_count = street_count + 1;
				}
				else {
					parameters[item.name] = item.value;
				}
			}
		}
	}
}

function showLoading() {
	
}

function save_address_information(save_address_url) {
	var form = $('one-step-checkout-form');
	var shipping_method = $RF(form, 'shipping_method');
	var parameters = {shipping_method: shipping_method};
	
	get_billing_data(parameters);	
	get_shipping_data(parameters);	
	
	var shipping_method_section = $$('div.onestepcheckout-shipping-method-section')[0];
	if(typeof shipping_method_section != 'undefined') {		
		shipping_method_section.update('<div class="ajax-loader1"></div>');
	}
	
	var payment_method_section = $$('div.onestepcheckout-payment-methods')[0];
	payment_method_section.update('<div class="ajax-loader2">&nbsp;</div>');
	
	var review = $('checkout-review-load');
	review.update('<div class="ajax-loader3"></div>');
	
	count_loading = count_loading + 1;
	$('onestepcheckout-button-place-order').disabled = true;
	$('onestepcheckout-button-place-order').removeClassName('btn-checkout');
	$('onestepcheckout-button-place-order').addClassName('place-order-loader');
	var request = new Ajax.Request(save_address_url, {	
		parameters: parameters,
		onSuccess: function(transport) {
			if(transport.status == 200)	{
				var response = getResponseText(transport);
				count_loading = count_loading - 1;
				if (count_loading == 0) {
					if(typeof shipping_method_section != 'undefined') {	
						shipping_method_section.update(response.shipping_method);
					}
					payment_method_section.update(response.payment_method);
					// show payment form if available
					if($RF(form, 'payment[method]') != null)    {
						try    {
							var payment_method = $RF(form, 'payment[method]');
							$('container_payment_method_' + payment_method).show();
							$('payment_form_' + payment_method).show();
						} catch(err)    {

						}
					}
					review.update(response.review);
					save_shipping_method(shipping_method_url);
					$('onestepcheckout-button-place-order').disabled = false;
					$('onestepcheckout-button-place-order').addClassName('btn-checkout');
					$('onestepcheckout-button-place-order').removeClassName('place-order-loader');
				}
			}
		},
		onFailure: ''
	});
}

function save_shipping_method(shipping_method_url, enable_update_payment) {
	if(typeof enable_update_payment == 'undefined')	{
		var enable_update_payment = false;
	}
	
	var form = $('one-step-checkout-form');
	var shipping_method = $RF(form, 'shipping_method');
	var payment_method = $RF(form, 'payment[method]');
	
	//reload payment only if this feature is enabled in admin - show image loading
	var payment_method_section = $$('div.onestepcheckout-payment-methods')[0];
	if (enable_update_payment) {		
		payment_method_section.update('<div class="ajax-loader2">&nbsp;</div>');
	}
	
	//show image loading for review total
	var review = $('checkout-review-load');
	review.update('<div class="ajax-loader3"></div>');
	
	var parameters = {
		shipping_method: shipping_method,
		payment_method: payment_method
	};
	
	//Find payment parameters and include 
	var items = $$('input[name^=payment]', 'select[name^=payment]');
	var names = items.pluck('name');
	var values = items.pluck('value');
	
	for(var x=0; x < names.length; x++)	{
		if(names[x] != 'payment[method]')	{
			parameters[names[x]] = values[x];	
		}
	}
	$('onestepcheckout-button-place-order').disabled = true;
	$('onestepcheckout-button-place-order').removeClassName('btn-checkout');
	$('onestepcheckout-button-place-order').addClassName('place-order-loader');
	var request = new Ajax.Request(shipping_method_url, {
		method: 'post',
		parameters: parameters,
		onFailure: '',
		onSuccess: function(transport) {
			if(transport.status == 200)	{
				var response = getResponseText(transport);
				if (enable_update_payment) {
					payment_method_section.update(response.payment);
					// show payment form if available
					if($RF(form, 'payment[method]') != null)    {
						try    {
							var payment_method = $RF(form, 'payment[method]');
							$('container_payment_method_' + payment_method).show();
							$('payment_form_' + payment_method).show();
						} catch(err)    {

						}
					}
				}
				review.update(response.review);
				$('onestepcheckout-button-place-order').disabled = false;
				$('onestepcheckout-button-place-order').addClassName('btn-checkout');
				$('onestepcheckout-button-place-order').removeClassName('place-order-loader');
				if(onestepcheckoutinadmin) recollectTotal();
			}
		}
	});
	
}

function updateSection(transport) {
	var response = getResponseText(transport);
	if (response.shipping_method) {
		var shipping_method_section = $$('div.onestepcheckout-shipping-method-section')[0];
		if(typeof shipping_method_section != 'undefined') {		
			
		}
	}
	if (response.payment_method) {
	
	}
}

function add_coupon_code(add_coupon_url) {
	var review = $('checkout-review-load');
	review.update('<div class="ajax-loader3"></div>');
	var coupon_code = $('coupon_code_onestepcheckout').value;	
	var parameters = {coupon_code: coupon_code};	
	var request = new Ajax.Request(add_coupon_url, {
		method: 'post',
		onFailure: '',
		parameters: parameters,
		onSuccess: function(transport) {
			var response = getResponseText(transport);
			if (response.error) {				
				review.update(response.review_html);
				$('remove_coupon_code_button').hide();
				alert(response.message);
			}
			else {
				review.update(response.review_html);
				$('remove_coupon_code_button').show();
				if(onestepcheckoutinadmin) recollectTotal();
			}
		}
	});
	
}

function remove_coupon_code(add_coupon_url) {
	var review = $('checkout-review-load');
	review.update('<div class="ajax-loader3"></div>');
	var coupon_code = $('coupon_code_onestepcheckout').value;	
	var parameters = {coupon_code: coupon_code, remove: '1'};	
	var request = new Ajax.Request(add_coupon_url, {
		method: 'post',
		onFailure: '',
		parameters: parameters,
		onSuccess: function(transport) {
			var response = getResponseText(transport);
			if (response.error) {				
				review.update(response.review_html);
			}
			else {
				review.update(response.review_html);
				$('coupon_code_onestepcheckout').value = '';
				$('remove_coupon_code_button').hide();	
				if(onestepcheckoutinadmin) recollectTotal();				
			}
		}
	});
}

function setNewAddress(isNew, type, save_address_url) {
	if (isNew) {
		resetSelectedAddress(type);
		$(type + '-new-address-form').show();
	}
	else {
		$(type + '-new-address-form').hide();
	}	
	save_address_information(save_address_url);
}

function resetSelectedAddress(type) {
	var selectElement = $(type + '-address-select')
	if (selectElement) {
		selectElement.value = '';
	}
}

function showLogin(url) {
	TINY.box.show(url, 1, 400, 250, 150);
	return false;
}

function showpwdbox(url) {
	TINY.box.show(url, 1, 400, 250, 150);
	return false;
}

function showTermsAndCondition() {
	TINY.box.show(show_term_condition_url, 1, term_popup_width, term_popup_height, 150);
	return false;
}

function loginProcess(transport) {
	var response = getResponseText(transport);
	if (response.error && response.error != '') {
		$('onestepcheckout-login-error-message').update(response.error);
		$('onestepcheckout-login-error-message').show();
		disableLoginLoading();
	}
	else {
		$('onestepcheckout-login-error-message').hide();
		window.location = window.location;
	}
}

function passwordProcess(transport) {
	var response = getResponseText(transport);
	if (response.success) {
		$('onestepcheckout-password-error-message').hide();
		$('onestepcheckout-password-loading').hide();
		$('onestepcheckout-password-success-message').show();
	}
	else {
		if (response.error && response.error != '') {
			$('onestepcheckout-password-error-message').update(response.error);
			$('onestepcheckout-password-error-message').show();
			disablePassLoading();
		}
	}
}

function showPassLoading() {
	$('onestepcheckout-password-error-message').hide();
	$('osc-forgotpassword-form').hide();
	$('onestepcheckout-password-loading').show();
}

function disablePassLoading() {
	$('osc-forgotpassword-form').show();
	$('onestepcheckout-password-loading').hide();
}


function showLoginLoading() {
	$('onestepcheckout-login-error-message').hide();
	$('onestepcheckout-login-form').hide();
	$('onestepcheckout-login-loading').show();
}

function disableLoginLoading() {
	$('onestepcheckout-login-form').show();
	$('onestepcheckout-login-loading').hide();
}

function change_class_name(element, oldStep, newStep) {
	if(element) {
		element.removeClassName('step_'+oldStep);
		element.addClassName('step_'+newStep);
	}
}

function $RF(el, radioGroup) {
	if($(el).type && $(el).type.toLowerCase() == 'radio') {
		var radioGroup = $(el).name;
		var el = $(el).form;
	} else if ($(el).tagName.toLowerCase() != 'form') {
		return false;
	}

	var checked = $(el).getInputs('radio', radioGroup).find(
		function(re) {return re.checked;}
	);
	return (checked) ? $F(checked) : null;
}

function initWhatIsCvvListeners() {
	$$('.cvv-what-is-this').each(function(element){		
		Event.observe(element, 'click', toggleToolTip);
	});
}

function checkPaymentMethod(){
	var options = document.getElementsByName('payment[method]');
	var pay = true;
	for(var i=0;i<options.length;i++){
		if($(options[i].id).checked){
			pay = false;
			break;
		}
	}
	return pay;
}

function addGiftwrap(url){                                          
    var parameters = {};
    if(!$('onestepcheckout_giftwrap_checkbox').checked) {
        parameters['remove'] = 1;
    }else{
		var options = document.getElementsByName('payment[method]');
		if(checkPaymentMethod()){
			if($(options[0].id))
				$(options[0].id).checked = true;
		}
	}            
    var summary = $('checkout-review-load');
    summary.update('<div class="ajax-loader3">&nbsp;</div>');

    new Ajax.Request(url, {
        method: 'post',
        parameters: parameters,
        onFailure: '',
        onSuccess: function(transport) {                                 
            if(transport.status == 200)	{                                            
                summary.update(transport.responseText);
                save_shipping_method(shipping_method_url, enable_update_payment);
            }
        }
    });           
}

/**
 * FORM LOGIN
 **/
var OneStepCheckoutLoginPopup = Class.create({
    initialize: function(options) {
        this.options = options;
        this.popup_container = $('onestepcheckout-login-popup');
        this.popup_link = $('onestepcheckout-login-link');
        this.popup = null;
        this.createPopup();
        this.mode = 'login';

        this.forgot_password_link = $('onestepcheckout-forgot-password-link');
        this.forgot_password_container = $('onestepcheckout-login-popup-contents-forgot');
        this.forgot_password_loading = $('onestepcheckout-forgot-loading');
        this.forgot_password_error = $('onestepcheckout-forgot-error');
        this.forgot_password_success = $('onestepcheckout-forgot-success');
        this.forgot_password_button = $('onestepcheckout-forgot-button');
        this.forgot_password_table = $('onestepcheckout-forgot-table');

        this.login_link = $('onestepcheckout-return-login-link');
        this.login_container = $('onestepcheckout-login-popup-contents-login');
        this.login_table = $('onestepcheckout-login-table');
        this.login_error = $('onestepcheckout-login-error');
        this.login_loading = $('onestepcheckout-login-loading');
        this.login_button = $('onestepcheckout-login-button');
        this.login_form = $('onestepcheckout-login-form');
        this.login_username = $('id_onestepcheckout_username');

        /* Bindings for the enter button */
		var login_validator = new Validation('onestepcheckout-login-form');		
        this.keypress_handler = function(e) {					
			if(e.keyCode == Event.KEY_RETURN) {
				if (login_validator.validate()) {
					e.preventDefault();

					if(this.mode == 'login') {
						this.login_handler();
					} else if(this.mode == 'forgot') {
						this.forgot_password_handler();
					}
				}
			}
        }.bind(this);

        this.login_handler = function(e) {
			if (login_validator.validate()) {
				var parameters = this.login_form.serialize(true);
				var url = this.options.login_url;
				this.showLoginLoading();

				new Ajax.Request(url, {
					method: 'post',
					parameters: parameters,
					onSuccess: function(transport) {
						var result = transport.responseText.evalJSON();
						if(result.success) {
							window.location = window.location;
						} else {
							this.showLoginError(result.error);
						}
					}.bind(this)
				});
			}
        };
		
		var fogot_validator = new Validation('onestepcheckout-forgot-form');
        this.forgot_password_handler = function(e) {
            var email = $('id_onestepcheckout_email').getValue();

           /*  if(email == '') {
                alert(this.options.translations.invalid_email);
                return;
            } */
			if (fogot_validator.validate()) {
				this.showForgotPasswordLoading();

				/* Prepare AJAX call */
				var url = this.options.forgot_password_url;

				new Ajax.Request(url, {
					method: 'post',
					parameters: { email: email },
					onSuccess: function(transport) {
						var result = transport.responseText.evalJSON();

						if(result.success) {
							/* Show success message */
							this.showForgotPasswordSuccess();

							/* Pre-set username to simplify login */
							this.login_username.setValue(email);
						} else {
							/* Show error message */
							this.showForgotPasswordError();
						}

					}.bind(this)
				});
			}
        };

        this.bindEventHandlers();
    },

    bindEventHandlers: function() {
        /* First bind the link for opening the popup */
        if(this.popup_link){
            this.popup_link.observe('click', function(e) {
                e.preventDefault();
                this.popup.open();
            }.bind(this));
        }

        /* Link for closing the popup */
        if(this.popup_container){
            this.popup_container.select('p.close a').invoke(
                'observe', 'click', function(e) {
                this.popup.close();
            }.bind(this));
        }

        /* Link to switch between states */
        if(this.login_link){
            this.login_link.observe('click', function(e) {
                e.preventDefault();
                this.forgot_password_container.hide();
                this.login_container.show();
                this.mode = 'login';
            }.bind(this));
        }

        /* Link to switch between states */
        if(this.forgot_password_link){
            this.forgot_password_link.observe('click', function(e) {
                e.preventDefault();
                this.login_container.hide();
                this.forgot_password_container.show();
                this.mode = 'forgot';
            }.bind(this));
        }

        /* Now bind the submit button for logging in */
        if(this.login_button){
            this.login_button.observe(
                'click', this.login_handler.bind(this));
        }

        /* Now bind the submit button for forgotten password */
        if(this.forgot_password_button){
            this.forgot_password_button.observe('click',
                this.forgot_password_handler.bind(this));
        }

        /* Handle return keypress when open */
        if(this.popup){
            this.popup.observe('afterOpen', function(e) {
                document.observe('keypress', this.keypress_handler);
            }.bind(this));

            this.popup.observe('afterClose', function(e) {
                this.resetPopup();
                document.stopObserving('keypress', this.keypress_handler);
            }.bind(this));
        }

    },

    resetPopup: function() {
        this.login_table.show();
        this.forgot_password_table.show();

        this.login_loading.hide();
        this.forgot_password_loading.hide();

        this.login_error.hide();
        this.forgot_password_error.hide();

        this.login_container.show();
        this.forgot_password_container.hide();
    },

    showLoginError: function(error) {
        this.login_table.show();
        this.login_error.show();
        this.login_loading.hide();

        if(error) {
            this.login_error.update(error);
        }
    },

    showLoginLoading: function() {
        this.login_table.hide();
        this.login_loading.show();
        this.login_error.hide();
    },

    showForgotPasswordSuccess: function() {
        this.forgot_password_error.hide();
        this.forgot_password_loading.hide();
        this.forgot_password_table.hide();
        this.forgot_password_success.show();
    },

    showForgotPasswordError: function() {
        this.forgot_password_error.show();
        this.forgot_password_error.update(
            this.options.translations.email_not_found),

        this.forgot_password_table.show();
        this.forgot_password_loading.hide();
    },

    showForgotPasswordLoading: function() {
        this.forgot_password_loading.show();
        this.forgot_password_error.hide();
        this.forgot_password_table.hide();
    },

    show: function(){
        this.popup.open();
    },

    createPopup: function() {
        this.popup = new Control.Modal(this.popup_container, {
            overlayOpacity: 0.65,
            fade: true,
            fadeDuration: 0.3
        });
    }
});

//Validate Radio
function $RF(el, radioGroup) {
    if($(el).type && $(el).type.toLowerCase() == 'radio') {
        var radioGroup = $(el).name;
        var el = $(el).form;
    } else if ($(el).tagName.toLowerCase() != 'form') {
        return false;
    }

    var checked = $(el).getInputs('radio', radioGroup).find(
            function(re) {return re.checked;}
    );
    return (checked) ? $F(checked) : null;
}

function $RFF(el, radioGroup) {
    if($(el).type && $(el).type.toLowerCase() == 'radio') {
        var radioGroup = $(el).name;
        var el = $(el).form;
    } else if ($(el).tagName.toLowerCase() != 'form') {
        return false;
    }
    return $(el).getInputs('radio', radioGroup).first();
}

function get_separate_save_methods_function(url, update_payments)
{
    if(typeof update_payments == 'undefined')    {
        var update_payments = false;
    }

    return function(e)    {
        if(typeof e != 'undefined')    {
            var element = e.element();

            if(element.name != 'shipping_method')    {
                update_payments = false;
            }
        }

        var form = $('one-step-checkout-form');
        var shipping_method = $RF(form, 'shipping_method');
        var payment_method = $RF(form, 'payment[method]');
        var totals = get_totals_element();

        var freeMethod = $('p_method_free');
        if(freeMethod){
            payment.reloadcallback = true;
            payment.countreload = 1;
        }

        totals.update('<div class="loading-ajax">&nbsp;</div>');

        if(update_payments)    {
            var payment_methods = $$('div.payment-methods')[0];
            payment_methods.update('<div class="loading-ajax">&nbsp;</div>');
        }

        var parameters = {
                shipping_method: shipping_method,
                payment_method: payment_method
        }

        /* Find payment parameters and include */
        var items = $$('input[name^=payment]').concat($$('select[name^=payment]'));
        var names = items.pluck('name');
        var values = items.pluck('value');

        for(var x=0; x < names.length; x++)    {
            if(names[x] != 'payment[method]')    {
                parameters[names[x]] = values[x];
            }
        }

        new Ajax.Request(url, {
            method: 'post',
            onSuccess: function(transport)    {
            if(transport.status == 200)    {
                var data = transport.responseText.evalJSON();
                var form = $('onestepcheckout-form');

                totals.update(data.summary);

                if(update_payments)    {

                    payment_methods.replace(data.payment_method);

                    $$('div.payment-methods input[name^=payment\[method\]]').invoke('observe', 'click', get_separate_save_methods_function(url));
                    $$('div.payment-methods input[name^=payment\[method\]]').invoke('observe', 'click', function() {
                        $$('div.onestepcheckout-payment-method-error').each(function(item) {
                            new Effect.Fade(item);
                        });
                    });

                    if($RF($('one-step-checkout-form'), 'payment[method]') != null)    {
                        try    {
                            var payment_method = $RF(form, 'payment[method]');
                            $('container_payment_method_' + payment_method).show();
                            $('payment_form_' + payment_method).show();
                        } catch(err)    {

                        }
                    }
                }
            }
        },
        parameters: parameters
        });
    }
}

var TINY={};

function T$(i){return document.getElementById(i)}

TINY.box=function(){
	var p,m,b,fn,ic,iu,iw,ih,ia,f=0;
	return{
		show:function(c,u,w,h,a,t){
			if(!f){
				p=document.createElement('div'); p.id='tinybox';
				m=document.createElement('div'); m.id='tinymask';
				b=document.createElement('div'); b.id='tinycontent';
				document.body.appendChild(m); document.body.appendChild(p); p.appendChild(b);
				//m.onclick=TINY.box.hide; window.onresize=TINY.box.resize; f=1
			}
			if(!a&&!u){
				p.style.width=w?w+'px':'auto'; p.style.height=h?h+'px':'auto';
				p.style.backgroundImage='none'; b.innerHTML=c
			}else{
				b.style.display='none'; p.style.width=p.style.height='100px'
			}
			this.mask();
			ic=c; iu=u; iw=w; ih=h; ia=a; this.alpha(m,1,80,3);
			if(t){setTimeout(function(){TINY.box.hide()},1000*t)}
		},
		fill:function(c,u,w,h,a){
			if(u){
				p.style.backgroundImage='';
				var x=window.XMLHttpRequest?new XMLHttpRequest():new ActiveXObject('Microsoft.XMLHTTP');
				x.onreadystatechange=function(){
					if(x.readyState==4&&x.status==200){TINY.box.psh(x.responseText,w,h,a)}
				};
				x.open('GET',c,1); x.send(null)
			}else{
				this.psh(c,w,h,a)
			}
		},
		psh:function(c,w,h,a){
			if(a){
				if(!w||!h){
					var x=p.style.width, y=p.style.height; b.innerHTML=c;
					p.style.width=w?w+'px':''; p.style.height=h?h+'px':'';
					b.style.display='';
					w=parseInt(b.offsetWidth); h=parseInt(b.offsetHeight);
					b.style.display='none'; p.style.width=x; p.style.height=y;
				}else{
					b.innerHTML=c
				}
				this.size(p,w,h)
			}else{
				p.style.backgroundImage='none'
			}
		},
		hide:function(){
			TINY.box.alpha(p,-1,0,3)
		},
		resize:function(){
			TINY.box.pos(); TINY.box.mask()
		},
		mask:function(){
			m.style.height=TINY.page.total(1)+'px';
			m.style.width=''; m.style.width=TINY.page.total(0)+'px'
		},
		pos:function(){
			var t=(TINY.page.height()/2)-(p.offsetHeight/2); t=t<10?10:t;
			p.style.top=(t+TINY.page.top())+'px';
			p.style.left=(TINY.page.width()/2)-(p.offsetWidth/2)+'px'
		},
		alpha:function(e,d,a){
			clearInterval(e.ai);
			if(d==1){
				e.style.opacity=0; e.style.filter='alpha(opacity=0)';
				e.style.display='block'; this.pos()
			}
			e.ai=setInterval(function(){TINY.box.ta(e,a,d)},20)
		},
		ta:function(e,a,d){
			var o=Math.round(e.style.opacity*100);
			if(o==a){
				clearInterval(e.ai);
				if(d==-1){
					e.style.display='none';
					e==p?TINY.box.alpha(m,-1,0,2):b.innerHTML=p.style.backgroundImage=''
				}else{
					e==m?this.alpha(p,1,100):TINY.box.fill(ic,iu,iw,ih,ia)
				}
			}else{
				var n=Math.ceil((o+((a-o)*.5))); n=n==1?0:n;
				e.style.opacity=n/100; e.style.filter='alpha(opacity='+n+')'
			}
		},
		size:function(e,w,h){
			e=typeof e=='object'?e:T$(e); clearInterval(e.si);
			var ow=e.offsetWidth, oh=e.offsetHeight,
			wo=ow-parseInt(e.style.width), ho=oh-parseInt(e.style.height);
			var wd=ow-wo>w?0:1, hd=(oh-ho>h)?0:1;
			e.si=setInterval(function(){TINY.box.ts(e,w,wo,wd,h,ho,hd)},20)
		},
		ts:function(e,w,wo,wd,h,ho,hd){
			var ow=e.offsetWidth-wo, oh=e.offsetHeight-ho;
			if(ow==w&&oh==h){
				clearInterval(e.si); p.style.backgroundImage='none'; b.style.display='block'
			}else{
				if(ow!=w){var n=ow+((w-ow)*.5); e.style.width=wd?Math.ceil(n)+'px':Math.floor(n)+'px'}
				if(oh!=h){var n=oh+((h-oh)*.5); e.style.height=hd?Math.ceil(n)+'px':Math.floor(n)+'px'}
				this.pos()
			}
		}
	}
}();

TINY.page=function(){
	return{
		top:function(){return document.documentElement.scrollTop||document.body.scrollTop},
		width:function(){return self.innerWidth||document.documentElement.clientWidth||document.body.clientWidth},
		height:function(){return self.innerHeight||document.documentElement.clientHeight||document.body.clientHeight},
		total:function(d){
			var b=document.body, e=document.documentElement;
			return d?Math.max(Math.max(b.scrollHeight,e.scrollHeight),Math.max(b.clientHeight,e.clientHeight)):
			Math.max(Math.max(b.scrollWidth,e.scrollWidth),Math.max(b.clientWidth,e.clientWidth))
		}
	}
}();
/**
 * @author Ryan Johnson <http://syntacticx.com/>
 * @copyright 2008 PersonalGrid Corporation <http://personalgrid.com/>
 * @package LivePipe UI
 * @license MIT
 * @url http://livepipe.net/core
 * @require prototype.js
 */

if(typeof(Control) == 'undefined')
    Control = {};

var $proc = function(proc){
    return typeof(proc) == 'function' ? proc : function(){return proc};
};

var $value = function(value){
    return typeof(value) == 'function' ? value() : value;
};

Object.Event = {
    extend: function(object){
        object._objectEventSetup = function(event_name){
            this._observers = this._observers || {};
            this._observers[event_name] = this._observers[event_name] || [];
        };
        object.observe = function(event_name,observer){
            if(typeof(event_name) == 'string' && typeof(observer) != 'undefined'){
                this._objectEventSetup(event_name);
                if(!this._observers[event_name].include(observer))
                    this._observers[event_name].push(observer);
            }else
                for(var e in event_name)
                    this.observe(e,event_name[e]);
        };
        object.stopObserving = function(event_name,observer){
            this._objectEventSetup(event_name);
            if(event_name && observer)
                this._observers[event_name] = this._observers[event_name].without(observer);
            else if(event_name)
                this._observers[event_name] = [];
            else
                this._observers = {};
        };
        object.observeOnce = function(event_name,outer_observer){
            var inner_observer = function(){
                outer_observer.apply(this,arguments);
                this.stopObserving(event_name,inner_observer);
            }.bind(this);
            this._objectEventSetup(event_name);
            this._observers[event_name].push(inner_observer);
        };
        object.notify = function(event_name){
            this._objectEventSetup(event_name);
            var collected_return_values = [];
            var args = $A(arguments).slice(1);
            try{
                for(var i = 0; i < this._observers[event_name].length; ++i)
                    collected_return_values.push(this._observers[event_name][i].apply(this,args) || null);
            }catch(e){
                if(e == $break)
                    return false;
                else
                    throw e;
            }
            return collected_return_values;
        };
        if(object.prototype){
            object.prototype._objectEventSetup = object._objectEventSetup;
            object.prototype.observe = object.observe;
            object.prototype.stopObserving = object.stopObserving;
            object.prototype.observeOnce = object.observeOnce;
            object.prototype.notify = function(event_name){
                if(object.notify){
                    var args = $A(arguments).slice(1);
                    args.unshift(this);
                    args.unshift(event_name);
                    object.notify.apply(object,args);
                }
                this._objectEventSetup(event_name);
                var args = $A(arguments).slice(1);
                var collected_return_values = [];
                try{
                    if(this.options && this.options[event_name] && typeof(this.options[event_name]) == 'function')
                        collected_return_values.push(this.options[event_name].apply(this,args) || null);
                    var callbacks_copy = this._observers[event_name]; // since original array will be modified after observeOnce calls
                    for(var i = 0; i < callbacks_copy.length; ++i)
                        collected_return_values.push(callbacks_copy[i].apply(this,args) || null);
                }catch(e){
                    if(e == $break)
                        return false;
                    else
                        throw e;
                }
                return collected_return_values;
            };
        }
    }
};

/* Begin Core Extensions */

//Element.observeOnce
Element.addMethods({
    observeOnce: function(element,event_name,outer_callback){
        var inner_callback = function(){
            outer_callback.apply(this,arguments);
            Element.stopObserving(element,event_name,inner_callback);
        };
        Element.observe(element,event_name,inner_callback);
    }
});

//mouse:wheel
(function(){
    function wheel(event){
        var delta, element, custom_event;
        // normalize the delta
        if (event.wheelDelta) { // IE & Opera
            delta = event.wheelDelta / 120;
        } else if (event.detail) { // W3C
            delta =- event.detail / 3;
        }
        if (!delta) { return; }
        element = Event.extend(event).target;
        element = Element.extend(element.nodeType === Node.TEXT_NODE ? element.parentNode : element);
        custom_event = element.fire('mouse:wheel',{ delta: delta });
        if (custom_event.stopped) {
            Event.stop(event);
            return false;
        }
    }
    document.observe('mousewheel',wheel);
    document.observe('DOMMouseScroll',wheel);
})();

/* End Core Extensions */

//from PrototypeUI
var IframeShim = Class.create({
    initialize: function() {
        this.element = new Element('iframe',{
            style: 'position:absolute;filter:progid:DXImageTransform.Microsoft.Alpha(opacity=0);display:none',
            src: 'javascript:void(0);',
            frameborder: 0
        });
        $(document.body).insert(this.element);
    },
    hide: function() {
        this.element.hide();
        return this;
    },
    show: function() {
        this.element.show();
        return this;
    },
    positionUnder: function(element) {
        var element = $(element);
        var offset = element.cumulativeOffset();
        var dimensions = element.getDimensions();
        this.element.setStyle({
            left: offset[0] + 'px',
            top: offset[1] + 'px',
            width: dimensions.width + 'px',
            height: dimensions.height + 'px',
            zIndex: element.getStyle('zIndex') - 1
        }).show();
        return this;
    },
    setBounds: function(bounds) {
        for(prop in bounds)
            bounds[prop] += 'px';
        this.element.setStyle(bounds);
        return this;
    },
    destroy: function() {
        if(this.element)
            this.element.remove();
        return this;
    }
});
/**
 * @author Ryan Johnson <http://syntacticx.com/>
 * @copyright 2008 PersonalGrid Corporation <http://personalgrid.com/>
 * @package LivePipe UI
 * @license MIT
 * @url http://livepipe.net/control/window
 * @require prototype.js, effects.js, draggable.js, resizable.js, livepipe.js
 */

//adds onDraw and constrainToViewport option to draggable
if(typeof(Draggable) != 'undefined'){
    //allows the point to be modified with an onDraw callback
    Draggable.prototype.draw = function(point) {
        var pos = Position.cumulativeOffset(this.element);
        if(this.options.ghosting) {
            var r = Position.realOffset(this.element);
            pos[0] += r[0] - Position.deltaX; pos[1] += r[1] - Position.deltaY;
        }

        var d = this.currentDelta();
        pos[0] -= d[0]; pos[1] -= d[1];

        if(this.options.scroll && (this.options.scroll != window && this._isScrollChild)) {
            pos[0] -= this.options.scroll.scrollLeft-this.originalScrollLeft;
            pos[1] -= this.options.scroll.scrollTop-this.originalScrollTop;
        }

        var p = [0,1].map(function(i){
            return (point[i]-pos[i]-this.offset[i])
        }.bind(this));

        if(this.options.snap) {
            if(typeof this.options.snap == 'function') {
                p = this.options.snap(p[0],p[1],this);
            } else {
                if(this.options.snap instanceof Array) {
                    p = p.map( function(v, i) {return Math.round(v/this.options.snap[i])*this.options.snap[i] }.bind(this))
                } else {
                    p = p.map( function(v) {return Math.round(v/this.options.snap)*this.options.snap }.bind(this))
                  }
            }
        }

        if(this.options.onDraw)
            this.options.onDraw.bind(this)(p);
        else{
            var style = this.element.style;
            if(this.options.constrainToViewport){
                var viewport_dimensions = document.viewport.getDimensions();
                var container_dimensions = this.element.getDimensions();
                var margin_top = parseInt(this.element.getStyle('margin-top'));
                var margin_left = parseInt(this.element.getStyle('margin-left'));
                var boundary = [[
                    0 - margin_left,
                    0 - margin_top
                ],[
                    (viewport_dimensions.width - container_dimensions.width) - margin_left,
                    (viewport_dimensions.height - container_dimensions.height) - margin_top
                ]];
                if((!this.options.constraint) || (this.options.constraint=='horizontal')){
                    if((p[0] >= boundary[0][0]) && (p[0] <= boundary[1][0]))
                        this.element.style.left = p[0] + "px";
                    else
                        this.element.style.left = ((p[0] < boundary[0][0]) ? boundary[0][0] : boundary[1][0]) + "px";
                }
                if((!this.options.constraint) || (this.options.constraint=='vertical')){
                    if((p[1] >= boundary[0][1] ) && (p[1] <= boundary[1][1]))
                        this.element.style.top = p[1] + "px";
                  else
                        this.element.style.top = ((p[1] <= boundary[0][1]) ? boundary[0][1] : boundary[1][1]) + "px";
                }
            }else{
                if((!this.options.constraint) || (this.options.constraint=='horizontal'))
                  style.left = p[0] + "px";
                if((!this.options.constraint) || (this.options.constraint=='vertical'))
                  style.top     = p[1] + "px";
            }
            if(style.visibility=="hidden")
                style.visibility = ""; // fix gecko rendering
        }
    };
}

if(typeof(Prototype) == "undefined")
    throw "Control.Window requires Prototype to be loaded.";
if(typeof(IframeShim) == "undefined")
    throw "Control.Window requires IframeShim to be loaded.";
if(typeof(Object.Event) == "undefined")
    throw "Control.Window requires Object.Event to be loaded.";
/*
    known issues:
        - when iframe is clicked is does not gain focus
        - safari can't open multiple iframes properly
        - constrainToViewport: body must have no margin or padding for this to work properly
        - iframe will be mis positioned during fade in
        - document.viewport does not account for scrollbars (this will eventually be fixed in the prototype core)
    notes
        - setting constrainToViewport only works when the page is not scrollable
        - setting draggable: true will negate the effects of position: center
*/
Control.Window = Class.create({
    initialize: function(container,options){
        Control.Window.windows.push(this);

        //attribute initialization
        this.container = false;
        this.isOpen = false;
        this.href = false;
        this.sourceContainer = false; //this is optionally the container that will open the window
        this.ajaxRequest = false;
        this.remoteContentLoaded = false; //this is set when the code to load the remote content is run, onRemoteContentLoaded is fired when the connection is closed
        this.numberInSequence = Control.Window.windows.length + 1; //only useful for the effect scoping
        this.indicator = false;
        this.effects = {
            fade: false,
            appear: false
        };
        this.indicatorEffects = {
            fade: false,
            appear: false
        };

        //options
        this.options = Object.extend({
            //lifecycle
            beforeOpen: Prototype.emptyFunction,
            afterOpen: Prototype.emptyFunction,
            beforeClose: Prototype.emptyFunction,
            afterClose: Prototype.emptyFunction,
            //dimensions and modes
            height: null,
            width: null,
            className: false,
            position: 'center', //'center', 'center_once', 'relative', [x,y], [function(){return x;},function(){return y;}]
            offsetLeft: 0, //available only for anchors opening the window, or windows set to position: hover
            offsetTop: 0, //""
            iframe: false, //if the window has an href, this will display the href as an iframe instead of requesting the url as an an Ajax.Request
            hover: false, //element object to hover over, or if "true" only available for windows with sourceContainer (an anchor or any element already on the page with an href attribute)
            indicator: false, //element to show or hide when ajax requests, images and iframes are loading
            closeOnClick: false, //does not work with hover,can be: true (click anywhere), 'container' (will refer to this.container), or element (a specific element)
            iframeshim: true, //whether or not to position an iFrameShim underneath the window
            //effects
            fade: false,
            fadeDuration: 0.75,
            //draggable
            draggable: false,
            onDrag: Prototype.emptyFunction,
            //resizable
            resizable: false,
            minHeight: false,
            minWidth: false,
            maxHeight: false,
            maxWidth: false,
            onResize: Prototype.emptyFunction,
            //draggable and resizable
            constrainToViewport: false,
            //ajax
            method: 'post',
            parameters: {},
            onComplete: Prototype.emptyFunction,
            onSuccess: Prototype.emptyFunction,
            onFailure: Prototype.emptyFunction,
            onException: Prototype.emptyFunction,
            //any element with an href (image,iframe,ajax) will call this after it is done loading
            onRemoteContentLoaded: Prototype.emptyFunction,
            insertRemoteContentAt: false //false will set this to this.container, can be string selector (first returned will be selected), or an Element that must be a child of this.container
        },options || {});

        //container setup
        this.indicator = this.options.indicator ? $(this.options.indicator) : false;
        if(container){
            if(typeof(container) == "string" && container.match(Control.Window.uriRegex))
                this.href = container;
            else{
                this.container = $(container);
                //need to create the container now for tooltips (or hover: element with no container already on the page)
                //second call made below will not create the container since the check is done inside createDefaultContainer()
                this.createDefaultContainer(container);
                //if an element with an href was passed in we use it to activate the window
                if(this.container && ((this.container.readAttribute('href') && this.container.readAttribute('href') != '') || (this.options.hover && this.options.hover !== true))){
                    if(this.options.hover && this.options.hover !== true)
                        this.sourceContainer = $(this.options.hover);
                    else{
                        this.sourceContainer = this.container;
                        this.href = this.container.readAttribute('href');
                        var rel = this.href.match(/^#(.+)$/);
                        if(rel && rel[1]){
                            this.container = $(rel[1]);
                            this.href = false;
                        }else
                            this.container = false;
                    }
                    //hover or click handling
                    this.sourceContainerOpenHandler = function(event){
                        this.open(event);
                        event.stop();
                        return false;
                    }.bindAsEventListener(this);
                    this.sourceContainerCloseHandler = function(event){
                        this.close(event);
                    }.bindAsEventListener(this);
                    this.sourceContainerMouseMoveHandler = function(event){
                        this.position(event);
                    }.bindAsEventListener(this);
                    if(this.options.hover){
                        this.sourceContainer.observe('mouseenter',this.sourceContainerOpenHandler);
                        this.sourceContainer.observe('mouseleave',this.sourceContainerCloseHandler);
                        if(this.options.position == 'mouse')
                            this.sourceContainer.observe('mousemove',this.sourceContainerMouseMoveHandler);
                    }else
                        this.sourceContainer.observe('click',this.sourceContainerOpenHandler);
                }
            }
        }
        this.createDefaultContainer(container);
        if(this.options.insertRemoteContentAt === false)
            this.options.insertRemoteContentAt = this.container;
        var styles = {
            margin: 0,
            position: 'absolute',
            zIndex: Control.Window.initialZIndexForWindow()
        };
        if(this.options.width)
            styles.width = $value(this.options.width) + 'px';
        if(this.options.height)
            styles.height = $value(this.options.height) + 'px';
        this.container.setStyle(styles);
        if(this.options.className)
            this.container.addClassName(this.options.className);
        this.positionHandler = this.position.bindAsEventListener(this);
        this.outOfBoundsPositionHandler = this.ensureInBounds.bindAsEventListener(this);
        this.bringToFrontHandler = this.bringToFront.bindAsEventListener(this);
        this.container.observe('mousedown',this.bringToFrontHandler);
        this.container.hide();
        this.closeHandler = this.close.bindAsEventListener(this);
        //iframeshim setup
        if(this.options.iframeshim){
            this.iFrameShim = new IframeShim();
            this.iFrameShim.hide();
        }
        //resizable support
        this.applyResizable();
        //draggable support
        this.applyDraggable();

        //makes sure the window can't go out of bounds
        Event.observe(window,'resize',this.outOfBoundsPositionHandler);

        this.notify('afterInitialize');
    },
    open: function(event){
        if(this.isOpen){
            this.bringToFront();
            return false;
        }
        if(this.notify('beforeOpen') === false)
            return false;
        //closeOnClick
        if(this.options.closeOnClick){
            if(this.options.closeOnClick === true)
                this.closeOnClickContainer = $(document.body);
            else if(this.options.closeOnClick == 'container')
                this.closeOnClickContainer = this.container;
            else if (this.options.closeOnClick == 'overlay'){
                Control.Overlay.load();
                this.closeOnClickContainer = Control.Overlay.container;
            }else
                this.closeOnClickContainer = $(this.options.closeOnClick);
            this.closeOnClickContainer.observe('click',this.closeHandler);
        }
        if(this.href && !this.options.iframe && !this.remoteContentLoaded){
            //link to image
            this.remoteContentLoaded = true;
            if(this.href.match(/\.(jpe?g|gif|png|tiff?)$/i)){
                var img = new Element('img');
                img.observe('load',function(img){
                    this.getRemoteContentInsertionTarget().insert(img);
                    this.position();
                    if(this.notify('onRemoteContentLoaded') !== false){
                        if(this.options.indicator)
                            this.hideIndicator();
                        this.finishOpen();
                    }
                }.bind(this,img));
                img.writeAttribute('src',this.href);
            }else{
                //if this is an ajax window it will only open if the request is successful
                if(!this.ajaxRequest){
                    if(this.options.indicator)
                        this.showIndicator();
                    this.ajaxRequest = new Ajax.Request(this.href,{
                        method: this.options.method,
                        parameters: this.options.parameters,
                        onComplete: function(request){
                            this.notify('onComplete',request);
                            this.ajaxRequest = false;
                        }.bind(this),
                        onSuccess: function(request){
                            this.getRemoteContentInsertionTarget().insert(request.responseText);
                            this.notify('onSuccess',request);
                            if(this.notify('onRemoteContentLoaded') !== false){
                                if(this.options.indicator)
                                    this.hideIndicator();
                                this.finishOpen();
                            }
                        }.bind(this),
                        onFailure: function(request){
                            this.notify('onFailure',request);
                            if(this.options.indicator)
                                this.hideIndicator();
                        }.bind(this),
                        onException: function(request,e){
                            this.notify('onException',request,e);
                            if(this.options.indicator)
                                this.hideIndicator();
                        }.bind(this)
                    });
                }
            }
            return true;
        }else if(this.options.iframe && !this.remoteContentLoaded){
            //iframe
            this.remoteContentLoaded = true;
            if(this.options.indicator)
                this.showIndicator();
            this.getRemoteContentInsertionTarget().insert(Control.Window.iframeTemplate.evaluate({
                href: this.href
            }));
            var iframe = this.container.down('iframe');
            iframe.onload = function(){
                this.notify('onRemoteContentLoaded');
                if(this.options.indicator)
                    this.hideIndicator();
                iframe.onload = null;
            }.bind(this);
        }
        this.finishOpen(event);
        return true
    },
    close: function(event){ //event may or may not be present
        if(!this.isOpen || this.notify('beforeClose',event) === false)
            return false;
        if(this.options.closeOnClick)
            this.closeOnClickContainer.stopObserving('click',this.closeHandler);
        if(this.options.fade){
            this.effects.fade = new Effect.Fade(this.container,{
                queue: {
                    position: 'front',
                    scope: 'Control.Window' + this.numberInSequence
                },
                from: 1,
                to: 0,
                duration: this.options.fadeDuration / 2,
                afterFinish: function(){
                    if(this.iFrameShim)
                        this.iFrameShim.hide();
                    this.isOpen = false;
                    this.notify('afterClose');
                }.bind(this)
            });
        }else{
            this.container.hide();
            if(this.iFrameShim)
                this.iFrameShim.hide();
        }
        if(this.ajaxRequest)
            this.ajaxRequest.transport.abort();
        if(!(this.options.draggable || this.options.resizable) && this.options.position == 'center')
            Event.stopObserving(window,'resize',this.positionHandler);
        if(!this.options.draggable && this.options.position == 'center')
            Event.stopObserving(window,'scroll',this.positionHandler);
        if(this.options.indicator)
            this.hideIndicator();
        if(!this.options.fade){
            this.isOpen = false;
            this.notify('afterClose');
        }
        return true;
    },
    position: function(event){
        //this is up top for performance reasons
        if(this.options.position == 'mouse'){
            var xy = [Event.pointerX(event),Event.pointerY(event)];
            this.container.setStyle({
                top: xy[1] + $value(this.options.offsetTop) + 'px',
                left: xy[0] + $value(this.options.offsetLeft) + 'px'
            });
            return;
        }
        var container_dimensions = this.container.getDimensions();
        var viewport_dimensions = document.viewport.getDimensions();
        Position.prepare();
        var offset_left = (Position.deltaX + Math.floor((viewport_dimensions.width - container_dimensions.width) / 2));
        var offset_top = (Position.deltaY + ((viewport_dimensions.height > container_dimensions.height) ? Math.floor((viewport_dimensions.height - container_dimensions.height) / 2) : 0));
        if(this.options.position == 'center' || this.options.position == 'center_once'){
            this.container.setStyle({
                top: (container_dimensions.height <= viewport_dimensions.height) ? ((offset_top != null && offset_top > 0) ? offset_top : 0) + 'px' : '100px',
                left: (container_dimensions.width <= viewport_dimensions.width) ? ((offset_left != null && offset_left > 0) ? offset_left : 0) + 'px' : 0
            });
        }else if(this.options.position == 'relative'){
            var xy = this.sourceContainer.cumulativeOffset();
            var top = xy[1] + $value(this.options.offsetTop);
            var left = xy[0] + $value(this.options.offsetLeft);
            this.container.setStyle({
                top: (container_dimensions.height <= viewport_dimensions.height) ? (this.options.constrainToViewport ? Math.max(0,Math.min(viewport_dimensions.height - (container_dimensions.height),top)) : top) + 'px' : 0,
                left: (container_dimensions.width <= viewport_dimensions.width) ? (this.options.constrainToViewport ? Math.max(0,Math.min(viewport_dimensions.width - (container_dimensions.width),left)) : left) + 'px' : 0
            });
        }else if(this.options.position.length){
            var top = $value(this.options.position[1]) + $value(this.options.offsetTop);
            var left = $value(this.options.position[0]) + $value(this.options.offsetLeft);
            this.container.setStyle({
                top: (container_dimensions.height <= viewport_dimensions.height) ? (this.options.constrainToViewport ? Math.max(0,Math.min(viewport_dimensions.height - (container_dimensions.height),top)) : top) + 'px' : 0,
                left: (container_dimensions.width <= viewport_dimensions.width) ? (this.options.constrainToViewport ? Math.max(0,Math.min(viewport_dimensions.width - (container_dimensions.width),left)) : left) + 'px' : 0
            });
        }
        if(this.iFrameShim)
            this.updateIFrameShimZIndex();
    },
    ensureInBounds: function(){
        if(!this.isOpen)
            return;
        var viewport_dimensions = document.viewport.getDimensions();
        var container_offset = this.container.cumulativeOffset();
        var container_dimensions = this.container.getDimensions();
        if(container_offset.left + container_dimensions.width > viewport_dimensions.width){
            this.container.setStyle({
                left: (Math.max(0,viewport_dimensions.width - container_dimensions.width)) + 'px'
            });
        }
        if(container_offset.top + container_dimensions.height > viewport_dimensions.height){
            this.container.setStyle({
                top: (Math.max(0,viewport_dimensions.height - container_dimensions.height)) + 'px'
            });
        }
    },
    bringToFront: function(){
        Control.Window.bringToFront(this);
        this.notify('bringToFront');
    },
    destroy: function(){
        this.container.stopObserving('mousedown',this.bringToFrontHandler);
        if(this.draggable){
            Draggables.removeObserver(this.container);
            this.draggable.handle.stopObserving('mousedown',this.bringToFrontHandler);
            this.draggable.destroy();
        }
        if(this.resizable){
            Resizables.removeObserver(this.container);
            this.resizable.handle.stopObserving('mousedown',this.bringToFrontHandler);
            this.resizable.destroy();
        }
        if(this.container && !this.sourceContainer)
            this.container.remove();
        if(this.sourceContainer){
            if(this.options.hover){
                this.sourceContainer.stopObserving('mouseenter',this.sourceContainerOpenHandler);
                this.sourceContainer.stopObserving('mouseleave',this.sourceContainerCloseHandler);
                if(this.options.position == 'mouse')
                    this.sourceContainer.stopObserving('mousemove',this.sourceContainerMouseMoveHandler);
            }else
                this.sourceContainer.stopObserving('click',this.sourceContainerOpenHandler);
        }
        if(this.iFrameShim)
            this.iFrameShim.destroy();
        Event.stopObserving(window,'resize',this.outOfBoundsPositionHandler);
        Control.Window.windows = Control.Window.windows.without(this);
        this.notify('afterDestroy');
    },
    //private
    applyResizable: function(){
        if(this.options.resizable){
            if(typeof(Resizable) == "undefined")
                throw "Control.Window requires resizable.js to be loaded.";
            var resizable_handle = null;
            if(this.options.resizable === true){
                resizable_handle = new Element('div',{
                    className: 'resizable_handle'
                });
                this.container.insert(resizable_handle);
            }else
                resizable_handle = $(this.options.resziable);
            this.resizable = new Resizable(this.container,{
                handle: resizable_handle,
                minHeight: this.options.minHeight,
                minWidth: this.options.minWidth,
                maxHeight: this.options.constrainToViewport ? function(element){
                    //viewport height - top - total border height
                    return (document.viewport.getDimensions().height - parseInt(element.style.top || 0)) - (element.getHeight() - parseInt(element.style.height || 0));
                } : this.options.maxHeight,
                maxWidth: this.options.constrainToViewport ? function(element){
                    //viewport width - left - total border width
                    return (document.viewport.getDimensions().width - parseInt(element.style.left || 0)) - (element.getWidth() - parseInt(element.style.width || 0));
                } : this.options.maxWidth
            });
            this.resizable.handle.observe('mousedown',this.bringToFrontHandler);
            Resizables.addObserver(new Control.Window.LayoutUpdateObserver(this,function(){
                if(this.iFrameShim)
                    this.updateIFrameShimZIndex();
                this.notify('onResize');
            }.bind(this)));
        }
    },
    applyDraggable: function(){
        if(this.options.draggable){
            if(typeof(Draggables) == "undefined")
                throw "Control.Window requires dragdrop.js to be loaded.";
            var draggable_handle = null;
            if(this.options.draggable === true){
                draggable_handle = new Element('div',{
                    className: 'draggable_handle'
                });
                this.container.insert(draggable_handle);
            }else
                draggable_handle = $(this.options.draggable);
            this.draggable = new Draggable(this.container,{
                handle: draggable_handle,
                constrainToViewport: this.options.constrainToViewport,
                zindex: this.container.getStyle('z-index'),
                starteffect: function(){
                    if(Prototype.Browser.IE){
                        this.old_onselectstart = document.onselectstart;
                        document.onselectstart = function(){
                            return false;
                        };
                    }
                }.bind(this),
                endeffect: function(){
                    document.onselectstart = this.old_onselectstart;
                }.bind(this)
            });
            this.draggable.handle.observe('mousedown',this.bringToFrontHandler);
            Draggables.addObserver(new Control.Window.LayoutUpdateObserver(this,function(){
                if(this.iFrameShim)
                    this.updateIFrameShimZIndex();
                this.notify('onDrag');
            }.bind(this)));
        }
    },
    createDefaultContainer: function(container){
        if(!this.container){
            //no container passed or found, create it
            this.container = new Element('div',{
                id: 'control_window_' + this.numberInSequence
            });
            $(document.body).insert(this.container);
            if(typeof(container) == "string" && $(container) == null && !container.match(/^#(.+)$/) && !container.match(Control.Window.uriRegex))
                this.container.update(container);
        }
    },
    finishOpen: function(event){
        this.bringToFront();
        if(this.options.fade){
            if(typeof(Effect) == "undefined")
                throw "Control.Window requires effects.js to be loaded."
            if(this.effects.fade)
                this.effects.fade.cancel();
            this.effects.appear = new Effect.Appear(this.container,{
                queue: {
                    position: 'end',
                    scope: 'Control.Window.' + this.numberInSequence
                },
                from: 0,
                to: 1,
                duration: this.options.fadeDuration / 2,
                afterFinish: function(){
                    if(this.iFrameShim)
                        this.updateIFrameShimZIndex();
                    this.isOpen = true;
                    this.notify('afterOpen');
                }.bind(this)
            });
        }else
            this.container.show();
        this.position(event);
        if(!(this.options.draggable || this.options.resizable) && this.options.position == 'center')
            Event.observe(window,'resize',this.positionHandler,false);
        if(!this.options.draggable && this.options.position == 'center')
            Event.observe(window,'scroll',this.positionHandler,false);
        if(!this.options.fade){
            this.isOpen = true;
            this.notify('afterOpen');
        }
        return true;
    },
    showIndicator: function(){
        this.showIndicatorTimeout = window.setTimeout(function(){
            if(this.options.fade){
                this.indicatorEffects.appear = new Effect.Appear(this.indicator,{
                    queue: {
                        position: 'front',
                        scope: 'Control.Window.indicator.' + this.numberInSequence
                    },
                    from: 0,
                    to: 1,
                    duration: this.options.fadeDuration / 2
                });
            }else
                this.indicator.show();
        }.bind(this),Control.Window.indicatorTimeout);
    },
    hideIndicator: function(){
        if(this.showIndicatorTimeout)
            window.clearTimeout(this.showIndicatorTimeout);
        this.indicator.hide();
    },
    getRemoteContentInsertionTarget: function(){
        return typeof(this.options.insertRemoteContentAt) == "string" ? this.container.down(this.options.insertRemoteContentAt) : $(this.options.insertRemoteContentAt);
    },
    updateIFrameShimZIndex: function(){
        if(this.iFrameShim)
            this.iFrameShim.positionUnder(this.container);
    }
});
//class methods
Object.extend(Control.Window,{
    windows: [],
    baseZIndex: 9999,
    indicatorTimeout: 250,
    iframeTemplate: new Template('<iframe src="#{href}" width="100%" height="100%" frameborder="0"></iframe>'),
    uriRegex: /^(\/|\#|https?\:\/\/|[\w]+\/)/,
    bringToFront: function(w){
        Control.Window.windows = Control.Window.windows.without(w);
        Control.Window.windows.push(w);
        Control.Window.windows.each(function(w,i){
            var z_index = Control.Window.baseZIndex + i;
            w.container.setStyle({
                zIndex: z_index
            });
            if(w.isOpen){
                if(w.iFrameShim)
                w.updateIFrameShimZIndex();
            }
            if(w.options.draggable)
                w.draggable.options.zindex = z_index;
        });
    },
    open: function(container,options){
        var w = new Control.Window(container,options);
        w.open();
        return w;
    },
    //protected
    initialZIndexForWindow: function(w){
        return Control.Window.baseZIndex + (Control.Window.windows.length - 1);
    }
});
Object.Event.extend(Control.Window);

//this is the observer for both Resizables and Draggables
Control.Window.LayoutUpdateObserver = Class.create({
    initialize: function(w,observer){
        this.w = w;
        this.element = $(w.container);
        this.observer = observer;
    },
    onStart: Prototype.emptyFunction,
    onEnd: function(event_name,instance){
        if(instance.element == this.element && this.iFrameShim)
            this.w.updateIFrameShimZIndex();
    },
    onResize: function(event_name,instance){
        if(instance.element == this.element)
            this.observer(this.element);
    },
    onDrag: function(event_name,instance){
        if(instance.element == this.element)
            this.observer(this.element);
    }
});

//overlay for Control.Modal
Control.Overlay = {
    id: 'control_overlay',
    loaded: false,
    container: false,
    lastOpacity: 0,
    getStyles: function() {
        return {
            position: 'fixed',
            top: 0,
            left: 0,
            width: '100%',
            height: '100%',
            zIndex: Control.Window.baseZIndex - 1
        };
    },
    getIeStyles: function() {
        return {
            position: 'absolute',
            top: 0,
            left: 0,
            zIndex: Control.Window.baseZIndex - 1
        };
    },
    effects: {
        fade: false,
        appear: false
    },
    load: function(){
        if(Control.Overlay.loaded)
            return false;
        Control.Overlay.loaded = true;
        Control.Overlay.container = new Element('div',{
            id: Control.Overlay.id
        });
        $(document.body).insert(Control.Overlay.container);
        if(Prototype.Browser.IE){
            Control.Overlay.container.setStyle(Control.Overlay.getIeStyles());
            Event.observe(window,'scroll',Control.Overlay.positionOverlay);
            Event.observe(window,'resize',Control.Overlay.positionOverlay);
            Control.Overlay.observe('beforeShow',Control.Overlay.positionOverlay);
        }else
            Control.Overlay.container.setStyle(Control.Overlay.getStyles());
        Control.Overlay.iFrameShim = new IframeShim();
        Control.Overlay.iFrameShim.hide();
        Event.observe(window,'resize',Control.Overlay.positionIFrameShim);
        Control.Overlay.container.hide();
        return true;
    },
    unload: function(){
        if(!Control.Overlay.loaded)
            return false;
        Event.stopObserving(window,'resize',Control.Overlay.positionOverlay);
        Control.Overlay.stopObserving('beforeShow',Control.Overlay.positionOverlay);
        Event.stopObserving(window,'resize',Control.Overlay.positionIFrameShim);
        Control.Overlay.iFrameShim.destroy();
        Control.Overlay.container.remove();
        Control.Overlay.loaded = false;
        return true;
    },
    show: function(opacity,fade){
        if(Control.Overlay.notify('beforeShow') === false)
            return false;
        Control.Overlay.lastOpacity = opacity;
        Control.Overlay.positionIFrameShim();
        Control.Overlay.iFrameShim.show();
        if(fade){
            if(typeof(Effect) == "undefined")
                throw "Control.Window requires effects.js to be loaded."
            if(Control.Overlay.effects.fade)
                Control.Overlay.effects.fade.cancel();
            Control.Overlay.effects.appear = new Effect.Appear(Control.Overlay.container,{
                queue: {
                    position: 'end',
                    scope: 'Control.Overlay'
                },
                afterFinish: function(){
                    Control.Overlay.notify('afterShow');
                },
                from: 0,
                to: Control.Overlay.lastOpacity,
                duration: (fade === true ? 0.75 : fade) / 2
            });
        }else{
            Control.Overlay.container.setStyle({
                opacity: opacity || 1
            });
            Control.Overlay.container.show();
            Control.Overlay.notify('afterShow');
        }
        return true;
    },
    hide: function(fade){
        if(Control.Overlay.notify('beforeHide') === false)
            return false;
        if(Control.Overlay.effects.appear)
            Control.Overlay.effects.appear.cancel();
        Control.Overlay.iFrameShim.hide();
        if(fade){
            Control.Overlay.effects.fade = new Effect.Fade(Control.Overlay.container,{
                queue: {
                    position: 'front',
                    scope: 'Control.Overlay'
                },
                afterFinish: function(){
                    Control.Overlay.notify('afterHide');
                },
                from: Control.Overlay.lastOpacity,
                to: 0,
                duration: (fade === true ? 0.75 : fade) / 2
            });
        }else{
            Control.Overlay.container.hide();
            Control.Overlay.notify('afterHide');
        }
        return true;
    },
    positionIFrameShim: function(){
        if(Control.Overlay.container.visible())
            Control.Overlay.iFrameShim.positionUnder(Control.Overlay.container);
    },
    //IE only
    positionOverlay: function(){
        Control.Overlay.container.setStyle({
            width: document.body.clientWidth + 'px',
            height: document.body.clientHeight + 'px'
        });
    }
};
Object.Event.extend(Control.Overlay);

Control.ToolTip = Class.create(Control.Window,{
    initialize: function($super,container,tooltip,options){
        $super(tooltip,Object.extend(Object.extend(Object.clone(Control.ToolTip.defaultOptions),options || {}),{
            position: 'mouse',
            hover: container
        }));
    }
});
Object.extend(Control.ToolTip,{
    defaultOptions: {
        offsetLeft: 10
    }
});

Control.Modal = Class.create(Control.Window,{
    initialize: function($super,container,options){
        Control.Modal.InstanceMethods.beforeInitialize.bind(this)();
        $super(container,Object.extend(Object.clone(Control.Modal.defaultOptions),options || {}));
    },
    closeWithoutOverlay: function(){
        this.keepOverlay = true;
        this.close();
    }
});
Object.extend(Control.Modal,{
    defaultOptions: {
        overlayOpacity: 0.5,
        closeOnClick: 'overlay'
    },
    current: false,
    open: function(container,options){
        var modal = new Control.Modal(container,options);
        modal.open();
        return modal;
    },
    close: function(){
        if(Control.Modal.current)
            Control.Modal.current.close();
    },
    InstanceMethods: {
        beforeInitialize: function(){
            Control.Overlay.load();
            this.observe('beforeOpen',Control.Modal.Observers.beforeOpen.bind(this));
            this.observe('afterOpen',Control.Modal.Observers.afterOpen.bind(this));
            this.observe('afterClose',Control.Modal.Observers.afterClose.bind(this));
        }
    },
    Observers: {
        beforeOpen: function(){
            Control.Window.windows.without(this).each(function(w){
                if(w.closeWithoutOverlay && w.isOpen){
                    w.closeWithoutOverlay();
                }else{
                    w.close();
                }
            });
            if(!Control.Overlay.overlayFinishedOpening){
                Control.Overlay.observeOnce('afterShow',function(){
                    Control.Overlay.overlayFinishedOpening = true;
                    this.open();
                }.bind(this));
                Control.Overlay.show(this.options.overlayOpacity,this.options.fade ? this.options.fadeDuration : false);
                throw $break;
            }
        },
        afterOpen: function(){
            Control.Overlay.show(this.options.overlayOpacity);
            Control.Overlay.overlayFinishedOpening = true;
            Control.Modal.current = this;
        },
        afterClose: function(){
            if(!this.keepOverlay){
                Control.Overlay.hide(this.options.fade ? this.options.fadeDuration : false);
                Control.Overlay.overlayFinishedOpening = false;
            }
            this.keepOverlay = false;
            Control.Modal.current = false;
        }
    }
});

Control.LightBox = Class.create(Control.Window,{
    initialize: function($super,container,options){
        this.allImagesLoaded = false;
        if(options.modal){
            var options = Object.extend(Object.clone(Control.LightBox.defaultOptions),options || {});
            options = Object.extend(Object.clone(Control.Modal.defaultOptions),options);
            options = Control.Modal.InstanceMethods.beforeInitialize.bind(this)(options);
            $super(container,options);
        }else
            $super(container,Object.extend(Object.clone(Control.LightBox.defaultOptions),options || {}));
        this.hasRemoteContent = this.href && !this.options.iframe;
        if(this.hasRemoteContent)
            this.observe('onRemoteContentLoaded',Control.LightBox.Observers.onRemoteContentLoaded.bind(this));
        else
            this.applyImageObservers();
        this.observe('beforeOpen',Control.LightBox.Observers.beforeOpen.bind(this));
    },
    applyImageObservers:function(){
        var images = this.getImages();
        this.numberImagesToLoad = images.length;
        this.numberofImagesLoaded = 0;
        images.each(function(image){
            image.observe('load',function(image){
                ++this.numberofImagesLoaded;
                if(this.numberImagesToLoad == this.numberofImagesLoaded){
                    this.allImagesLoaded = true;
                    this.onAllImagesLoaded();
                }
            }.bind(this,image));
            image.hide();
        }.bind(this));
    },
    onAllImagesLoaded: function(){
        this.getImages().each(function(image){
            this.showImage(image);
        }.bind(this));
        if(this.hasRemoteContent){
            if(this.options.indicator)
                this.hideIndicator();
            this.finishOpen();
        }else
            this.open();
    },
    getImages: function(){
        return this.container.select(Control.LightBox.imageSelector);
    },
    showImage: function(image){
        image.show();
    }
});
Object.extend(Control.LightBox,{
    imageSelector: 'img',
    defaultOptions: {},
    Observers: {
        beforeOpen: function(){
            if(!this.hasRemoteContent && !this.allImagesLoaded)
                throw $break;
        },
        onRemoteContentLoaded: function(){
            this.applyImageObservers();
            if(!this.allImagesLoaded)
                throw $break;
        }
    }
});

/*
	Update billing and shipping address when filled postcode
*/
function updateRegion(countryEl, regionTextEl, regionSelectEl, regions, disableAction, zipEl)
	{
		if (regions[countryEl.value]) {
			var i, option, region, def;

			def = regionSelectEl.getAttribute('defaultValue');
			if (regionTextEl) {
				if (!def) {
					def = regionTextEl.value.toLowerCase();
				}
				regionTextEl.value = '';
			}

			regionSelectEl.options.length = 1;
			for (regionId in regions[countryEl.value]) {
				region = regions[countryEl.value][regionId];

				option = document.createElement('OPTION');
				option.value = regionId;
				option.text = region.name.stripTags();
				option.title = region.name;

				if (regionSelectEl.options.add) {
					regionSelectEl.options.add(option);
				} else {
					regionSelectEl.appendChild(option);
				}

				if (regionId==def || (region.name && region.name.toLowerCase()==def) ||
					(region.name && region.code.toLowerCase()==def)
				) {
					regionSelectEl.value = regionId;
				}
			}

			if (disableAction=='hide') {
				if (regionTextEl) {
					regionTextEl.style.display = 'none';
				}

				regionSelectEl.style.display = '';
			} else if (disableAction=='disable') {
				if (regionTextEl) {
					regionTextEl.disabled = true;
				}
				regionSelectEl.disabled = false;
			}
			setMarkDisplay(regionSelectEl, true);
		} else {
			if (disableAction=='hide') {
				if (regionTextEl) {
					regionTextEl.style.display = '';
				}
				regionSelectEl.style.display = 'none';
				Validation.reset(regionSelectEl);
			} else if (disableAction=='disable') {
				if (regionTextEl) {
					regionTextEl.disabled = false;
				}
				regionSelectEl.disabled = true;
			} else if (disableAction=='nullify') {
				regionSelectEl.options.length = 1;
				regionSelectEl.value = '';
				regionSelectEl.selectedIndex = 0;
				lastCountryId = '';
			}
			setMarkDisplay(regionSelectEl, false);
		}

		_checkRegionRequired(countryEl, regionTextEl, regionSelectEl, regions['config']);
		// Make Zip and its label required/optional
		var zipUpdater = new ZipUpdater(countryEl.value, zipEl);
		zipUpdater.update();
	}
	
	function setMarkDisplay(elem, display)
	{
		elem = $(elem);
		var labelElement = elem.up(0).down('label > span.required') ||
						   elem.up(1).down('label > span.required') ||
						   elem.up(0).down('label.required > em') ||
						   elem.up(1).down('label.required > em');
		if(labelElement) {
			inputElement = labelElement.up().next('input');
			if (display) {
				labelElement.show();
				if (inputElement) {
					inputElement.addClassName('required-entry');
				}
			} else {
				labelElement.hide();
				if (inputElement) {
					inputElement.removeClassName('required-entry');
				}
			}
		}
	}
	
	function _checkRegionRequired(countryEl, regionTextEl, regionSelectEl, config)
	{
		var label, wildCard;
		var elements = [regionTextEl, regionSelectEl];
		var that = this;
		if (typeof config == 'undefined') {
			return;
		}
		var regionRequired = config.regions_required.indexOf(countryEl.value) >= 0;

		elements.each(function(currentElement) {
			Validation.reset(currentElement);
			label = $$('label[for="' + currentElement.id + '"]')[0];
			if (label) {
				wildCard = label.down('em') || label.down('span.required');
				if (!that.config.show_all_regions) {
					if (regionRequired) {
						label.up().show();
					} else {
						label.up().hide();
					}
				}
			}

			if (label && wildCard) {
				if (!regionRequired) {
					wildCard.hide();
					if (label.hasClassName('required')) {
						label.removeClassName('required');
					}
				} else if (regionRequired) {
					wildCard.show();
					if (!label.hasClassName('required')) {
						label.addClassName('required')
					}
				}
			}

			if (!regionRequired) {
				if (currentElement.hasClassName('required-entry')) {
					currentElement.removeClassName('required-entry');
				}
				if ('select' == currentElement.tagName.toLowerCase() &&
					currentElement.hasClassName('validate-select')) {
					currentElement.removeClassName('validate-select');
				}
			} else {
				if (!currentElement.hasClassName('required-entry')) {
					currentElement.addClassName('required-entry');
				}
				if ('select' == currentElement.tagName.toLowerCase() &&
					!currentElement.hasClassName('validate-select')) {
					currentElement.addClassName('validate-select');
				}
			}
		});
	}	
/**********Js For Ajaxcart In Cart Page**********/

var Ajaxcartpage = Class.create();
Ajaxcartpage.prototype = {
	initialize: function(ajaxMask,ajaxPopup,popupContent,cartPage,links,instanceName){
		this.ajaxMask = ajaxMask;
		this.ajaxPopup = ajaxPopup;
		this.popupContent = popupContent;
		this.cartPage = cartPage;
		this.links = links;
		this.instanceName = instanceName;
		
		this.jsSource = [];
		this.jsCache = [];
		this.jsCount = 0;		
		this.intervalCache = 0;
		
		this.ajaxOnComplete = this.ajaxOnComplete.bindAsEventListener(this);
		this.addJsSource = this.addJsSource.bindAsEventListener(this);
		this.updateJscartEvent = this.updateJscartEvent.bindAsEventListener(this);
	},
	getCartPage: function(){
		if (!this.objCartPage){
			if ($$(this.cartPage).first()){
				this.objCartPage = $$(this.cartPage).first();
			}
		}
		return this.objCartPage;
	},
	addToCartHandle: function(requestUrl, params){
		this.url = requestUrl;
		if(window.location.href.match('https://') && !requestUrl.match('https://'))
			requestUrl = requestUrl.replace('http://','https://');
		if(!window.location.href.match('https://') && requestUrl.match('https://'))
			requestUrl = requestUrl.replace('https://','http://');
		if (requestUrl.indexOf('?') != -1)
			requestUrl += '&isajaxcart=true';
		else
			requestUrl += '?isajaxcart=true';
		if (this.getCartPage())
			requestUrl += '&isajaxcartpage=1'
		if (this.links)
			requestUrl += '&ajaxlinks=1';
		
		$(this.ajaxMask).show();
		new Ajax.Request(requestUrl,{
			method: 'post',
			postBody: params,
			parameters: params,
			onException: function (xhr, e){
				if(this.ajaxMask)
					$(this.ajaxMask).hide();
				if(this.ajaxPopup)
					$(this.ajaxPopup).hide();
				window.location.href = this.url;
			},
			onComplete: this.ajaxOnComplete
		});
	},
	ajaxOnComplete: function(xhr){
		if (xhr.responseText.isJSON()){
			var response = xhr.responseText.evalJSON();
			if (response.hasOptions) {
				if (response.redirectUrl) this.addToCartHandle(response.redirectUrl,'');
				else this.popupContentWindow(response);
			} else {
				this.addToCartFinish(response);
			}
		} else {
			$(this.ajaxMask).hide();
			$(this.ajaxPopup).hide();
			window.location.href = this.url;
		}
	},
	addToCartFinish: function(response){
		if (this.getCartPage() && response.cartPage){
			if (response.emptyCart){
				this.getCartPage().update(response.cartPage);
			} else {
				$(this.popupContent).innerHTML = response.cartPage;
				ajaxcartUpdateCartHtml(this.getCartPage(),$(this.popupContent));
				$(this.popupContent).innerHTML = '';
				this.updateJscartEvent();
			}
		}
		if (this.links && response.ajaxlinks){
			this.links.update(response.ajaxlinks);
			this.links.innerHTML = this.links.firstChild.innerHTML;
		}
		$(this.ajaxMask).hide();
		$(this.ajaxPopup).hide();
		save_address_information(save_address_url);
		if($('giftwrap_price'))
			reloadGiftwrap();
	},
	popupContentWindow: function(response){
		if (response.optionjs){
			for (var i=0;i<response.optionjs.length;i++){
				var pattern = 'script[src="'+response.optionjs[i]+'"]';
				if ($$(pattern).first()) continue;
				this.jsSource[this.jsSource.length] = response.optionjs[i];
			}
		}
		if (response.optionhtml){
			$(this.popupContent).innerHTML = response.optionhtml;
			this.jsCache = response.optionhtml.extractScripts();
		}
		this.intervalCache = setInterval(this.addJsSource,500);
	},
	addJsSource: function(){
		if (this.jsCount == this.jsSource.length){
			this.jsSource = [];
			this.jsCount = 0;
			clearInterval(this.intervalCache);
			this.addJsScript();
		} else {
			var headDoc = $$('head').first();
			var jsElement = new Element('script');
			jsElement.src = this.jsSource[this.jsCount];
			headDoc.appendChild(jsElement);
			this.jsCount++;
		}
	},
	addJsScript: function(){
		if (this.jsCache.length == 0) return false;
		try {
			for (var i=0;i<this.jsCache.length;i++){
				var script = this.jsCache[i];
				var headDoc = $$('head').first();
				var jsElement = new Element('script');
				jsElement.type = 'text/javascript';
				jsElement.text = script;
				headDoc.appendChild(jsElement);
			}
			this.jsCache = [];
			$(this.ajaxMask).hide();
			$(this.ajaxPopup).show();
			var content = $(this.popupContent);
			content.style.removeProperty('top');
			if (content.offsetHeight + content.offsetTop > window.innerHeight){
				content.style.position = 'absolute';
				content.style.top = window.pageYOffset+'px';
			}else{
				content.style.position = 'fixed';
			}
			ajaxMoreTemplateJs();
		} catch (e){}
	},
	updateJscartEvent: function(){
		var instanceName = this.instanceName;
		$$('a').each(function(el){
			if (el.href.search('/checkout/cart/delete/') != -1
				|| el.href.search('/checkout/cart/configure/') != -1)
					el.href = "javascript:"+instanceName+".addToCartHandle('"+el.href+"','')";
		});
		ajaxUpdateFormAction();
	}
}
notifyThreedError = function(msg){
	Control.Window.windows.each(function(w){
		if(w.container.visible()){
			w.close();
		}
	});
	// if((typeof checkout.accordion == 'object')){
		// checkout.accordion.openSection('opc-payment');
	// }
	alert(msg);
	var loading_osc = $$('div#onestepcheckout-place-order-loading');
	if(loading_osc.length){
		$('onestepcheckout-button-place-order').removeClassName('place-order-loader').addClassName('btn-checkout');
		$('onestepcheckout-button-place-order').removeAttribute('disabled');
		loading_osc.invoke('hide');
		already_placing_order = false;
	}
}

restoreOscLoad = function(){
		//Hide loading indicator on OSC//
			var loading_osc = $$('div.onestepcheckout-place-order-loading');
			if(loading_osc.length){
				$('onestepcheckout-place-order').removeClassName('grey').addClassName('orange');
				$('onestepcheckout-place-order').removeAttribute('disabled');
				loading_osc.invoke('hide');
				already_placing_order = false;
			}else{
				var loading_osc = $$('div#onestepcheckout-place-order-loading');
				if(loading_osc.length){
					$('onestepcheckout-button-place-order').removeClassName('place-order-loader').addClassName('btn-checkout');
					$('onestepcheckout-button-place-order').removeAttribute('disabled');
					loading_osc.invoke('hide');
					already_placing_order = false;
				}
			}
}

if(typeof EbizmartsSagePaySuite == 'undefined') {
    var EbizmartsSagePaySuite = {};
}
EbizmartsSagePaySuite.Checkout = Class.create();
EbizmartsSagePaySuite.Checkout.prototype = {

	initialize: function(config){
		this.config 		= config;
		this.servercode			= 'sagepayserver';
		this.directcode			= 'sagepaydirectpro';
		this.paypalcode			= 'sagepaypaypal';
		this.formcode			= 'sagepayform';
		this.code               = '';
		this.oldUrl             = '';

		if(this.getConfig('review')){
			this.oldUrl = this.getConfig('review').saveUrl;
			this.getConfig('review').saveUrl = SuiteConfig.getConfig('global', 'sgps_saveorder_url');
			this.getConfig('review').onSave = this.reviewSave.bindAsEventListener(this);
		}else if(this.getConfig('osc')){

		Event.stopObserving($('onestepcheckout-form'));
		$('onestepcheckout-form')._submit = $('onestepcheckout-form').submit;
		$('onestepcheckout-form').submit = function(){ this.reviewSave(); }.bind(this);

		}else if(this.getConfig('magestore')){
			Event.stopObserving($('one-step-checkout-form'));
			$('one-step-checkout-form')._submit = $('one-step-checkout-form').submit;
			$('one-step-checkout-form').submit = function(){ this.reviewSave(); }.bind(this);
		}else if(this.getConfig('msform')){
			this.getConfig('msform').observe('submit', function(evmsc){Event.stop(evmsc);this.reviewSave(evmsc);}.bind(this));
		}

		var blFormMAC = $('multishipping-billing-form');
		if(blFormMAC){
			blFormMAC.observe('submit', function(_event_){ Event.stop(_event_); this.setPaymentMethod(); }.bind(this));
		}

		var paymentSubmit = this.getPaymentSubmit();
		if(paymentSubmit) {

			 if(this.getCurrentCheckoutStep() == 'opc-review'){//Magento 1.5.x+
			 	this.setPaymentMethod(true);
			 }else{
			 	paymentSubmit.observe('click', this.setPaymentMethod.bindAsEventListener(this));
			 }

		}

	},
	evalTransport: function(transport){
		try { response = eval('('+transport.responseText+')') } catch(e) { response = {} }
                return response;
	},
	getConfig: function(instance){
		return (this.config[instance] != 'undefined' ? this.config[instance] : false);
	},
	getCurrentCheckoutStep: function(){
		return this.getConfig('checkout').accordion.currentSection;
	},
	getPaymentSubmit: function(){
		var elements 	= $$("#opc-payment [onclick]");
		 for(var i=0; i<elements.length; i++) {
		 	// IE problems with readAttribute/getAttribute returning invalid results
		 	var attrubutes = [elements[i].readAttribute('onclick'), elements[i].getAttribute('onclick')];
		 	for(var j=0; j<attrubutes.length; j++) {
		 		if(Object.isString(attrubutes[j]) && -1 !== attrubutes[j].search(/payment\.save/)) {
					return elements[i];
				}
		 	}
        }
        return false;
	},
	getShippingMethodSubmit: function(){
		var elements 	= $$("#opc-shipping_method [onclick]");
		 for(var i=0; i<elements.length; i++) {
		 	var attrubutes = [elements[i].readAttribute('onclick'), elements[i].getAttribute('onclick')];
		 	for(var j=0; j<attrubutes.length; j++) {
		 		if(Object.isString(attrubutes[j]) && -1 !== attrubutes[j].search(/shippingMethod\.save/)) {
					return elements[i];
				}
		 	}
        }
        return false;
	},
	getPaymentMethod: function(){

		var form = null;

		if($('multishipping-billing-form')){
			form = $('multishipping-billing-form');
		}else if(this.getConfig('osc')){
			form = this.getConfig('oscFrm');
		}else if(this.getConfig('magestore')){
			form = this.getConfig('magestoreFrm');
		}else if((typeof this.getConfig('payment')) != 'undefined'){
			form = $(this.getConfig('payment').form);
		}

		if(form === null){
			return this.code;
		}

		var checkedPayment = null

		form.getInputs('radio', 'payment[method]').each(function(el){
			if(el.checked){
				checkedPayment = el.value;
				throw $break;
			}
		});

		if(checkedPayment != null){
			return checkedPayment;
		}

        return this.code;
	},
	isFormPaymentMethod: function(){
		return (this.getPaymentMethod() === this.formcode);
	},
	isServerPaymentMethod: function(){
		return (this.getPaymentMethod() === this.servercode || ($('suite_ms_payment_method') && $('suite_ms_payment_method').getValue()==this.servercode));
	},
	isDirectPaymentMethod: function(){
		return (this.getPaymentMethod() === this.directcode);
	},
	isSagePay: function(){
		var isSagePay = false;
		if( (this.getPaymentMethod() === this.formcode) || (this.getPaymentMethod() === this.directcode) ||
   		    (this.getPaymentMethod() === this.servercode) || (this.getPaymentMethod() === this.paypalcode) ) {
   		    isSagePay = true;
   		}

   		return isSagePay;
	},
	growlError: function(msg){
		alert(msg);
		return;
		try{
			var ng = new k.Growler({location:"tc"});
			ng.error(msg, {life:10});
		}catch(grwlerror){
			alert(msg);
		}
	},
	growlWarn: function(msg){
		alert(msg);
		return;
		try{
			var ng = new k.Growler({location:"tc"});
			ng.warn(msg, {life:10});
		}catch(grwlerror){
			alert(msg);
		}
	},
	isDirectTokenTransaction: function(){
		var tokenRadios = $$('div#payment_form_sagepaydirectpro ul.tokensage li.tokencard-radio input');
		if(tokenRadios.length){
			if(tokenRadios[0].disabled === false){
				return true;
			}
		}
		return false;
	},
	isServerTokenTransaction: function(){
		var tokenRadios = $$('div#payment_form_sagepayserver ul.tokensage li.tokencard-radio input');
		if(tokenRadios.length){
			if(tokenRadios[0].disabled === false){
				return true;
			}
		}
		return false;
	},
	getServerSecuredImage: function(){
		return new Element('img', {'src':SuiteConfig.getConfig('server', 'secured_by_image'), 'style':'margin-bottom:5px'});
	},
	setShippingMethod: function(){
		try{
				if($('sagepaydirectpro_cc_type')){
					$('sagepaydirectpro_cc_type').selectedIndex = 0;
				}
		}catch(ser){
			alert(ser);
		}
	},
	setPaymentMethod: function(modcompat){

		if(this.getConfig('review')){
			if(!this.isSagePay()) {
				this.getConfig('review').saveUrl = this.oldUrl;
			}
			else{
				this.getConfig('review').saveUrl = SuiteConfig.getConfig('global', 'sgps_saveorder_url');
			}
		}

		// Remove Server InCheckout iFrame if exists
		if($('sagepaysuite-server-incheckout-iframe')){
			$('checkout-review-submit').show();
			$('sagepaysuite-server-incheckout-iframe').remove();
		}

		if(this.isServerPaymentMethod()){

			if( parseInt(SuiteConfig.getConfig('global','token_enabled')) === 1 && ($('remembertoken-sagepayserver').checked === true) ){

			$('sagepayserver-dummy-link').writeAttribute('href', SuiteConfig.getConfig('server','new_token_url'));
				if(this.isServerTokenTransaction()){

					if($('multishipping-billing-form')){
						$('multishipping-billing-form').submit();
					}

					return;
				}
				 var lcontwmt = new Element('div',{className: 'lcontainer'});
				 var heit = parseInt(SuiteConfig.getConfig('server','token_iframe_height'))+80;
				 lcontwmt.setStyle({'height':heit.toString() + 'px'});

				 var wmt = new Control.Modal('sagepayserver-dummy-link',{
						     className: 'modal',
						     iframe: true,
						     closeOnClick: false,
						     insertRemoteContentAt: lcontwmt,
						     height: SuiteConfig.getConfig('server','token_iframe_height'),
						     width: SuiteConfig.getConfig('server','token_iframe_width'),
						     fade: true,
						     afterClose: function(){
						     	this.getTokensHtml();
				 			 }.bind(this)
				 })
				wmt.container.insert(lcontwmt);

				wmt.container.down().insert(this.getServerSecuredImage());
				wmt.container.setStyle({'height':heit.toString() + 'px'});
			 	wmt.open();

				if(this.getConfig('checkout') && (modcompat == undefined)){
					this.getConfig('checkout').accordion.openSection('opc-payment');
				}
				return;
			}

		}else if(this.isDirectPaymentMethod() && parseInt(SuiteConfig.getConfig('global','token_enabled')) === 1 && ($('remembertoken-sagepaydirectpro').checked === true)){

			if(this.isDirectTokenTransaction()){
				return;
			}

			try{
	            if(new Validation(this.getConfig('payment').form).validate() === false){
	                return;
	            }
            }catch(one){}

			if(this.getConfig('osc')){
				var valOsc = new VarienForm('onestepcheckout-form').validator.validate();
				if(!valOsc){
					return;
				}
			}else if(this.getConfig('magestore')){
				var valOsc = new VarienForm('one-step-checkout-form').validator.validate();
				if(!valOsc){
					return;
				}
			}

			var pmntForm = (this.getConfig('osc') ? this.getConfig('oscFrm') : $('co-payment-form'));
			if(this.getConfig('magestore')){
				pmntForm = this.getConfig('magestoreFrm');
			}
			new Ajax.Request(SuiteConfig.getConfig('direct','sgps_registerdtoken_url'),{
					method:"post",
					parameters: Form.serialize(pmntForm),
					onSuccess:function(f){

						try{
							this.getTokensHtml();

							var d=f.responseText.evalJSON();

							if(d.response_status=="INVALID"||d.response_status=="MALFORMED"||d.response_status=="ERROR"||d.response_status=="FAIL"){
								if(this.getConfig('checkout')){
									this.getConfig('checkout').accordion.openSection('opc-payment');
								}
								this.growlWarn("An error ocurred with Sage Pay Direct:\n" + d.response_status_detail.toString());

								if(this.getConfig('osc')){
									$('onestepcheckout-place-order').removeClassName('grey').addClassName('orange');
									$$('div.onestepcheckout-place-order-loading').invoke('remove');
									return;
								}else if(this.getConfig('magestore')){
									$('onestepcheckout-button-place-order').addClassName('btn-checkout');
									$('onestepcheckout-button-place-order').removeClassName('place-order-loader');
								}

							}else if(d.response_status == 'threed'){
								$('sagepaydirectpro-dummy-link').writeAttribute('href', d.url);
							}

							if(this.getConfig('osc')){
								this.reviewSave({'tokenSuccess':true});
								return;
							}else if(this.getConfig('magestore')){
								this.reviewSave({'tokenSuccess':true});
								return;
							}

						}catch(alfaEr){

							if(this.getConfig('checkout')){
								this.getConfig('checkout').accordion.openSection('opc-payment');
							}
							this.growlError(f.responseText.toString());
						}

					}.bind(this)
			});

		}

	},
	getTokensHtml: function(){

		new Ajax.Updater(('tokencards-payment-' + this.getPaymentMethod()), SuiteConfig.getConfig('global', 'html_paymentmethods_url'), {
						 parameters: { payment_method: this.getPaymentMethod() },
						 onComplete:function(){
                                               if($$('a.addnew').length > 1){
                                                   $$('a.addnew').each(function(el){
                                                      if(!el.visible()){
                                                          el.remove();
                                                      }
                                                   })
                                               }
                                               toggleNewCard(2);

										     	if($('onestepcheckout-form') && this.isServerPaymentMethod()){
											     	toggleNewCard(1);

											     	var tokens = $$('div#payment_form_sagepayserver ul li.tokencard-radio input');
											     	if(tokens.length){
												     	tokens.each(function(radiob){
															radiob.disabled = true;
															radiob.removeAttribute('checked');
														});
														tokens.first().writeAttribute('checked', 'checked');
														tokens.first().disabled = false;
														$('onestepcheckout-form').submit();
													}else{
														this.resetOscLoading();
													}

										     	}
						 }.bind(this)
		});

	},
	resetOscLoading: function(){
		restoreOscLoad();
	},
	reviewSave: function(transport){
			if((typeof transport) == 'undefined'){
				var transport = {};
			}

			//OSC\\
			
			if((typeof transport.responseText) == 'undefined' && ($('onestepcheckout-form')||$('one-step-checkout-form'))){
				if(!this.isSagePay()){
					if(this.getConfig('osc')){
						$('onestepcheckout-form')._submit();
					}else if(this.getConfig('magestore')){
						$('one-step-checkout-form')._submit();
					}
					return;
				}
				if(this.getConfig('osc')){
					if(this.isFormPaymentMethod()){
						new Ajax.Request(SuiteConfig.getConfig('global', 'sgps_saveorder_url'),{
								method:"post",
								parameters: Form.serialize($('onestepcheckout-form')),
								onSuccess:function(f){
									var d = f.responseText.evalJSON();
									if(d.response_status == 'ERROR'){
										alert(d.response_status_detail);
										this.resetOscLoading();
										return;
									}

									setLocation(SuiteConfig.getConfig('form','url'));
								}
						});
						return;
					}
				}else if(this.getConfig('magestore')){
					if(this.isFormPaymentMethod()){
						disable_payment();
						new Ajax.Request(SuiteConfig.getConfig('global', 'sgps_saveorder_url'),{
								method:"post",
								parameters: Form.serialize($('one-step-checkout-form')),
								onSuccess:function(f){
									var d = f.responseText.evalJSON();
									if(d.response_status == 'ERROR'){
										alert(d.response_status_detail);
										this.resetOscLoading();
										return;
									}

									setLocation(SuiteConfig.getConfig('form','url'));
								}
						});
						return;
					}

				}
				if((this.isDirectPaymentMethod() || this.isServerPaymentMethod()) && parseInt(SuiteConfig.getConfig('global','token_enabled')) === 1){
					if((typeof transport.tokenSuccess) == 'undefined'){
						this.setPaymentMethod();

						if(!this.isDirectTokenTransaction() && !this.isServerTokenTransaction() && (($('remembertoken-sagepaydirectpro') && $('remembertoken-sagepaydirectpro').checked === true) || ($('remembertoken-sagepayserver') && $('remembertoken-sagepayserver').checked === true))){
							return;
						}
					}
				}
				
				if(this.getConfig('osc')){
					if(parseInt($$('div.onestepcheckout-place-order-loading').length) || (typeof transport.tokenSuccess != 'undefined' && true === transport.tokenSuccess)){

						if(Ajax.activeRequestCount > 1 && (typeof transport.tokenSuccess) == 'undefined'){
							return;
						}
						var slPayM = this.getPaymentMethod();


						if(slPayM == this.servercode || slPayM == this.directcode){
							new Ajax.Request(SuiteConfig.getConfig('global', 'sgps_saveorder_url'),{
									method:"post",
									parameters: Form.serialize($('onestepcheckout-form')),
									onSuccess:function(f){
										this.reviewSave(f);
										transport.element().removeClassName('grey').addClassName('orange');
										$$('div.onestepcheckout-place-order-loading').invoke('hide');
									}.bind(this)
							});
							return;
						}else{
						   $('onestepcheckout-form')._submit();
						   return;
						}

					  }else{
						return;
					  }
					}else if(this.getConfig('magestore')){
						if(parseInt($$('div#onestepcheckout-place-order-loading').length) || (typeof transport.tokenSuccess != 'undefined' && true === transport.tokenSuccess)){

						if(Ajax.activeRequestCount > 1 && (typeof transport.tokenSuccess) == 'undefined'){
							return;
						}
						var slPayM = this.getPaymentMethod();


						if(slPayM == this.servercode || slPayM == this.directcode){
							disable_payment();
							new Ajax.Request(SuiteConfig.getConfig('global', 'sgps_saveorder_url'),{
									method:"post",
									parameters: Form.serialize($('one-step-checkout-form')),
									onSuccess:function(f){
										this.reviewSave(f);
										transport.element().removeClassName('place-order-loader').addClassName('btn-checkout');
										$$('div#onestepcheckout-place-order-loading').invoke('hide');
									}.bind(this)
							});
							return;
						}else{
						   $('one-step-checkout-form')._submit();
						   return;
						}

					  }else{
						return;
					  }
					}
			//OSC\\
			
			}else if((typeof transport.responseText) == 'undefined' && this.getConfig('msform')){
					var ps = $H({'payment[method]': 'sagepayserver'});

					if($('sagepay_server_token_cc_id')){
						ps.set('payment[sagepay_token_cc_id]', $('sagepay_server_token_cc_id').getValue());
					}

					new Ajax.Request(SuiteConfig.getConfig('global', 'sgps_saveorder_url'),{
							method:"post",
							parameters: ps,
							onSuccess:function(f){
								this.reviewSave(f);
							}.bind(this)
					});
					return;
		}else{
			try{
				var response = this.evalTransport(transport);
			}catch(notv){
				suiteLogError(notv);
			}
		}

		if((typeof response.response_status != 'undefined') && response.response_status != 'OK' && response.response_status != 'threed' && response.response_status != 'paypal_redirect'){

			this.resetOscLoading();

			this.growlWarn("An error ocurred with Sage Pay:\n" + response.response_status_detail.toString());
			return;
		}

		if(response.response_status == 'paypal_redirect'){
			setLocation(response.redirect);
			return;
		}

		if(this.getConfig('osc') && response.success && response.response_status == 'OK' && (typeof response.next_url == 'undefined')){
			setLocation(SuiteConfig.getConfig('global','onepage_success_url'));
			return;
		}else if(this.getConfig('magestore') && response.success && response.response_status == 'OK' && (typeof response.next_url == 'undefined')){
			setLocation(SuiteConfig.getConfig('global','onepage_success_url'));
			return;
		}

		if(!response.redirect || !response.success) {
			this.getConfig('review').nextStep(transport);
			return;
		}

		if(this.isServerPaymentMethod()){

			$('sagepayserver-dummy-link').writeAttribute('href', response.redirect);

			 var rbButtons = $('review-buttons-container');

			 var lcont = new Element('div',{className: 'lcontainer'});
			 var heit = parseInt(SuiteConfig.getConfig('server','iframe_height'));
			 if(Prototype.Browser.IE){
			 	heit = heit-65;
			 }

			var wtype = SuiteConfig.getConfig('server','payment_iframe_position').toString();
			if(wtype == 'modal'){

				 var wm = new Control.Modal('sagepayserver-dummy-link',{
						     className: 'modal',
						     iframe: true,
						     closeOnClick: false,
						     insertRemoteContentAt: lcont,
						     height: SuiteConfig.getConfig('server','iframe_height'),
						     width: SuiteConfig.getConfig('server','iframe_width'),
						     fade: true,
						     afterOpen: function(){
								if(rbButtons){
									rbButtons.addClassName('disabled');
								}
				 			 },
						     afterClose: function(){
						     	if(rbButtons){
						     		rbButtons.removeClassName('disabled');
						     	}
				 			 }
				 });
			 	wm.container.insert(lcont);
				wm.container.down().setStyle({'height':heit.toString() + 'px'});
				wm.container.down().insert(this.getServerSecuredImage());
				wm.open();

			}else if(wtype == 'incheckout'){

				var iframeId = 'sagepaysuite-server-incheckout-iframe';
				var paymentIframe = new Element('iframe', {'src': response.redirect, 'id': iframeId});

				if(this.getConfig('osc')){
					var placeBtn = $('onestepcheckout-place-order');

					placeBtn.hide();

					$('onestepcheckout-form').insert( { after:paymentIframe } );
					$(iframeId).scrollTo();

				}else if(this.getConfig('magestore')){
					var placeBtn = $('onestepcheckout-button-place-order');

					placeBtn.hide();

					$('one-step-checkout-form').insert( { after:paymentIframe } );
					$(iframeId).scrollTo();
				}else{

					if( (typeof $('checkout-review-submit')) == 'undefined' ){
						var btnsHtml  = $$('div.content.button-set').first();
					}else{
						var btnsHtml  = $('checkout-review-submit');
					}

					btnsHtml.hide();
					btnsHtml.insert( { after:paymentIframe } );

				}

			}

		}else if(this.isDirectPaymentMethod() && (typeof response.response_status != 'undefined') && response.response_status == 'threed'){

			 $('sagepaydirectpro-dummy-link').writeAttribute('href', response.redirect);

			 var lcontdtd = new Element('div',{className: 'lcontainer'});
			 var dtd = new Control.Modal('sagepaydirectpro-dummy-link',{
			     className: 'modal sagepaymodal',
                 closeOnClick: false,
                 insertRemoteContentAt: lcontdtd,
			     iframe: true,
			     height: SuiteConfig.getConfig('direct','threed_iframe_height'),
			     width: SuiteConfig.getConfig('direct','threed_iframe_width'),
			     fade: true,
			     afterOpen: function(){

				     if(true === Prototype.Browser.IE){
				     	var ie_version = parseFloat(navigator.appVersion.split("MSIE")[1]);
						if(ie_version<8){
							return;
						}
				     }

				     try{
				     	var daiv = this.container;

				     	if($$('.sagepaymodal').length > 1){
				     		$$('.sagepaymodal').each(function(elem){
				     			if(elem.visible()){
				     				daiv = elem;
				     				throw $break;
				     			}
				     		});
				     	}

						daiv.down().down('iframe').insert({before:new Element('div', {'id':'sage-pay-direct-ddada','style':'background:#FFF'}).update(
							SuiteConfig.getConfig('direct','threed_after').toString() + SuiteConfig.getConfig('direct','threed_before').toString())});

						}catch(er){}

						if(false === Prototype.Browser.IE){
							daiv.down().down('iframe').setStyle({'height':(parseInt(daiv.down().getHeight())-60)+'px'});
							daiv.setStyle({'height':(parseInt(daiv.down().getHeight())+57)+'px'});
						}else{
							daiv.down().down('iframe').setStyle({'height':(parseInt(daiv.down().getHeight())+116)+'px'});
						}

	 			 },
			     afterClose: function(){
                     if($('sage-pay-direct-ddada')){
                     	$('sage-pay-direct-ddada').remove();
                     }
					$('sagepaydirectpro-dummy-link').writeAttribute('href', '');
	 			 }
			 });
			 dtd.container.insert(lcontdtd);
			 dtd.open();

		}else if(this.isDirectPaymentMethod()){
			new Ajax.Request(SuiteConfig.getConfig('direct','sgps_registertrn_url'),{
					onSuccess:function(f){

						try{

							var d=f.responseText.evalJSON();

							if(d.response_status=="INVALID"||d.response_status=="MALFORMED"||d.response_status=="ERROR"||d.response_status=="FAIL"){
								this.getConfig('checkout').accordion.openSection('opc-payment');
								this.growlWarn("An error ocurred with Sage Pay Direct:\n" + d.response_status_detail.toString());
							}else if(d.response_status == 'threed'){
								$('sagepaydirectpro-dummy-link').writeAttribute('href', d.url);
							}

						}catch(alfaEr){
							this.growlError(f.responseText.toString());
						}

					}.bind(this)
			});
		}
		else{
			this.getConfig('review').nextStep(transport);
			return;
		}
	}
}

try{
	Event.observe(window,"load",function(){
	var options = document.getElementsByName('payment[method]');
	var checkSagepay = false;
	for(var i=0;i<options.length;i++){
		if(options[i].id.contains('sagepay')){
			checkSagepay = true;
			break;
		}
	
	}	
	if(!checkSagepay || checkSagepay==false){
		return;
	}
	$(document.body).insert(new Element('a', { 'id': 'sagepayserver-dummy-link', 'href': '#', 'style':'display:none' }).update('&nbsp;'));
	$(document.body).insert(new Element('a', { 'id': 'sagepaydirectpro-dummy-link', 'href': '#', 'style':'display:none' }).update('&nbsp;'));

	var msCont = $('suite_ms_payment_method');

		if( !msCont && (SuiteConfig.getConfig('global', 'ajax_review') == '2') && ((typeof window.review) != 'undefined') ){
	        var SageServer = new EbizmartsSagePaySuite.Checkout(
	                {
	                        'checkout':             window.checkout,
	                        'review':               window.review,
	                        'payment':              window.payment,
	                        'billing':              window.billing,
	                        'accordion':            window.accordion
	                }
	        );
        }else if(!msCont && ($$('div.shopping-cart-totals').length != 1) && $('onestepcheckout-form')){
	        var SageServer = new EbizmartsSagePaySuite.Checkout(
	                {
	                        'osc': $('onestepcheckout-place-order'),
	                        'oscFrm': $('onestepcheckout-form')
	                }
	        );

        }else if($('one-step-checkout-form')){
			var SageServer = new EbizmartsSagePaySuite.Checkout(
	                {
	                        'magestore': $('onestepcheckout-button-place-order'),
	                        'magestoreFrm': $('one-step-checkout-form')
	                }
	        );
		}else if(msCont && (msCont.getValue() == 'sagepayserver')){
	        var SageServer = new EbizmartsSagePaySuite.Checkout(
	                {
	                	'msform': $$('div.multiple-checkout')[0].down(2)
	                }
	        );
        }

		if(parseInt(SuiteConfig.getConfig('global','valid')) === 0){
			if(SuiteConfig.getConfig('direct','mode') == "live" || SuiteConfig.getConfig('server','mode') == "live"){
				new PeriodicalExecuter(function(){ alert(SuiteConfig.getConfig('global','not_valid_message')); }, 10);
			}else{
				var invalidG = new k.Growler({location:"bl"}).error('<strong>'+SuiteConfig.getConfig('global','not_valid_message')+'</strong>', {life:14400});
			}
		}
	})
}catch(er){ suiteLogError(er); }


    addValidationClass = function(obj){
        if(obj.hasClassName('validation-passed')){
            obj.removeClassName('validation-passed');
        }
        obj.addClassName('validate-issue-number');
    }
    paypalClean = function(reverse){
    	var ccTypeContainer = $('sagepaydirectpro_cc_type');
       	var sf = 'div#payment_form_sagepaydirectpro';
       	var sfls = $$(sf+' input, '+sf+' select, '+sf+' radio, '+sf+' checkbox');

       	if(reverse){
	       	//sfls.invoke('enable');
	       	//Just hide items wose parent is visible, these prevents enabling hiden token card elements
	       	sfls.each(function(item){
	       		if(item.up().visible() === true){
	       			item.enable();
	       		}
	       	});

	       	sfls.invoke('show');

	       	$$(sf+' label, '+sf+' a[class!="addnew"]').invoke('show');

	       	//ccTypeContainer.show();
	       	//ccTypeContainer.disabled = false;
	       	ccTypeContainer.addClassName('validate-ccsgpdp-type-select');
       	}else{
	       	sfls.invoke('disable');
	       	sfls.invoke('hide');

	       	$$(sf+' label, '+sf+' a').invoke('hide');

	       	ccTypeContainer.show();
	       	ccTypeContainer.disabled = false;
	       	ccTypeContainer.removeClassName('validate-ccsgpdp-type-select');
       	}

    }
    changecsvclass = function(obj) {
        var ccTypeContainer = $('sagepaydirectpro_cc_type');
        var ccCVNContainer = $('sagepaydirectpro_cc_cid');

        fillSagePayTestData();

        if(ccTypeContainer.value == 'PAYPAL'){//PayPal MARK integration
        	paypalClean(false);
        }else{
        	paypalClean(true);
        }

        if(ccTypeContainer)
        {
            if(ccTypeContainer.value == 'LASER' && ccCVNContainer.hasClassName('required-entry'))
            {
                if(ccCVNContainer) {
                    ccCVNContainer.removeClassName('required-entry');
                }
            }
            if(ccTypeContainer.value != 'LASER' && !ccCVNContainer.hasClassName('required-entry'))
            {
                if(ccCVNContainer) {
                    ccCVNContainer.addClassName('required-entry');
                }
            }
        }
    }

    Validation.addAllThese([
        ['validate-ccsgpdp-number', 'Please enter a valid credit card number.', function(v, elm) {
                // remove non-numerics
			try{
           var ccTypeContainer = $(elm.id.substr(0,elm.id.indexOf('_cc_number')) + '_cc_type');
                if (ccTypeContainer && typeof Validation.creditCartTypes.get(ccTypeContainer.value) != 'undefined'
                        && Validation.creditCartTypes.get(ccTypeContainer.value)[2] == false) {
                    if (!Validation.get('IsEmpty').test(v) && Validation.get('validate-digits').test(v)) {
                        return true;
                    } else {
                        return false;
                    }
                }

                if (ccTypeContainer.value == 'OT' ||  ccTypeContainer.value == 'UKE' || ccTypeContainer.value == 'DELTA' || ccTypeContainer.value == 'MAESTRO' || ccTypeContainer.value == 'SOLO' || ccTypeContainer.value == 'SWITCH' || ccTypeContainer.value == 'LASER' || ccTypeContainer.value == 'JCB' || ccTypeContainer.value == 'DC') {
                     return true;
                }

                return validateCreditCard(v);
                }catch(_error){return true;}
            }],
        ['validate-ccsgpdp-cvn', 'Please enter a valid credit card verification number.', function(v, elm) {
        		try{
                var ccTypeContainer = $(elm.id.substr(0,elm.id.indexOf('_cc_cid')) + '_cc_type');
                var ccCVNContainer = $(elm.id.substr(0,elm.id.indexOf('_cc_cid')) + '_cc_cid');
                if(ccTypeContainer)
                {
                    if(ccTypeContainer.value == 'LASER' && ccCVNContainer.hasClassName('required-entry'))
                    {
                        if(ccCVNContainer) {
                            ccCVNContainer.removeClassName('required-entry');
                        }
                    }
                    if(ccTypeContainer.value != 'LASER' && !ccCVNContainer.hasClassName('required-entry'))
                    {
                        if(ccCVNContainer) {
                            ccCVNContainer.addClassName('required-entry');
                        }
                    }
                }
                else
                {
                    return true;
                }
                if (!ccTypeContainer && ccTypeContainer.value != 'LASER') {
                    return true;
                }
                var ccType = ccTypeContainer.value;

                switch (ccType) {
                    case 'VISA' :
                    case 'MC' :
                        re = new RegExp('^[0-9]{3}$');
                        break;
                    //case 'AMEX' :
                    //    re = new RegExp('^[0-9]{4}$');
                    //    break;
                    case 'MAESTRO':
                    case 'SOLO':
                    case 'SWITCH':
                        re = new RegExp('^([0-9]{1}|^[0-9]{2}|^[0-9]{3})?$');
                        break;
                    default:
                        re = new RegExp('^([0-9]{3}|[0-9]{4})?$');
                        break;
                }

                if (v.match(re) || ccType == 'LASER') {
                    return true;
                }

                return false;
                                }catch(_error){return true;}
            }],
            ['validate-ccsgpdp-type', 'Credit card number doesn\'t match credit card type', function(v, elm) {
            try{
                    // remove credit card number delimiters such as "-" and space
                    elm.value = removeDelimiters(elm.value);
                    v         = removeDelimiters(v);

                    var ccTypeContainer = $(elm.id.substr(0,elm.id.indexOf('_cc_number')) + '_cc_type');
                    if (!ccTypeContainer) {
                        return true;
                    }
                    var ccType = ccTypeContainer.value;

                    // Other card type or switch or solo card
                    if (ccType == 'OT' ||  ccType == 'UKE' || ccType == 'DELTA' || ccType == 'MAESTRO' || ccType == 'SOLO' || ccType == 'SWITCH' || ccType == 'LASER' || ccType == 'JCB' || ccType == 'DC') {
                        return true;
                    }
                    // Credit card type detecting regexp
                    var ccTypeRegExp = {
                        'VISA': new RegExp('^4[0-9]{12}([0-9]{3})?$'),
                        'MC': new RegExp('^5[1-5][0-9]{14}$'),
                        'AMEX': new RegExp('^3[47][0-9]{13}$')
                    };

                    // Matched credit card type
                    var ccMatchedType = '';
                    $H(ccTypeRegExp).each(function (pair) {
                        if (v.match(pair.value)) {
                            ccMatchedType = pair.key;
                            throw $break;
                        }
                    });

                    if(ccMatchedType != ccType) {
                        return false;
                    }

                    return true;
                                    }catch(_error){return true;}
                }],
         ['validate-ccsgpdp-type-select', 'Card type doesn\'t match credit card number', function(v, elm) {
                try{var ccNumberContainer = $(elm.id.substr(0,elm.id.indexOf('_cc_type')) + '_cc_number');
                return Validation.get('validate-ccsgpdp-type').test(ccNumberContainer.value, ccNumberContainer);
                                }catch(_error){return true;}
            }],
         ['validate-issue-number', 'Issue Number must have at least two characters', function(v, elm) {
				try{
                if(v.length > 0 && !(v.match(new RegExp('^([0-9]{1}|[0-9]{2})$')))){
                    return false;
                }

                return true;
                                }catch(_error){return true;}
            }]
    ]);


