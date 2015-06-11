
(function(){

// el contenedor mas general considerado
var main_parent = $('#main_parent');

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
$('#test_contenedor').on('click','.plegable_form.unclicked',function(){

  // expansión del contenido
  in_element($(this).parent().parent().children('.plegable'));
});

// fade out content
$('#test_contenedor').on('click','.plegable_form.clicked',function(){

  // desparición del contenido
  out_element($(this).parent().parent().children('.plegable'));
});

// listas ocultables
main_parent.on('click','.plegable_list.unclicked',function(){

  out_element($(this).parent().parent().children('.plegable'));
})

main_parent.on('click','.plegable_list.clicked',function(){
  
  in_element($(this).parent().parent().children('.plegable'));
})


/* ------------------------------[ AJAX ]  */
// ================================================================

// guardar una nova mesura de pes capturada

$('#test_contenedor').on('submit','#novaMesura', function(){
  
  var params = $(this).serialize() ;

$.ajax({ 
  data:  params,
  url:   novamesura,
  type:  'post',
  dataType: "html",
  beforeSend: function () {
     
    } , 
  success:  function (response) {
    $('#mesures_body').append(response);
  },
  });


return false;

});


// finalitzem un resultat

$('#test_contenedor').on('submit','#endResult', function(){
  
  var params = $(this).serialize() ;

$.ajax({ 
  data:  params,
  url:   finalitzar_resultat,
  type:  'post',
  dataType: "html",
  beforeSend: function () {
     
    } , 
  success:  function (response) {
    $('#test_contenedor').html(response);
  },
  });


return false;

});

// capturar les mesures de pes de la balança:

$('#test_contenedor').on('click','#realitzar_mesura', function(){

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

// generar un nou test

$('#resultats_list_contenedor').on('click','#new_result', function(){

  var OF_id = $(this).attr('data-ofId');
  var maquina_id = $(this).attr('data-maquinaId');
  var parametros ="OF_id=" + OF_id + "&maquina_id=" + maquina_id ;

  $.ajax({
    data:  parametros,
    url:   nou_resultat,
    type:  'post',
    beforeSend: function () {} , 
    success:  function (response) {
      $('#test_contenedor').html(response);
    },
  });

});


// carregar els tests d'una màquina

$('.results_list').on('click', function(){

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
      $('#resultats_list_contenedor').html(response);
      // buidem la pantalla del test
      $('#test_contenedor').html('');
   },
  });

});


$('#resultats_list_contenedor').on('click','.gotest',function(){
 
  var parametros = {
      "resultat" : $(this).attr('data-resultat'),
    };


    $.ajax({
      data:  parametros,
      url:   goToTest,
      type:  'post',
      beforeSend: function () {
        click_element($('.plegable_list'));
      } , 
      success:  function (response) {
        // desaparece la lista de rsultados
        out_element($('#results_table'));
        
        $('#test_contenedor').html(response);
   },
  });

});

/* ------------------------------[ helper functions: ]  */
// ================================================================


// support a les interfaces css 'clicked / unclicked' i 'in / out'

main_parent.on('click','.unclicked',function(){click_element($(this));});
main_parent.on('click','.clicked',function(){unclick_element($(this));});
main_parent.on('click','.clickable',function(){$(this).removeClass('.clickable_done').addClass('clickable_done')})

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
