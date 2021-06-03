window.addEventListener('load', ($) => {

  // init
  $ = jQuery;
  // console.log('extend');

  // Run only on form
  if($('form#wpcm-car-form').length  &&  !location.search.includes('?wpcm_step=')) {
  
    // Declare variables
    const ajaxURL = '/wp-admin/admin-ajax.php';
    let new_make  = null;
    let new_model = null;
    let make_ID   = null;
    let makeRes, modelRes;
    
    // Create buttons
    $('.wpcm-fieldset-make label')
    .append(' <small id="add_new_make" class="d-none">(Add&nbsp;new&nbsp;make)</small>');
    $('.wpcm-fieldset-model label')
    .append(' <small id="add_new_model" class="d-none">(Add&nbsp;new&nbsp;model)</small>');

    // Create response message
    $('.wpcm-fieldset-make').append('<span class="response-msg d-none"></span>');
    $('.wpcm-fieldset-model').append('<span class="response-msg d-none"></span>');

    // Assign response message
    makeRes   = $('.wpcm-fieldset-make .response-msg');
    modelRes  = $('.wpcm-fieldset-model .response-msg');

    // On Select2 Open - Display options
    $('#make').on('select2:open', () => $('#add_new_make').fadeIn() );
    $('#model').on('select2:open', () => $('#add_new_model').fadeIn() );
    
    // On select2 Close Hide options;
    $('#make').on('select2:close', () => {
      setTimeout(() => { // check if select2 is not reopened
        if($('#make').data('select2').isOpen() == false) {
          $('#add_new_make').fadeOut()
        }
      },500)
    });	

    $('#model').on('select2:close', () => {
      setTimeout(() => { // check if select2 is not reopened
        if($('#model').data('select2').isOpen() == false) {
          $('#add_new_model').fadeOut()
        }
      },500)
    });

    $('#make').on('select2:select', () => new_make = $('#make').find(':selected')[0].label);
    $('#model').on('select2:select', () => new_model = $('#model').find(':selected')[0].label);
    
    // AJAX
    
    // Find search value for make
    $("#make").data("select2").dropdown.$search.on('input', function(e) {
      new_make = stripString(e.target.value);
    });

    // Find search value for model
    $("#make").on('change', () => { make_ID = $('#make').find(':selected').val() });
    
    // Wait for Select2 options to refresh models and get input value
    observeElMutations( $('#model')[0], observeElMutationsCallback );
    
    function observeElMutationsCallback() {
      $("#model").data("select2").dropdown.$search.on('input', function(e) {
        new_model = stripString(e.target.value);
      });
    }

    
    // Create new make with AJAX
    $('#add_new_make').on('click', function() { 
      if(new_make && new_make.length) {
        makeRes.text('Adding new make...');
        makeRes.fadeIn();
        add_new_make()
      } else {
        makeRes.text('Field is empty!');
        makeRes.fadeIn().delay(5000).fadeOut();
      }
    });
    
    function add_new_make() {
      $.ajax({
        type: "POST",
        dataType: 'text',
        url: ajaxURL,
        data: {
            action: 'add_new_make_fn',
            // add your parameters here
            new_make_name: new_make
        },
        success: function (output) {
          jOutput = JSON.parse(output);

          if(jOutput.success) {
            let jData = jOutput.data;
          
            var newOption = new Option(jData.text, jData.id, true, true);
            $('#make').append(newOption).trigger('change');
            
            // Display success
            makeRes.text('New make added!');
          } else {
            // Display err
            makeRes.text('This make already exist!');
          }
          
        },
        error: function(jqXHR, exception ) {
          errCodes(jqXHR, exception);
          makeRes.text('D\'oh, something went wrong');
        },
        complete: function() {
          makeRes.fadeIn().delay(5000).fadeOut();
      }
      });
    }


    // Create new model with AJAX
    $('#add_new_model').on('click', function() {
      if(!new_make) {
        modelRes.text('Select make first');
        modelRes.fadeIn().delay(5000).fadeOut();
        return
      }
      if(new_model && new_model.length) {
        modelRes.text('Adding new model...');
        modelRes.fadeIn();
        add_new_model()
      } else {
        modelRes.text('Field is empty!');
        modelRes.fadeIn().delay(5000).fadeOut();
      }

    });
    
    function add_new_model() {
      $.ajax({
        type: "POST",
        dataType: 'text',
        url: ajaxURL,
        data: {
            action: 'add_new_model_fn',
            // add your parameters here
            new_model_name: new_model,
            new_model_parent_ID : make_ID
        },
        success: function (output) {
          jOutput = JSON.parse(output);

          if(jOutput.success) {
            let jData = jOutput.data;
            
            var newOption = new Option(jData.text, jData.id, true, true);
            $('#model').append(newOption).trigger('change');
            
            // Display success
            modelRes.text('New model added!');
          } else {
            // Display err
            modelRes.text('This model already exist!');
          }
        },
        error: function(jqXHR, exception ) {
          errCodes(jqXHR, exception);
          modelRes.text('D\'oh, something went wrong');
        },
        complete: function() {
          modelRes.fadeIn().delay(5000).fadeOut();
      }
      });
    }




    // Add styling
    $('body').append('<style>.d-none{display: none} #add_new_make, #add_new_model {color: red; cursor: pointer} #wpcm-car-form fieldset{position: relative} .response-msg{ position: absolute; left:0; bottom:0}</style>');

    
    // Helper functions
    function stripString(str) {
      return str.replace(/(<([^>]+)>)/gi, "").trim();
    }

    function errCodes(jqXHR, exception) {
      var msg = '';
      if (jqXHR.status === 0) {
          msg = 'Not connect.\n Verify Network.';
      } else if (jqXHR.status == 404) {
          msg = 'Requested page not found. [404]';
      } else if (jqXHR.status == 500) {
          msg = 'Internal Server Error [500].';
      } else if (exception === 'parsererror') {
          msg = 'Requested JSON parse failed.';
      } else if (exception === 'timeout') {
          msg = 'Time out error.';
      } else if (exception === 'abort') {
          msg = 'Ajax request aborted.';
      } else {
          msg = 'Uncaught Error.\n' + jqXHR.responseText;
      }
      console.error(msg);
    }

    
    function observeElMutations(el, cb) {
      const targetNode = el;
      const config = { childList: true };
      const callback = function(mutationsList, observer) {
          // Use traditional 'for loops' for IE 11
          let done = false;
          for(const mutation of mutationsList) {
              if (mutation.type === 'childList' && !done) {
                  cb();
                  done = true;
              }
          }
      };
      const observer = new MutationObserver(callback);
      observer.observe(targetNode, config);
    }

  } // endif;
});