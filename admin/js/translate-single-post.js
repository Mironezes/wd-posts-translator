function singleVacancyTranslateHandler() {
  // Send content on button click
  jQuery('.wdpt-request-translate-btn').click(function () {
      let action = jQuery(this).data('action');
      let post_id = jQuery(this).data('post_id');
  
      var current_el = this;
      var translation_is_done = '<span style="color: green;" class="twizards_translate_in_process">Переведено</span>';
  
      jQuery.ajax({
            url : wdpt_single_translation.url,
            type : 'post',
            data : {
              action : action,
              post_id: post_id,
              security : wdpt_single_translation.translate_nonce,
            },
            complete : function(response) {
              if(response) {
                jQuery(current_el).replaceWith(translation_is_done);
              }
            },
            fail : function(error) {
              alert('Ошибка во время перевода, см. консоль');
              console.log(error);
            }
      });
  });
}
document.addEventListener('DOMContentLoaded', singleVacancyTranslateHandler);