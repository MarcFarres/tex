
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
    if($('#test_contenedor').hasClass('openedTests')) {
      $('#resultats_list_contenedor').html(response);
      $('#test_contenedor').html('');
    }
    else {$('#test_contenedor').html(response);}
  },
  });


return false;

});

// capturar les mesures de pes de la balança:

$('#test_contenedor').on('click','#realitzar_mesura', function(){
  llegir_mesura();
});

function llegir_mesura(){
  $.ajax({
  data:  parametros,
  url:   ajax_route,
  type:  'post',
  beforeSend: function () {
    } , 
  success:  function (response) {
    if(response = 'finish_process'){
      return 0;
    }
    $('#valor').attr('value',response); 
    setTimeout(llegir_mesura(), 500);
  },
  });
}

// obrim la llista de tests oberts d'una familia

$('#families_maquines').on('click','.action-button', function(){

  var tipus = $(this).attr('data-tipus');
  
  var parametros = {
    "tipus" : tipus,
  };

$.ajax({
  data:  parametros,
  url:   tipus_route,
  type:  'post',
  beforeSend: function () {
    } , 
  success:  function (response) {

    $('#resultats_list_contenedor').html(response);
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


// obrim un nou test desde el 'resultats list contenedor'

$('#resultats_list_contenedor').on('click','.gotest',function(){
  alert('hola');
  var parametros = {
      "resultat" : $(this).attr('data-resultat'),
    };
    
    var clicked_element = $(this).parent().parent().parent().find('.clicked') ;
    unclick_element(clicked_element);
    click_element($(this).parent().parent());

    $.ajax({
      data:  parametros,
      url:   goToTest,
      type:  'post',
      beforeSend: function () {
       // click_element($('.plegable_list'));
      } , 
      success:  function (response) {
         
        $('#test_contenedor').html(response);
   },
  });

});


// Obrim els tests d'una data particular

$('#calendari').on('click','.gotests',function(){
 
  var parametros = {
      "timeo" : $(this).attr('data-timeo'),
    };
    
    var clicked_element = $(this).parent().parent().parent().find('.clicked') ;
    unclick_element(clicked_element);
    click_element($(this).parent().parent());

    $.ajax({
      data:  parametros,
      url:   get_data_tests,
      type:  'post',
      beforeSend: function () {
       // click_element($('.plegable_list'));
      } , 
      success:  function (response) {
         
        $('#tests_contenedor').html(response);
        $('#tests_table').dataTable({ });
   },
  });

});


$('#resultats_list_contenedor').on('click','.borrar',function(){
 
  var parametros = {
      "resultat_id" : $(this).attr('data-resultat'),
      "maquina_id" : $(this).attr('data-maquina'),
      "id" : $(this).attr('data-of')
    };


    $.ajax({
      data:  parametros,
      url:   borrar_resultat,
      type:  'post',
      beforeSend: function () {
        click_element($('.plegable_list'));
      } , 
      success:  function (response) {
        $('#resultats_list_contenedor').html(response);
      // buidem la pantalla del test
      $('#test_contenedor').html('');
   },
  });

});



$('#test_contenedor').on('click','.borrar',function(){
 
  var parametros = {
      "mesura_id" : $(this).attr('data-mesura'),
      "resultat_id" : $(this).attr('data-resultat')
    };


    $.ajax({
      data:  parametros,
      url:   borrar_mesura,
      type:  'post',
      beforeSend: function () {
        
      } , 
      success:  function (response) {
        out_element($('#results_table'));
        
        $('#test_contenedor').html(response);
   },
  });

});

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


}()); //run this anonymous function immediately
