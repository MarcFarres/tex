

var actual_left =-350;
var actual_date = 0;
var max_left = -50;
var min_left = -350;

// variables usadas para recuperar los tests
var actual_tipus = false;
var actual_maquina = false;
var actual_of = false;
var the_date = $('.last-week_day').eq(0).attr('data-time');

// altura máxima para el contenedor de los tests
var max_height = $('#TO_tipus-menu-container').css('height');

$('#TO_tests-container').css({
  'max-height':max_height,
  'overflow-y':'scroll',
});

$('.TO_familia-row.unclicked').click(function(){
  unclick_element($('.TO_familia-row.clicked'));
})
$('#TO_maquines-menu-container').on('click','.maquina-of-type.unclicked',function(){
  unclick_element($('.maquina-of-type.clicked'));
})

// recuperem els tests d'una màquina per a un dia concret
// Test per MÀQUINA
$('#TO_maquines-menu-container').on('click','.maquina-of-type',function(){
  actual_maquina = $(this).attr('data-id');
  llegir_mesura_var = false;
  // activem el botó
  in_element($('#nou_test'));
  // coloquem la llista de OF a totes
  $('#OF_list_menu').val('false');
  actual_of = false;
  recover_tests();
});

// recuperem les maquines d'un tipus determinat
// tests per TIPUS
$('#TO_tipus-menu-container').on('click','.TO_familia-row', function(){

  var tipus = $(this).attr('data-tipus');
  actual_tipus = tipus;
  mesura_num = 0;
  // desactivem la captacio de mesures
  llegir_mesura_var = false;

  var parametros1 = {
    "tipus" : tipus,
  };

  var parametros2 = {
    "tipus" : tipus,
    "data" : the_date,
    "of" : actual_of,
  };

  // desactivem el botó 'nou test'
  out_element($('#nou_test'));
  // desactivem la maquina actual
  actual_maquina = false;
  unclick_element($('.maquina-of-type.clicked'));

// omplim el menú de les maquines
$.ajax({
  data:  parametros1,
  url:   maquines_list,
  type:  'post',
  beforeSend: function () {
    } , 
  success:  function (response) {
    
    $('#TO_maquines-menu-container').html(response);
  },
  });


// borrar mesura
$('#main_parent').on('click','.borrar',function(){
 
  var parametros = {
      "mesura_id" : $(this).attr('data-mesura'),
      "resultat_id" : $(this).attr('data-resultat')
    };
  var mesura_row = $(this).parent().parent();
  

    $.ajax({
      data:  parametros,
      url:   borrar_mesura,
      type:  'post',
      beforeSend: function () {
        
      } , 
      success:  function(response) {
      
        mesura_row.remove();
      },

  }).fail( function( jqXHR, textStatus, errorThrown ) {

        if (jqXHR.status === 0) {
    
            alert('Not connect: Verify Network.');

        } else if (jqXHR.status == 404) {

            alert('Requested page not found [404]');

        } else if (jqXHR.status == 500) {

            alert('Internal Server Error [500].');

        } else if (textStatus === 'parsererror') {

            alert('Requested JSON parse failed.');

        } else if (textStatus === 'timeout') {

            alert('Time out error.');

        } else if (textStatus === 'abort') {

            alert('Ajax request aborted.');

        } else {

            alert('Uncaught Error: ' + jqXHR.responseText);

        }

    });

});


  // els tests d'un tipus
  $.ajax({
  data:  parametros2,
  url:   tests_of_type_list,
  type:  'post',
  beforeSend: function () {
    } , 
  success:  function (response) { 
    $('#TO_tests-container').html(response);
    actualize_consulta_info();
  },
  });
});


// click en el calendari setmanal
$('#main_parent').on('click','.last-week_day', function(){
  the_date = $(this).attr('data-time');
  llegir_mesura_var = false;

  if(actual_maquina)recover_tests();
});

