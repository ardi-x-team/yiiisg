$(function(){
  $('#loading').hide();

}).ajaxStart(function() {
  $('#loading').show();  

}).ajaxSuccess(function() {
  $('#loading').text('Success!').show();

}).ajaxError(function() {
  $('#loading').text('Woopsie... error!').show();
  
});
