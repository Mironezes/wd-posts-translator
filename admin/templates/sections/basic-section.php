<section id="wdpt-snippets-settings" class="wdpt-section">
  <div class="wdpt-section-header">
      <h2>Базовые настройки</h2>
  </div>
  <div class="wdpt-row">
    <div class="wdpt-section-content">

      <div id="wdpt-dictionaries-group" class="wdpt-setting-group">  
        <div id="wdpt-titles-dictionary" class="wdpt-setting-item">
            <div class="wdpt-setting-item-accordion">
              <h3>Cловарь тайтлов</h3>
              <i class="fas fa-chevron-down"></i>
            </div>

              <div class="wdpt-setting-item-accordion-content">
                <div class="wdpt-add-item-panel">
                  <div class="wdpt-list-item">
                    <span>Cлово</span>
                    <?php wdpt_text_handler_html(
                      ['field_name' => 'wdpt_title_dictionary_item_origin', 
                       'id' => 'wdpt-title-dictionary-item-origin',
                       ]
                      ); ?>
                  </div>
                  <div class="wdpt-list-item">             
                    <span>Перевод</span>
                    <?php wdpt_text_handler_html(
                      ['field_name' => 'wdpt_title_dictionary_item_translation', 
                       'id' => 'wdpt-title-dictionary-item-translation']
                      ); ?>
                  </div>

                  <div class="wdpt-list-item-handlers">
                    <span title="Добавить новое слово" class="wdpt-list-item-handler add">Добавить</span>
                  </div>        
                </div>
                <div class="wdpt-list-table-wrapper">
                  <table class="wdpt-list-table">
                    <thead>
                      <th>Слово</th>
                      <th>Перевод</th>
                      <th>Действие</th>
                    </thead>
                    <tbody>
                      <?php 
                          if( get_option('wdpt_title_dictionary', '') ) {
                            $dictionary = get_option('wdpt_title_dictionary');

                            foreach ($dictionary as $word => $translation) : ?>
                              <tr id="<?= wp_rand();?>">
                                <td><?= $word; ?></td>
                                <td><?= $translation; ?></td>
                                <td class="wdpt-list-table__remove-item"><i class="fas fa-trash"></i></td>
                              </tr>
                            <?php endforeach; 
                          } ?>
                    </tbody>
                  </table>
                  <div class="wdpt-list-table-actions">
                    <button type="button" class="wdpt-button save-title-dictionary">Сохранить словарь</button>
                  </div>
                </div>
              </div>
        </div>   

        <div id="wdpt-abbrv-dictionary" class="wdpt-setting-item">
            <div class="wdpt-setting-item-accordion">
              <h3>Словарь аббревиатур для тайтлов</h3>
              <i class="fas fa-chevron-down"></i>
            </div>

            <div class="wdpt-setting-item-accordion-content">
              <strong>Список слов, не подлежащих смене ригистра (через запятую)</strong>
              <?php 
                wdpt_textarea_handler_html(['field_name' => 'wdpt_abbrv_dictionary']);        
              ?>
            </div>  
        </div>
      </div>
    </div>
  </div>
</section>