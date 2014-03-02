/*
 * All the plugins init are in this file
 **/
var map;
$(document).ready(function() {
  
  // activate the second carousel
  $('#slider-carousel').carousel();
  $('#testimonials-carousel').carousel();
  
  // init the google map plugin
  map = new GMaps({
    el: '#map',
    lat: 42.585444,
    lng: 12.891598,
    zoom: 7,
  });
  map.addMarker({
    lat: 42.954664,
    lng: 13.873276,
    title: 'Wedding',
    open: 'true',
    infoWindow: {
        content: '<p>Wedding is here close to San Benedetto del Tronto!</p>'
      }
  });
  map.setContextMenu({
    control: 'map',
    options: [{
      title: 'Add marker',
      name: 'add_marker',
      action: function(e){
        this.addMarker({
          lat: e.latLng.lat(),
          lng: e.latLng.lng(),
          title: 'New marker'
        });
        this.hideContextMenu();
      }
    }, {
      title: 'Center here',
      name: 'center_here',
      action: function(e){
        this.setCenter(e.latLng.lat(), e.latLng.lng());
      }
    }]
  });
  map.setContextMenu({
    control: 'marker',
    options: [{
      title: 'Center here',
      name: 'center_here',
      action: function(e){
        this.setCenter(e.latLng.lat(), e.latLng.lng());
      }
    }]
  });

  
  // sliding contact form
  $('.contact-form-btn').click( function(){
    if($(this).hasClass('closes')) {
      $('.contact-form-inner').slideDown();
      $(this).removeClass('closes').addClass('open');
    } else {
      $('.contact-form-inner').slideUp();
       $(this).removeClass('open').addClass('closes');
    }
  });
  
  // ajax contact form
  $('#contact-form').submit(function(){
    $.post('contact-form.php', $(this).serialize(), function(data){
      $('#contact-form').html(data);
      $('#contact-form input, #contact-form textarea').val('');
    });				
    return false;
  });

  // ajax subscription
  $('#subsc-form').submit(function(){
    $.post('subscription.php', $(this).serialize(), function(data){
    
      $('.subscribe-wrapper > *').fadeIn();
      $('.subscribe-wrapper').html(data);
      $('#subsc-form input').val('');
    });				
    return false;
  });

});