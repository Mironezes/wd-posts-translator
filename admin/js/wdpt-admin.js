// Condition for calling our functions
const isPluginPage = document.querySelector('#wdpt-settings-page');


// Hide notices with time helper
const hideMessage = (element, timeout) => {
  setTimeout(function() {
    element.remove();
}, timeout);
};
 

// Deep array comparison helper
const areArrsEqual = (first, second) => {
  if(first.length !== second.length){
     return false;
  };
  for(let i = 0; i < first.length; i++){
     if(!second.includes(first[i])){
        return false;
     };
  };
  return true;
};


// Title Dictionary Handler
function titleDictionaryHandler() {
  // Constants diclarations
  const add_item_button = document.querySelector('.wdpt-list-item-handler.add');
  const save_dictionary_button = document.querySelector('.save-title-dictionary');
  const form = document.querySelector('#wdpt-settings-page form');
  const table = document.querySelector('.wdpt-list-table tbody');
  const original_text = document.querySelector('#wdpt-title-dictionary-item-origin');
  const translated_text = document.querySelector('#wdpt-title-dictionary-item-translation');
  
  // Counts initial dictionary options
  let table_rows = jQuery('.wdpt-list-table tbody tr');
  let table_rows_ids = [];
  jQuery.each(table_rows, function() {table_rows_ids.push(this.id)});

  // Sends added rules to DB with wp_update_option func via ajax
  function updateDictionary() {
    let dictionary_rows = jQuery('.wdpt-list-table tbody tr');
    let data_obj = {};

    dictionary_rows.each((index, row) => {
      let key = row.querySelector('td:nth-of-type(1)').textContent;
      let value = row.querySelector('td:nth-of-type(2)').textContent;
      data_obj[key] = value;
    });

    jQuery.ajax({
      url : wdpt_localize.url,
      type : 'post',
      dataType: 'json',
      data : {
        action : 'update_title_dictionary',
        title_dictionary: data_obj,
        security : wdpt_localize.title_dictionary_nonce,
      },
      success : function(response) {
        let status_msg = document.querySelector('.wdpt-list-table-actions span');
        if(status_msg) status_msg.remove();
        
        save_dictionary_button.insertAdjacentHTML('afterend', '<span class="msg successful">Словарь обновлен</span>');

        hideMessage(document.querySelector('span.msg'), 1200);

        save_dictionary_button.classList.add('saved');
      },
      fail : function(error) {
        let status_msg = document.querySelector('.wdpt-list-table-actions span');
        if(status_msg) status_msg.remove();

        save_dictionary_button.insertAdjacentHTML('afterend', '<span class="msg error">Ошибка, см. информацию в консоли</span>');
        
        hideMessage(document.querySelector('span.msg'), 1200);

        console.log(error);
      }
    });
  }
  save_dictionary_button.addEventListener('click', updateDictionary);

  // Adds rule to dictionary
  function addItem() {
    if(original_text.value && translated_text.value) {
      save_dictionary_button.classList.remove('saved');
      let original_text_temp = original_text.value;
      let translated_text_temp = translated_text.value;
  
      original_text.value = '';
      translated_text.value = '';
  
      table.insertAdjacentHTML('beforeend',`
      <tr id="${wdpt_localize.wp_rand}">
        <td>${original_text_temp}</td>
        <td>${translated_text_temp}</td>
        <td class="wdpt-list-table__remove-item"><i class="fas fa-trash"></i></td>
      </tr>
      `);
    }
  }
  add_item_button.addEventListener('click', addItem);

  // Removes rule from dictionary
  function removeItem() {
    save_dictionary_button.classList.remove('saved');
    if(confirm('Удалить правило перевода из словаря?')) {
      this.closest('tr').remove();
    }
  }
  jQuery(document).on('click','.wdpt-list-table__remove-item i', removeItem);

  // Checks if no unsaved changes are left
  function onSave(e) {
    // Counts actual (on save) dictionary options
    let current_table_rows = jQuery('.wdpt-list-table tbody tr');
    let current_table_rows_ids = [];
    jQuery.each(current_table_rows, function() {
      current_table_rows_ids.push(this.id);
    });

    // Checks if saved options are equal to currents
    let is_ids_equals;
    if(areArrsEqual(table_rows_ids, current_table_rows_ids)) is_ids_equals = true;

    // If options are not equal & changes unsaved then show notification
    if( !is_ids_equals && !save_dictionary_button.classList.contains('saved')) {
      let warning_msg = confirm('Внесенные в словарь изменения будут утеряны. Продолжить?');
      if(!warning_msg) {
        e.preventDefault();
      }
    }
  }
  form.addEventListener('submit', onSave);
}


// Translate all Posts Ajax handler
function translatePosts() {
  const button = document.querySelector('#wdpt-update-posts-translation-btn');

  function init() {
    if(button.closest('label').nextElementSibling) {
      button.closest('label').nextElementSibling.remove();
    }
    jQuery.ajax({
      url : wdpt_localize.url,
      type : 'post',
      data : {
        action : 'translate_posts',
        security : wdpt_localize.translate_nonce,
      },
      complete : function(response) {
        button.closest('label').insertAdjacentHTML('afterend', '<span class="msg-successful">Запрос успешно получен, ожидайте перевода</span>');
      },
      fail : function(error) {
        console.log(error);
        button.closest('label').insertAdjacentHTML('afterend', '<span class="msg-error">Ошибка, см. информацию в консоли</span>');
      }
    });
  }
  button.addEventListener('click', init); 
}


// Resets value attr in target input
function resetValue(item) {
  const button = document.querySelector(item.button);
  const target = document.querySelector(item.target);

  button.addEventListener('click', () => {
    if( target.value !== "" && confirm('Точно?') ) {
      target.value = "";
    }
  });
}


// Show/hide accordion content helper
function toggleAccordion() {
  const accordions = Array.from(document.querySelectorAll('.wdpt-setting-item-accordion'));

  accordions.forEach(accordion => {

    let content = accordion.nextElementSibling;

    accordion.addEventListener('click', () => {
      if(content.classList.contains('active')) {
        accordion.classList.remove('opened');
        content.classList.remove('active');
      }
      else {
        accordion.classList.add('opened');
        content.classList.add('active');
      }
    });
  });
}


// Checkbox toggle helper
function toggleCheckbox() {
  const inputs = Array.from(document.querySelectorAll('input[type="checkbox"'));
  inputs.forEach((input) => {
    input.addEventListener('click', () => {
      
      if(input.hasAttribute('checked')) {
        input.removeAttribute('checked');
        input.value = 0;
        input.checked = false;
      }
      else {
        input.setAttribute('checked', 'checked');
        input.checked = true;
        input.value = 1;
      }
    })
  });
}


function Init() {
  if(isPluginPage) {
    toggleCheckbox();
    toggleAccordion();
    translatePosts();
    titleDictionaryHandler();
  }
}
document.addEventListener('DOMContentLoaded', Init);