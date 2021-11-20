<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://github.com/Mironezes
 *
 * @package    wd_posts_translator
 * @subpackage wd_posts_translator/admin/templates
 */

  require_once('includes/input-handler.php');
  require_once('includes/form-handler.php');

  function wdpt_settings_template() { ?>
    <div id="wdpt-settings-page">
      <div class="container">

        <h1>WD Posts Translator  <small>ver <?= WD_POSTS_TRANSLATOR_VERSION ?></small></h1>

        <?php 
          if(!empty($_POST['wdpt_form_submitted'])) {
            $_POST['wdpt_form_submitted'] === 'true' ? wdpt_form_handler() : null; 
          } 
        ?>

        <form method="POST">

          <input type="hidden" name="wdpt_form_submitted" value='true'>
          <?php wp_nonce_field('wdpt_save_settings', 'wdpt_nonce'); ?>

          <?php include_once('sections/basic-section.php') ?>

          <input type="submit" name="submit" id="submit" class="wdpt-button submit" value="<?= __('Сохранить изменения', 'wdpt_domain') ?>">
        </form>
      </div>
    </div>
  <?php }