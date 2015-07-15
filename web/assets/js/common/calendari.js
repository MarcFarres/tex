
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
   },
  });
}


}()); //run this anonymous function immediately
