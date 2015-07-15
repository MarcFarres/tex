
(function(){

var months=4;
var calendar_width = 210*months;

var calendar_max_left = 0;
var calendar_min_left = -calendar_width + 210;

var calendar_position = 0;

$('#calendari_months_container').css('width',calendar_width+'px');


$('#calendari').on('click','.calendari-right',function(){
  if(calendar_position>calendar_min_left){
    calendar_position = calendar_position -210;
    $('#calendari_months_container').css('left',calendar_position+'px');
  }
})

$('#calendari').on('click','.calendari-left',function(){
  if(calendar_position<calendar_max_left){
    calendar_position = calendar_position +210;
    $('#calendari_months_container').css('left',calendar_position+'px');
  }
})

}()); //run this anonymous function immediately
