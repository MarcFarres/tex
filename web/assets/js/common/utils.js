
(function(){
/*
   Índice de contenidos:
   --------------------
   
   . variables globales
   . helper functions

*/


/* ------------------------------[ variables globales: ]  */
/* =======================================================*/



/* -----------------------------------------------------  */




$( document ).ready(function(){ });

  
  $('#tests_table').dataTable({


  });

   $('#tests_table_filter').children('label').addClass('fa fa-search');

// Efectos del intranet menu lateral
// =======================================================

// coloreado de los iconos on hover

$('.list_element').on({
  'mouseenter':function(){
  in_element($(this).children('.list_icon'));
},
  'mouseleave':function(){
  out_element($(this).children('.list_icon'));
}});


// flecha de expansión de contenido

// fade in content
$('#intranet_menu_lat').on('click','.unclicked',function(){
  // animación de la flecha
  click_element($(this));
  // expansión del contenido
  in_element($(this).parent().children('.sublist_container'));
});

// fade out content
$('#intranet_menu_lat').on('click','.clicked',function(){
  // animación de la flecha
  unclick_element($(this));
  // desparición del contenido
  out_element($(this).parent().children('.sublist_container'));
});



// formularios ocultables
// fade in content
$('.intranet-form').on('click','.unclicked',function(){
  // animación de la flecha
  click_element($(this));
  // expansión del contenido
  in_element($(this).parent().parent().children('.plegable'));
});

// fade out content
$('.intranet-form').on('click','.clicked',function(){
  // animación de la flecha
  unclick_element($(this));
  // desparición del contenido
  out_element($(this).parent().parent().children('.plegable'));
});


/* ------------------------------[ AJAX ]  */
// ================================================================


// capturar les mesures de pes de la balança:

$('#realitzar_mesura').on('click', function(){

  var Id = $(this).attr('data-resultatId');
  var container = $(this);

  var parametros = {
          "valor1" : 'hola',
          "valor2" : 'buenas'
        };

$.ajax({
  data:  parametros,
  url:   ajax_route,
  type:  'post',
  beforeSend: function () {
    $('#valor').attr('Leyendo datos ... '); 
    } , 
  success:  function (response) {
    alert(response);
    $('#valor').attr('value',response);
  },
  });

});


// carregar els tests d'una màquina


$('.new_test').on('click', function(){

  var parametros = {
      "maquina_id" : $(this).attr('data-maquina'),
      "id" : $(this).attr('data-of')
    };

    if($('.maquina_actual'))$('.maquina_actual').removeClass('maquina_actual');
    $(this).parent().parent().addClass('maquina_actual');


    $.ajax({
      data:  parametros,
      url:   maquina_route,
      type:  'post',
      beforeSend: function () {
        
      } , 
      success:  function (response) {
      $('#test_contenedor').html(response);
   },
  });

});

/* ------------------------------[ helper functions: ]  */
// ================================================================

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


}()); //run this anonymous function immediately
