$(document).ready(function(){

  initAjaxSetup();

  initInputNumber();

  initDataTables();

});

function initAjaxSetup()
{
  $(document).ajaxStart(function(){
    $('.button-loading').addClass('state-loading');
    $('.button-loading').attr('disabled', 'disabled');
    //$('#ajax-spinner').removeClass('hide');
  });

  $(document).ajaxComplete(function(){
    $('.button-loading').removeClass('state-loading');
    $('.button-loading').attr('disabled', false);
    //$('#ajax-spinner').addClass('hide');
  });
  
}

function hideElement($elem)
{
  $elem.addClass('hide');
}

function showElement($elem)
{
  $elem.removeClass('hide');
}

function showMessageFlash(type, message)
{
  var html = '';
  var className = '';
  var classIcon = '';
  var title = '';
  switch (type) {
    case 'success':
      className = 'alert alert-success alert-dismissible';
      classIcon = 'icon fa fa-check';
      title = 'OK';
      break;
    case 'info':
      className = 'alert alert-info alert-dismissible';
      classIcon = 'icon fa fa-info';
      title = 'Info';
      break;
    case 'warning':
      className = 'alert alert-warning alert-dismissible';
      classIcon = 'icon fa fa-warning';
      title = 'Alerta';
      break;
    case 'error':
      className = 'alert alert-danger alert-dismissible';
      classIcon = 'icon fa fa-ban';
      title = 'Error';
      break;
  }

  html = '<div class="clear-fix">&nbsp;</div>';
  html += '<div class="' + className + '">';
  html += '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
  html += '<h4><i class="' + classIcon  + '"></i> ' + title + '</h4>';
  html += message;

  $('#div-mensajes').append(html);
}

/**
 * Modal simple para mostrar con un mensaje + boton cerrar
 * @param string message 
 * @param boolean isError 
 * @param function cerrarCallBack 
 */
function showSimpleModal(message, isError, cerrarCallBack)
{
  var color = '';
  var title = '';

  if (typeof isError == 'undefined') {
    isError = false;
  }

  if (typeof cerrarCallBack == 'undefined') {
    cerrarCallBack = null;
  }
  
  if (isError) {
    color = 'style="background: red;"';
    title = 'Error';
  } else {
    color = 'style="background: #13B9FF;"';
    title = 'OK';
  }

  var html = '<div class="modal" id="miModal" tabindex="-1">' + 
	  '<div class="modal-dialog">' + 
		'<div class="modal-content">' + 
		  '<div class="modal-header">' + 
			'<h5 class="modal-title">Modal title</h5>' + 
			'<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>' + 
		  '</div>' +
		  '<div class="modal-body">' +
			'<p>' + message + '</p>' + 
		  '</div>' + 
		  '<div class="modal-footer">' + 
			'<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>' +
		  '</div>' + 
		'</div>' + 
	  '</div>' + 
	'</div>';

  $('#modales').html(html);
  $('#miModal').modal('show');
  if (cerrarCallBack != null) {
    $('#btn-cerrar-simple-modal').off('click').on('click', cerrarCallBack);
  }
}

function showBorrarModal(options)
{
  var color = 'style="background: red;"';
  var title = 'Borrar';
  
  var html = '<div id="miModal" class="modal fade" role="dialog">' + 
  '<div class="modal-dialog">' + 
    '<div class="modal-content">' +  
      '<div class="modal-header" ' +  color + '><strong>' +  title + '</strong>' +
        '<button type="button" class="close pull-right" data-dismiss="modal">&times;</button><div ' + color + '></div>' + 
      '</div>' + 
      '<div class="modal-body">' + 
        '<p>' + options.message + '</p>' + 
      '</div>' + 
      '<div class="modal-footer">' + 
        '<button type="button" class="btn btn-primary pull-left" data-dismiss="modal">Cancelar</button>' + 
        '<button type="button" id="btn-borrar-modal" class="btn btn-danger pull-right button-loading">Borrar</button>' + 
      '</div>' + 
    '</div>' + 
  '</div>' + 
'</div>';

  $('#modales').html(html);
  $('#miModal').modal('show');
  $('#btn-borrar-modal').off('click').on('click', options.borrarCallback);
}


