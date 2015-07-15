
(function(){

// el contenedor mas general considerado


$( document ).ready(function(){ }); 
  $('#tests_table').dataTable({});

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

$('#main_parent').on('submit','#novaMesura', function(){
  
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

setTimeout(function() {
  var params2 = {
  "test_id" : $('#finalitzar_test').attr('data-resultatId'),
  "of_id" : $('#OF_list').val(),
  };

$.ajax({ 
  data:  params2,
  url:   finalitzar_test,
  type:  'post',
  dataType: "html",
  beforeSend: function () {
     
    } , 
  success:  function (response) {
    $('#NT_resultat_container').html(response);
  },
  });
}, 200);

return false;

});


function guardar_mesura(params){

}

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
  url:   ajax_route,
  type:  'post',
  beforeSend: function () {
      if(primera_mesura){
        //alert('esperi uns segons per a enviar mesures');
        primera_mesura = false;
      }
    } , 
  success:  function (response) {
    
    if(response == 'finish_process'){
      return 0;
    }
    
      var resultat_id = $('#realitzar_mesura').attr('data-resultatId');
      var valor = response;

      var params = {'resultat_id':resultat_id,'valor':valor};

    $.ajax({ 
      data:  params,
      url:   novamesura,
      type:  'post',
      dataType: "html",
      beforeSend: function () {
     
      } , 
      success:  function (response) {
      $('#mesures_body').append(response);
      setTimeout(llegir_mesura(), 100);
      },
    });
    /*
    $('#valor').attr('value',response); 
    setTimeout(llegir_mesura(), 500);
     */
  },
  });
}

// obrim la llista de tests oberts d'una familia

$('#families_maquines').on('click','.action-button', function(){

  var tipus = $(this).attr('data-tipus');
  tipus_selected = tipus;
  
  var parametros = {
    "tipus" : tipus_selected,
    'timeo' : timeo_selected,
  };

$.ajax({
  data:  parametros,
  url:   get_data_tests,
  type:  'post',
  beforeSend: function () {
    } , 
  success:  function (response) {
    
    $('#tests_contenedor').html(response);
    $('#tests_table').dataTable({});
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
      $('#TO_tests-container').html(response);
    },
  });

});


// obtenim un nou test per a una maquina 
// sense OF assignada

$('#main_parent').on('click','#nou_test', function(){

  var maquina_id = $(this).attr('data-maquina');
  var parametros = {
    "maquina_id" : maquina_id,
  };

  $.ajax({
    data:  parametros,
    url:   nou_test,
    type:  'post',
    beforeSend: function (){

    } , 
    success:  function (response) {
      $('#TO_tests-container').html(response);
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
        setTimeout(llegir_mesura(),200);
   },
  });

});


// Obrim els tests d'una data particular

$('#calendari').on('click','.gotests',function(){
 
timeo_selected = $(this).attr('data-timeo');

 if($(this).hasClass('clicked')){
  $(this).removeClass('clicked');
  timeo_selected = 0;
 }
 else{
  var clicked_element = $('.gotests.clicked') ;
    unclick_element(clicked_element);
    click_element($(this));
 }
  var parametros = {
      "timeo" : timeo_selected,
      "tipus" : tipus_selected,
    };

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


// -----------------------------
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


// [ Calendari ]=========================================


/*var calendar_width = 210*months;

$*/

}()); //run this anonymous function immediately
