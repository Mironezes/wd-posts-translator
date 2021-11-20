<?php 

function wdpt_isset_option($option, $data = null) {
  if( isset($_POST[$option])) {
    return update_option($option, sanitize_text_field($_POST[$option]));   
  }
  else {
    return update_option($option, '');    
  }
}

function wdpt_form_handler() {
    if(wp_verify_nonce($_POST['wdpt_nonce'], 'wdpt_save_settings') && current_user_can('manage_options')) { 

      wdpt_isset_option('wdpt_abbrv_dictionary'); 
      
    ?>

      <div class="notice updated">
        <p><?= __('Настройки обновлены', 'wdpt_domain') ?></p>
      </div>
    <?php }
      else { ?>
      <div class="notice error">
        <p><?= __('У вас нет прав на это', 'wdpt_domain') ?></p>
      </div>
    <?php  }
  }