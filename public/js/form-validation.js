$(document).ready(function() {
	
	$('#chk-mepa').on('click', function() {
		$('#payment').val('mepa');
	});
	
	$('#chk-efectvo').on('click', function() {
		$('#payment').val('efectivo');
	});
	
	$('#chk-td').on('click', function() {
		$('#payment').val('transferencia');
	});
});


// Example starter JavaScript for disabling form submissions if there are invalid fields
(function () {
  'use strict'
  
  // Fetch all the forms we want to apply custom Bootstrap validation styles to
  var forms = document.querySelectorAll('.needs-validation')

  // Loop over them and prevent submission
Array.prototype.slice.call(forms)
	.forEach(function (form) {
		form.addEventListener('submit', function (event) {
			event.preventDefault();
			event.stopPropagation();
			
			var monto = $('#monto').val();
				
			if (monto < 200) {
				alert("El monto tiene que ser mayor a $200.");
				return false;
			}
			if (form.checkValidity()) {
				
				
          
				var data = $('form[name=register-form]').serializeArray();
				
				$.ajax({
					url: window.location.pathname + '/register',
					data: data,
					type: 'post',
					dataType: 'json',
					success: function(response) {
						if (response.status == 'ok') {
							window.location.href = response.href;
						} else {
							alert("Ha ocurrido un error: " + response.message);
						}
					}, 
					error: function(error) {
						alert(error);
					}
				});
			}
		
			form.classList.add('was-validated');
			
		}, false)
    })
})()