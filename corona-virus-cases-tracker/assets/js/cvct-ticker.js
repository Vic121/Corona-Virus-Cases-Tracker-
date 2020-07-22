jQuery(document).ready(function($){
  
   $('.cvct-ticker').each(function(index){
   var style = $(this).data('ticker-style');
   var ticker_position_cls = $('.cvct-ticker').data('ticker-position-cls');

   
   //class for header and footer position
   $('body').addClass(ticker_position_cls); 

   if(style =='style-1'){
    $(".cvct-tooltip").not(".tooltipstered").tooltipster({
        animation: "fade",
        contentCloning: true,
        contentAsHTML: true,
        interactive: true,
        delayTouch:[200,200]
    }); 
   }

   if(style =='style-2'){
       if(ticker_position_cls == 'cvct-ticker-top'){
        $(this).find('.cvct-close-button').click(function(){  
                $('body').css('margin-top', '0px');
                $(".cvct-ticker-style-2").slideUp(); 
                $('.cvct-show-button').show();
            }); 

            $('.cvct-show-button').click(function(){           
                $('.cvct-show-button').hide();
                $('body').css('margin-top', '90px');
                $(".cvct-ticker-style-2").slideDown();       
            });
       }
       if(ticker_position_cls == 'cvct-ticker-bottom') {
        $(this).find('.cvct-close-button').click(function(){  
                $('body').css('margin-bottom', '0px');
                $(".cvct-ticker-style-2").slideUp(); 
                $('.cvct-show-button').show();
            }); 

            $('.cvct-show-button').click(function(){           
                $('.cvct-show-button').hide();
                $('body').css('margin-bottom', '90px');
                $(".cvct-ticker-style-2").slideDown();       
            });
       }
    }

    if(style =='style-3'){
        $(this).find('.cvct-close-button').click(function(){ 
            //$('body').css('padding-bottom', '0px');
            $(".cvct-ticker-style-3").slideUp(); 
           $('.cvct-show-button').show();
        }); 

        $('.cvct-show-button').click(function(){           
            $('.cvct-show-button').hide();
            $(".cvct-ticker-style-3").slideDown();       
        });

    }
 
});

});