function showModal(options)
{
  var color = 'style="background: #13B9FF;"';
  var title = options.title ? options.title : 'OK';

  var classSize = '';
  if (typeof options.size != 'undefined') {
    switch(options.size) {
        case 'small':
          classSize = 'modal-sm';
          break;

        case 'large':
          classSize = 'modal-lg';
          break;

        case 'extra-large':
          classSize = 'modal-xl';
          break;
    }
  } 
   
  var html = '<div id="miModal" class="modal fade" role="dialog">' + 
   '<div class="modal-dialog ' + classSize + '">' + 
     '<div class="modal-content">' +  
       '<div class="modal-header" ' +  color + '><strong>' +  title + '</strong>' +
         '<button type="button" class="close pull-right" data-dismiss="modal">&times;</button><div ' + color + '></div>' + 
       '</div>' + 
       '<div class="modal-body">' + 
         '<p>' + options.message + '</p>' + 
       '</div>' + 
       '<div class="modal-footer">' + 
         '<button type="button" class="btn btn-primary pull-left" data-dismiss="modal" id="btn-cerrar-modal">Cerrar</button>';

          if (options.aceptarButtonCallback != null) {
            options.aceptarButtonTitle = options.aceptarButtonTitle == null ? 'Aceptar' : options.aceptarButtonTitle;
            html += '<button type="button" class="btn btn-primary pull-right" id="btn-aceptar-modal">' + options.aceptarButtonTitle + '</button>';
          }

         html += '</div>' + 
     '</div>' + 
   '</div>' + 
 '</div>';
 
   $('#modales').html(html);
   $('#miModal').modal('show');
   if (options.aceptarButtonCallback != null) {
     $('#btn-aceptar-modal').off('click').on('click', options.aceptarButtonCallback);
   }

   return $('#miModal');
}

function modalClose()
{
  $('#miModal').modal('hide');
}

function getSelectedIdsForSelect($elem)
{
  var options = $elem.find('option');
  var selectedIds = [];
  options.each(function(key, value) { 
      if ($(value).is(':selected')) {
        var value = $(value).val().trim();
        if (value != '' && typeof value != 'undefined') {
          selectedIds.push(value);
        }
      }
  });

  return selectedIds;
}

function validarLatitudLongitud(lat, lng) 
{ 
  if (typeof lat == 'undefined' || typeof lng == 'undefined') {
    return false;
  }

  lat = lat.trim();
  lng = lng.trim();

  let pattern = new RegExp('^-?([1-8]?[1-9]|[1-9]0)\\.{1}\\d{1,6}');
  
  return pattern.test(lat) && pattern.test(lng);
}

function initInputNumber()
{
  $('.number').each(function() {
    $(this).bind('paste keydown', function (e) {
      return isNumeric(e);
    });
  });
}

function isNumeric(event)
{
  var key = event.charCode || event.keyCode || 0;
  // allow backspace, tab, delete, enter, arrows, numbers and keypad numbers ONLY
  // home, end, period, and numpad decimal
  return (
      key == 8 || 
      key == 9 ||
      key == 13 ||
      key == 46 ||
      key == 110 ||
      key == 190 ||
      (key >= 35 && key <= 40) ||
      (key >= 48 && key <= 57) ||
      (key >= 96 && key <= 105)
  );
}

function initDataTables()
{
  $('.dt').each(function() {
    $(this).DataTable({
      "language": {
        "processing": "Procesando...",
        "lengthMenu": "Registros a mostrar: _MENU_",
        "zeroRecords": "No se encontraron resultados",
        "emptyTable": "Ningún dato disponible en esta tabla",
        "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
        "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
        "infoFiltered": "(filtrado de un total de _MAX_ registros)",
        "infoPostFix": "",
        "search": "Buscar:",
        "url": "",
        "thousands": ".",
        "loadingRecords": "Cargando...",
        "paginate": {
            "first": "Primero",
            "last": "Último",
            "next": "Siguiente",
            "previous": "Anterior"
        },
        "aria": {
            "sortAscending": ": Activar para ordenar la columna de manera ascendente",
            "sortDescending": ": Activar para ordenar la columna de manera descendente"
        }
      },
      "columnDefs": [],
      "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
      "order": [],
    });
  });
}