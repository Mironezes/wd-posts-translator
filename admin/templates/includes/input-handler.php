<?php 
  function wdpt_checkbox_handler_html($args) { ?>
    <input type="checkbox" name="<?= $args['field_name']; ?>" value="1" <?php checked(esc_attr(get_option($args['field_name']))) ?>>  
  <? }

  function wdpt_text_handler_html($args) { ?>
    <input type="text" 
      <?php if(isset($args['id'])) : ?> id="<?= $args['id']; ?>" <?php endif ?>
      name="<?= $args['field_name']; ?>" 
      value="<?= esc_attr(get_option($args['field_name']));?>" >  
  <? }

function wdpt_image_to_url_handler_html($args) { 
  if(get_option($args['field_name'])) {
    $attachment_url = esc_attr(get_option($args['field_name']));
  }
  else {
    $attachment_id = esc_attr(get_option($args['field_name']));
    $attachment_url = wp_get_attachment_url($attachment_id);
  }
?>
  <input type="text" 
    <?php if(isset($args['id'])) : ?> id="<?= $args['id']; ?>" <?php endif ?>
    name="<?= $args['field_name']; ?>" 
    value="<?= $attachment_url; ?>" 
    >  
<? }


  function wdpt_textarea_handler_html($args) { ?>
    <textarea name="<?= $args['field_name']; ?>" cols="30" rows="10"><?= esc_attr(get_option($args['field_name']));?></textarea>
  <?php }

  function wdpt_date_handler_html($args) { ?>
    <input type="date" name="<?= $args['field_name']; ?>" value="<?= esc_attr(get_option($args['field_name']));?>" >  
  <? }


  function wdpt_number_handler_html($args) { ?>
    <input type="number" name="<?= $args['field_name']; ?>" min="<?= $args['min']; ?>" max="<?= $args['max']; ?>" value="<?= esc_attr(get_option($args['field_name']));?>" >  
  <? }