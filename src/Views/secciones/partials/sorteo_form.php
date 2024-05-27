<div class="col-md-12">
  <div class="row">
    <p class="lead">Si queres apoyar a esta organización, podes hacer un aporte monetario y automaticamente estaras participando en el sorteo de los recaudado. <a href="#" data-fancybox data-src="#dialog-content">Leer bases y condiciones.</a></p>
  </div>
</div>
<br/>
<div class="col-md-12">
<h4 class="mb-3">Completa tus datos</h4>
<form name="register-form" id="register-form" class="needs-validation" novalidate>

  <div class="row g-3">
    <div class="col-sm-6">
      <label for="firstName" class="form-label">Nombres (*)</label>
      <input type="text" class="form-control" name="firstName" id="firstName" placeholder="" value="" maxlength="255" required>
      <div class="invalid-feedback">
        El nombre es obligatorio.
      </div>
    </div>

    <div class="col-sm-6">
      <label for="lastName" class="form-label">Apellidos (*)</label>
      <input type="text" class="form-control" name="lastName" id="lastName" placeholder="" value="" maxlength="255" required>
      <div class="invalid-feedback">
        El apellido es obligatorio.
      </div>
    </div>
    
          <div class="col-sm-6">
      <label for="dni" class="form-label">DNI (*)</label>
      <input type="text" class="form-control number" name="dni" id="dni" placeholder="" value="" maxlength="11" required>
      <div class="invalid-feedback">
        El DNI es obligatorio.
      </div>
    </div>

    <div class="col-sm-6">
      <label for="email" class="form-label">Email (*)</label>
      <input type="email" class="form-control" name="email" id="email" placeholder="" maxlength="255" required>
      <div class="invalid-feedback">
        El email es obligatorio.
      </div>
    </div>
    
          <div class="col-sm-6">
      <label for="numero-telefono" class="form-label">Teléfono <span class="text-muted">(Opcional)</span></label>
      <input type="text" class="form-control" name="numero_telefono" id="numero-telefono" placeholder="" maxlength="100">
    </div>
    
          <div class="col-sm-6">
      <label for="monto" class="form-label">Monto (*) <span class="text-muted">(Mínimo $200 ARS)</span></label>
      <input type="text" class="form-control number" name="monto" id="monto" placeholder="" maxlength="13" required>
            <div class="invalid-feedback">
        El monto es obligatorio y tiene que ser mayor a $200.
      </div>
    </div>

          <hr class="my-4">

  <div class="form-check">
    <input type="checkbox" class="form-check-input" name="terminos-condiciones" id="terminos-condiciones" required>
    <label class="form-check-label" for="terminos-condiciones">He leido los terminos y condiciones del sorteo.</label>
  </div>

  <hr class="my-4">
  
      <h4 class="mb-3">Métodos de pago</h4>
  
  <div class="form-check">
        <input type="radio" class="form-check-input chk-payments" name="payments[]" id="chk-mepa" required>
        <label class="form-check-label" for="mepa">Mercado Pago <span class="text-muted">(Será redirigido al sitio de Mercado Pago)</span></label>
  </div>
  
  <div class="form-check">
  <input type="radio" class="form-check-input chk-payments" name="payments[]" id="chk-efectvo" required>
        <label class="form-check-label" for="efe">Efectivo</label>
      </div>
  
  <!--
  <div class="form-check">
    <input type="radio" class="form-check-input chk-payments" name="payments[]" id="chk-tb" required>
    <label class="form-check-label" for="tb">Transferencia bancaria <span class="text-muted">(CBU: 012345678901234567890 - Alias: sarasa)</span></label>
  </div>
  -->
  
  <div class="form-check">
    <input type="radio" class="form-check-input chk-payments" name="payments[]" id="chk-td" required>
    <label class="form-check-label" for="td">Transferencia digital <span class="text-muted">(CVU: <?php echo $config['cvu'] ?> - Alias: <?php echo $config['cvu_alias'] ?>)</span></label>
  </div>
  
  <hr class="my-4">
  
  <div class="col-sm-6">
    <div class="g-recaptcha" data-sitekey="6Ldk_l4UAAAAANfcV2MsS4hKLrtJoLpcAumRaGjh"></div>
  </div>
  
  <hr class="my-4">

  <button class="w-100 btn btn-primary btn-lg button-loading" id="btn-mepa-payment" type="submit">Pagar</button>		  
  
  <input type="hidden" id="payment" name="payment" value="" />
  
</div>

</form>
</div>

<div id="dialog-content" style="display:none;">
	<h2>Bases y condiciones</h2>
	<ul>
		<li>Cada participante, deberá completar su nombre, apellido, número de documento, email y el monto, para poder participar del sorteo.</li>
		<li>El monto mínimo para poder participar del sorteo es de 200 (docientos) pesos argentinos.</li>
		<li>Se podrá participar una vez por cada sorteo realizado y la página le asignará un número para el sorteo.</li>
		<li>Cada participante, podrá aportar varios pagos, pero solo se le asignará un número de sorteo para equidad de todos los participantes.</li>
		<li>Se le asignará un número para el sorteo y se sorteará por loteria nacional, en la fecha y lotería que será notificada por los organizadores.</li>
		<li>En el caso, de que haya un ganador, el mismo se ganara un ?% del pozo recaudado para ese sorteo.</li>
		<li>En el caso, de que no haya ganador, el pozo, pasará como aporte para la organización y el ?% del mismo se le sumará para el próximo sorteo como pozo acumulado.</li>
		<li>La forma de pago del premio para el ganador/a, será acordado por el participante y los organizadores, que podrá ser mediante transferencia bancaria/digital.</li> 
		<li>El/la ganador/a será notificado en la página en la sección "ganadores del sorteo".</li>
		<li>Los datos guardados, se trataran de acuerdo a la ley 25.326 de protección de datos personales.</li>
		<li>La página cuenta con protocolo de seguridad HTTPS, que cifra sus datos al enviar y recibir los mismos, para así completar la ley 25.326.</li>
		<li>Usted tiene derecho a saber sus datos guardados en nuestra base de datos, asi que si desea saber sus datos guardados o modificar los datos existentes, por favor mandar un mail a la casilla misdatos@nuevageneracion.com.ar con el asunto "Mis datos" para saber los datos guardados o "Modificar mis datos" para modificar sus datos y en el cuerpo del mail, poner sus nuevos datos: nombre, apellido, número de documento, email, teléfono.</li>
		<li>En el caso que usted quiera la baja de sus datos en el sistema, deberá mandar un mail a baja@nuevageneracion.com.ar notificado en el asunto del mail la palabra "Baja" y en el cuerpo del mail, su nombre, apellido y número de documento.</li>
		<li>Siguiendo con el punto anterior, cuando un participante se da de baja, queda automaticamente dado de baja del sorteo también.</li>
	</ul>
  </div>