// movemos una fecha hacia la izquierda en el calendario semanal
$('#main_parent').on('click','.last-week_arrow.left',function(){
  var new_left = actual_left + 50;
 
  if(actual_left<=max_left){

    actual_left = new_left;
    actual_date = actual_date + 1;
    $('#last-week_days').css('left',actual_left+'px');
    the_date = $('.last-week_day').eq(actual_date).attr('data-time');
    
    recover_tests();
    }
  }) 

// movemos una fecha hacia la derecha en el calendario semanal
$('#main_parent').on('click','.last-week_arrow.right',function(){
  var new_left = actual_left - 50;
  
  if(actual_left>min_left){
    actual_left = new_left;
    actual_date = actual_date - 1;
    $('#last-week_days').css('left',actual_left+'px');
    the_date = $('.last-week_day').eq(actual_date).attr('data-time');
  
    recover_tests();
  }
})


// seleccionamos una OF desde el menú superior
$('#main_parent').on('change','#OF_list_menu',function(){
  actual_of = $(this).val() ;

  // desactivem el botó 'nou test'
  out_element($('#nou_test'));
  // desactivem la maquina actual
  actual_maquina = false;
  unclick_element($('.maquina-of-type.clicked'));
  // recuperem els tests
  recover_tests();
})


function recover_tests(){
  
  mesura_num = 0;
  
  if(actual_maquina){
    // maquina y fecha
    var parametros = {
      "maquina_id" : actual_maquina,
      "data":the_date,
    };
    
    // pasem la id al botó "nou resultat"
    $('#nou_test').attr('data-maquina',actual_maquina);
  
    $.ajax({
      data:  parametros,
      url:   maquina_tests,
      type:  'post',
      beforeSend: function () {
       
      } , 
      success:  function (response) {
         
        $('#TO_tests-container').html(response);
        actualize_consulta_info(response);
        
      },
    });
  } // if actual maquina

  if(!actual_maquina){
    // tipus y fecha
    var parametros = {
      "tipus" : actual_tipus,
      "data":the_date,
      "of":actual_of,
    };


  $.ajax({
      data:  parametros,
      url:   tests_of_type_list,
      type:  'post',
      beforeSend: function () {
      } , 
      success:  function (response) {
         
        $('#TO_tests-container').html(response);
        actualize_consulta_info(response);   
      },
    });
  }
}


function actualize_consulta_info(response){
  
  var info_familia = $('#info_familia').html();
  var info_maquina = $('#info_maquina').html();
  var info_data = $('#info_data').html();
  var info_OF = $('#info_OF').html();
  
  

  //if(actual_tipus==false){info_familia = 'totes';}
  $('#consulta-info_familia').html(info_familia);

  //if(actual_maquina==false){info_maquina = 'totes';}
  $('#consulta-info_maquina').html(info_maquina);

  //if(the_date==false){info_data = 'totes';}
  $('#consulta-info_data').html(info_data);

  //if(actual_of==false){info_OF = 'totes';}
  $('#consulta-info_OF').html(info_OF);
}



// finalitzem un test !!  
$('#TO_tests-container').on('click','#finalitzar_test',function(){
 
  var parametros = {
    "test_id" : $(this).attr('data-resultatId'),
    "of_id" : $('#OF_list').val(),
  };
 

    $.ajax({
      data:  parametros,
      url:   finalitzar_test,
      type:  'post',
      beforeSend: function () {
      } , 
      success:  function (response) {
        $('#NT_resultat_container').html(response);
      },
    });

});


// guardar una nova mesura de pes capturada

$('#main_parent').on('submit','#novaMesura', function(){
  
  var params = $(this).serialize() ;

  guardar_mesura(params);
  return false;

});

// SISTEMA DE CAPTACIÓ DE DADES
// ------------------------------------------------------------------------------
var llegir_mesura_var = false;
// obtenim un nou test per a una maquina 
// sense OF assignada

