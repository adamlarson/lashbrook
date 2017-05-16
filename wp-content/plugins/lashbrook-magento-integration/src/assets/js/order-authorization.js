jQuery(document).ready(function($){
	var $signature = $("#auth-signature").jSignature();
	$signature.bind('change',function(){
		lashbrook_signatureChanged();
	});

	var $signature_empty = true;
	var $signature_reset = false;
	var $btnResetSignature = $("#reset-signature");
	$("#reset-signature").on('click',function(e){
		e.preventDefault();
		lashbrook_resetSignature();
	});

	var $btnAuthorizeSignature = $("#authorize-signature");
	$btnAuthorizeSignature.on('click',function(e){
		e.preventDefault();
		$(this).prop('disabled',true);
		lashbrook_authorizeOrder();
	});
	$btnAuthorizeSignature.prop('disabled',true);

	function lashbrook_resetSignature(){
		$btnAuthorizeSignature.prop('disabled',true);
		$signature_reset = true;
		$signature_empty = true;
		$signature.jSignature('reset');
	}

	function lashbrook_authorizeOrder(){
		var datapair = $signature.jSignature("getData", "svgbase64");
		var post = {
			action: lashbrook.actions.authorize_order,
			nonce: lashbrook.nonce,
			order_token: lashbrook.order_token,
			signature: "data:" + datapair[0] + "," + datapair[1]
		}
		$.ajax({
			url: lashbrook.ajax_url,
			type: 'post',
			dataType: 'json',
			data: post,
			success:function(response){
				if(response.success){
					var alert = "<div class=\"alert alert-success\">" + response.message + "</div>";
					$("#signature-div").fadeOut('fast',function(){
						$(this).html(alert).fadeIn();
					});
				}else{
					// didn't work
				}
			},
			error: function(jqXhr, textStatus, errorThrown){
				console.log(errorThrown);
			}
		});
	}

	function lashbrook_signatureChanged(){
		if($signature_reset){
			$signature_reset = false;
		}else{
			$signature_empty = false;
			$btnAuthorizeSignature.prop('disabled',false);
		}
	}
});
