
/*[ variables globales: ]*/
//================================================================

var main_parent = $('#main_parent');
var tipus_selected = 0;
var timeo_selected = 0;
var mesura_num = 0;

/* ------------------------------[ helper functions: ]  */
// ================================================================
// support a les interfaces css 'clicked / unclicked' i 'in / out'

main_parent.on('click','.unclicked',function(){if(!$(this).hasClass('clickedException'))click_element($(this));});
main_parent.on('click','.clicked',function(){if(!$(this).hasClass('clickedException'))unclick_element($(this));});
main_parent.on('click','.clickable',function(){$(this).removeClass('.clickable_done').addClass('clickable_done')})


$('.action-button').on('click',function(){

   $('.action-button.clicked').removeClass('clicked').addClass('unclicked');
})
function in_element(element){
  element.removeClass('out');
  element.addClass('in');
}

function out_element(element){   
  element.removeClass('in');  
  element.addClass('out');
}

function click_element(element){
  element.removeClass('unclicked');
  element.addClass('clicked');
}

function unclick_element(element){
  element.removeClass('clicked');
  element.addClass('unclicked');
}

function resultats_modifications(){
	$('#tests_table_filter').addClass('fa fa-search').css({
          'position':'fixed',
          'top':'55px',
          'right':'400px',
          'z-index':'50',
        });
        $('#tests_table_length').addClass('fa fa-file-text').css({
          'position':'fixed',
          'top':'55px',
          'left':'500px',
          'z-index':'50',
        });

        $('.layout-content-menu').css('min-height','180px')
}