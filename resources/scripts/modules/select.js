import SimpleBar from 'simplebar';
import 'simplebar/dist/simplebar.css';

export default class CustomSelect {
    
    constructor() {
           document.addEventListener("DOMContentLoaded",()=>{

              this.init();

           });
    }

    populate(ulElt, selectElt) {
      (function(window, $) {
        let $this = $(selectElt),
            numberOfOptions = $this.children('option').length;

        $(ulElt).empty();

        for (var i = 0; i < numberOfOptions; i++) {
            $('<li />', {
                text: $this.children('option').eq(i).text(),
                rel: $this.children('option').eq(i).val()
            }).appendTo($(ulElt));
        }

        $this.next('div.select-styled').text($this.children('option').eq(0).text());

      })(window, jQuery);
    }
    
    init() {
      var self = this;
        
        (function(window, $) {
           
           $('.entry-content select').each(function(){
               var $this = $(this), numberOfOptions = $(this).children('option').length;
             
               $this.addClass('select-hidden'); 
               $this.wrap('<div class="select plda-select"></div>');
               $this.after('<div class="select-styled is-placeholder"></div>');

               var $styledSelect = $this.next('div.select-styled');
               $styledSelect.text($this.children('option').eq(0).text());
             
               var $list = $('<ul />', {
                   'class': 'select-options'
               }).insertAfter($styledSelect);

               self.populate($list[0], this);
             
               // for (var i = 0; i < numberOfOptions; i++) {
               //     $('<li />', {
               //         text: $this.children('option').eq(i).text(),
               //         rel: $this.children('option').eq(i).val()
               //     }).appendTo($list);
               // }
             
               var $listItems = $list.children('li');
             
               $styledSelect.click(function(e) {
                   e.stopPropagation();
                   $('div.select-styled.active').not(this).each(function(){
                       $(this).removeClass('active').next('ul.select-options').hide();
                   });
                   $(this).toggleClass('active').next('ul.select-options').toggle();
               });
             
               $list.on('click', 'li', function(e) {
                   e.stopPropagation();
                   $styledSelect.text($(this).text()).removeClass('active');
                   $styledSelect.removeClass('is-placeholder');
                   $this.val($(this).attr('rel')).trigger('change');
                   if( typeof calders_forms_check_conditions === "function"){ calders_forms_check_conditions($this.closest('form').prop('id')) }
                   $list.hide();
                   //console.log($this.val());
               });
             
               $(document).click(function() {
                   $styledSelect.removeClass('active');
                   $list.hide();
               });

              // custom scroll
              document.querySelectorAll('ul.select-options').forEach( (elt) => {
                console.log(elt);
                new SimpleBar(elt);
              });
              

           });
        })(window, jQuery);
        
    }
    
}
