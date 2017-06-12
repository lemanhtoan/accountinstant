jQuery.noConflict(),jQuery(document).ready(function(a){a("#homeSlider").owlCarousel({autoHeight:true,itemsCustom:[[0,1],[480,1],[768,1],[992,1]],navigation:!0}),a("#header .block-topcart").click(function(){a(".block-content").css({opacity:"1",top:"65px","z-index":"999",visibility:"visible"}).toggle()}),a("#categorySlider, #relatedSlider").owlCarousel({itemsCustom:[[0,1],[480,1],[768,3],[992,5]],navigation:!0}),a("#mb-cate").click(function(){var c=a('select[name="categoryPost"] option:selected').val();location.href=c})});
jQuery('#promo-checkbox_id').change(function() {
    if(this.checked) {
        jQuery('.btb-ch').show();
    }else {
    	jQuery('.btb-ch').hide();
    }
});
jQuery('.review-btn').click(function(){
	jQuery('#livechat-badge').click();
	return false;
});