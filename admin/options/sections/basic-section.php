<?php

	// Vacancy title translation handler
  function wdpt_vacancy_title_translate($title) {
    $title_dictionary = array_change_key_case(get_option('wdpt_title_dictionary', ''));
    
    $check_abbreviations = function($word) {
      if(get_option('wdpt_abbrv_dictionary')) {
        $abbr_dictionary_list = get_option('wdpt_abbrv_dictionary', '');
        if(!in_array($word, $abbr_dictionary_list)) {
          return ucfirst($word);
        }
      }
      return $word;
    };
    
    $title_arr = array_map($check_abbreviations, explode(' ', $title));

    foreach ($title_arr as $word) {
      if(array_key_exists(strtolower($word), $title_dictionary)) {
        $translation = $title_dictionary[strtolower($word)];
        $translated_word = preg_replace('/\b'.$word.'\b/', $translation, $title_arr);
      
        $title_arr = $translated_word;
      }
    } 

    $translated_title = implode(' ', $title_arr); 
    return $translated_title;
  }


  // Check if post has been already translated
  function wdpt_is_post_translated($content) {

    preg_match_all('/[А-Яа-яЁё]/u', $content, $matches);

    if(count($matches[0]) > 10) {
      return true;
    }
    else {
      return false;
    }
  }


	// Vacancy content translation handler
  add_action( 'translate_posts_event', 'wdpt_translate_vacancy' );
  function wdpt_translate_vacancy() {
    // get list of posts in ukrainian
    $ukrainianPosts = getPosts('any', -1, null, 'vacancy', 58, 'uk');

    foreach ($ukrainianPosts as $ukr_vacancy) {
      try {

        $id = $ukr_vacancy->ID;

        if( get_post_meta($id, 'WDVacancyImported', true) ) {
          $original_content = $ukr_vacancy->post_content;
          $has_translation = wdpt_is_post_translated($original_content);
    
          if(!$has_translation) {
    
            $translated_title = wdpt_vacancy_title_translate($ukr_vacancy->post_title);    
            $sections_names = wdpt_get_content_sections($original_content);
            $translated_content_arr = [];

            foreach($sections_names as $section_name) {
              $section = wdpt_content_slicer($original_content, $section_name);
              $tr_section = wdpt_google_translate($section);
              array_push($translated_content_arr, $tr_section);
            }

            $translated_content = implode('', $translated_content_arr);
            
            $is_translated = wdpt_is_post_translated($translated_content);
    
            if($is_translated) {
              if ( $ukr_vacancy->post_status === 'publish' ) {
                $translated_location = wdpt_google_translate(get_post_meta($id, 'WDVacancySubtitle', true));
                if($translated_location) {
                  update_post_meta($id, 'WDVacancySubtitle', $translated_location);
                }
          
                $args = array(
                  'ID' => $id,
                  'post_title' => $translated_title,
                  'post_content' => $translated_content,
                );
          
                wp_update_post($args);
              }
              else {
                wp_publish_post($id);
              }
            }
            else {
              throw new Exception;
            }
          }
          elseif( $ukr_vacancy->post_status !== 'publish') {
            wp_publish_post($id);
          }
        }
      }
      catch (Exception $e) {

        $args = array(
          'ID' => $id,
          'post_status' => 'draft'
        );
          
        wp_update_post($args);
        continue;
      }
    }
  }


	// Google Translate Library Settings
  function wdpt_google_translate($content) {
    $source = 'en';
    $target = 'uk';
    $attempts = 1;
    
    $tr = new GoogleTranslateForFree();
    if(!isset($tr)) return;
    return $tr->translate($source, $target, $content, $attempts);
  }


  // Slices vacancy`s content on small separate sections before translation
  function wdpt_content_slicer($content, $section_class) {
    $content = preg_replace('/\n/m', '', $content); 
    
    preg_match('/<div class="'.$section_class.'"><h3\s+.*?>.*?<\/h3>.*?<\/div>/', $content, $filtered_content);
    if($filtered_content[0]) return $filtered_content[0];
    else return false;
  }

  function wdpt_get_content_sections($content) {
    preg_match_all('/<div class="(.*?)">/', $content, $sections);
    return $sections[1];
  }


	// Request Translation btn handler
  add_filter( 'post_row_actions', 'wdpt_request_translation_handler', 10, 2 );
  function wdpt_request_translation_handler($actions, $post) {
    $post_to_translate = pll_get_post_translations($post->ID);
    $is_translated = wdpt_is_post_translated($post->post_content);

    if(pll_get_post_language($post->ID) === 'uk' && !$is_translated) {
      if ($post_to_translate) {
        $actions['translate'] = sprintf(
            '<span class="wdpt-request-translate-btn" style="color: #2271b1; cursor: pointer;"
        data-post_id="' . $post->ID . '" data-action="translate_vacancy" >Запросить перевод</sp>'
        );
      }
    }

    return $actions;
  }


  // Single Vacancy Translate
  add_action( 'wp_ajax_translate_vacancy', 'wdpt_single_post_translate' );
  function wdpt_single_post_translate() {
    check_ajax_referer( 'post-single-translate', 'security' );

    try {
      $id = $_POST['post_id'];

      $vacancy = get_post($id);
      $original_content = $vacancy->post_content;
      $has_translation = wdpt_is_post_translated($original_content);
    
      if(!$has_translation) {

        $translated_title = wdpt_vacancy_title_translate($vacancy->post_title);
          
        $sections_names = wdpt_get_content_sections($original_content);
        
        $translated_content;
        
        foreach($sections_names as $section_name) {
          $section = wdpt_content_slicer($original_content, $section_name);
          $tr_section = wdpt_google_translate($section);
          $translated_content .= $tr_section;
        }
        
        $is_translated = wdpt_is_post_translated($translated_content);

        if($is_translated) {
          if ( $vacancy->post_status === 'publish' ) {
            $translated_location = wdpt_google_translate(get_post_meta($id, 'WDVacancySubtitle', true));
            if($translated_location) {
              update_post_meta($id, 'WDVacancySubtitle', $translated_location);
            }
      
            $args = array(
              'ID' => $id,
              'post_title' => $translated_title,
              'post_content' => $translated_content,
            );
      
            wp_update_post($args);          
          }
           {
            wp_publish_post($id);
          } 
        }
        else {
          throw new Exception;
        }
      }
    }
    catch (Exception $e) {
      $args = array(
        'ID' => $id,
        'post_status' => 'draft'
      );
      wp_update_post($args);
    }
  }


  // Single event to translate posts 
  add_action( 'wp_ajax_translate_posts', 'wdpt_cron_translate_all' );
	add_action( 'wp_ajax_nopriv_translate_posts', 'wdpt_cron_translate_all' );
  function wdpt_cron_translate_all() {
    check_ajax_referer( 'posts-translate-nonce', 'security' );
    wp_schedule_single_event( time(), 'translate_posts_event'); 
  }


  // Title Dictionary Handler
  add_action( 'wp_ajax_update_title_dictionary', 'wdpt_title_dictionary_handler' );
  add_action( 'wp_ajax_nopriv_update_title_dictionary', 'wdpt_title_dictionary_handler' );
  function wdpt_title_dictionary_handler() {
    check_ajax_referer( 'title-dictionary-nonce', 'security' );

    $title_dictionary = $_POST["title_dictionary"];
    update_option('wdpt_title_dictionary', $title_dictionary);
  }


// Filters translated content from validation errors
add_filter( 'the_content', 'vacancy_translated_content_filter' );
function vacancy_translated_content_filter( $content ) {

	if ( pll_current_language() === 'uk' ) {
		$content = preg_replace( '/< (br|nobr|p|div)>/m', '<$1>', $content );
		$content = preg_replace( '/<\ (p|div)\.>/m', '</$1>', $content );
		$content = preg_replace( '/<[^>]nobr>/m', '', $content );
		$content = preg_replace( '/<[^<]*\/[^<]*nobr[^>]*>/', '', $content );
		$content = preg_replace( '/<[^<]*nobr[^>]*>/', '', $content );
		$content = preg_replace( '/<[^<]*\/[^<]*div[^>]*\.[^>]*>/', '</div>', $content );
		$content = preg_replace( '/<[^<]*\/[^<]*div[^>]*>/', '</div>', $content );

	}

	return $content;
}