$('#main_parent').on('click','#nou_test', function(){

  var maquina_id = $(this).attr('data-maquina');
  var parametros = {
    "maquina_id" : maquina_id,
  };
  
   llegir_mesura_var = true;
      // al obrir el test ens disposem a captar les mesures de la balança
      llegir_mesura();
      
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


// editar un test ja realitzat:
$('#main_parent').on('click','#edit_test', function(){
  //activem la presa de dades
  llegir_mesura_var = true;
  llegir_mesura();

  var maquina_id = $(this).attr('data-maquina');
  var test_id = $(this).attr('data-test');
  var parametros = {
    "maquina_id" : maquina_id,
    "test_id" : test_id,
  };

  $.ajax({
    data:  parametros,
    url:   edit_test,
    type:  'post',
    beforeSend: function (){

    } , 
    success:  function (response) {
      $('#TO_tests-container').html(response);
      llegir_mesura_var = true;
      // al obrir el test ens disposem a captar les mesures de la balança
      llegir_mesura();
    },
  });

});

// capturar les mesures de pes de la balança:
// =========================================================

$('#main_parent').on('click','#realitzar_mesura', function(){
  // llegim les mesures de la balança
  llegir_mesura();
});


function llegir_mesura(){

if(llegir_mesura_var){

  $.ajax({
  url:   ajax_route,
  type:  'post',
  beforeSend: function () {
   
    } , 
  success:  function(response){
      if(response == '0' || !response){
        if( llegir_mesura_var ){
          llegir_mesura();
        }else{
          return false;
        }
        
      }else{
        if(llegir_mesura_var){
          var resultat_id = $('#realitzar_mesura').attr('data-resultatId');
          var valor = response;
          var params = {'resultat_id':resultat_id,'valor':valor};
          guardar_mesura(params);
          llegir_mesura();
        }

      }
    },
  });

} // if

}


function guardar_mesura(params){
  
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

mesura_num = mesura_num+1;

if(mesura_num > 2){
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
    }).fail( function( jqXHR, textStatus, errorThrown ) {

        if (jqXHR.status === 0) {
    
            alert('Not connect: Verify Network.');

        } else if (jqXHR.status == 404) {

            alert('Requested page not found [404]');

        } else if (jqXHR.status == 500) {

            alert('Debes seleccionar una OF para el test');

        } else if (textStatus === 'parsererror') {

            alert('Requested JSON parse failed.');

        } else if (textStatus === 'timeout') {

            alert('Time out error.');

        } else if (textStatus === 'abort') {

            alert('Ajax request aborted.');

        } else {

            alert('Uncaught Error: ' + jqXHR.responseText);

        }

    });

  }, 200);
} // if mesura_num
}

 

$('#main_parent').on('dblclick',".test-box_info-row",function(){
 
  var parametros = {
      "resultat_id" : $(this).attr('data-resultat'),
      "maquina_id" : $(this).attr('data-maquina'),
      "id" : $(this).attr('data-of')
    };

    var row = $(this).parent();


    $.ajax({
      data:  parametros,
      url:   borrar_resultat,
      type:  'post',
      beforeSend: function () {
      } , 
      success:  function (response) {
        row.remove();
   },
  });

});

(function(){

$.datepicker.regional['es'] = {
 closeText: 'Cerrar',
 prevText: '<Ant',
 nextText: 'Sig>',
 currentText: 'Hoy',
 monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
 monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
 dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
 dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
 dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
 weekHeader: 'Sm',
 dateFormat: 'dd/mm/yy',
 firstDay: 1,
 isRTL: false,
 showMonthAfterYear: false,
 yearSuffix: ''
 };

$.datepicker.setDefaults($.datepicker.regional["es"]);
$('#calendari_slider_frame').datepicker({
  onSelect: function (date) {
    getTestsOfDate(date);
  },
  dateFormat: 'yy-mm-dd',
}); 

function getTestsOfDate(data){
  var parametros = {
      "timeo" : data,
      "tipus" : tipus_selected,
    };

    timeo_selected = data;

    $.ajax({
      data:  parametros,
      url:   get_data_tests,
      type:  'post',
      beforeSend: function () {
       // click_element($('.plegable_list'));
      } , 
      success:  function (response) {
         
        $('#tests_contenedor').html(response);
        $('#tests_table').dataTable({ 
        // los tests mas recientes primero
        "order": [ 0, 'desc' ],
        });

        resultats_modifications();
   },
  });
}




}()); //run this anonymous function immediately
