$(function(){
  $('#loading').hide();

}).ajaxStart(function() {
  $('#loading').show();  

}).ajaxSuccess(function() {
  $('#loading').hide();  

}).ajaxError(function() {
  $('#loading').text('Woopsie... error!').show();
  
});
