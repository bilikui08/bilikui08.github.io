<?php include 'functions.php'; ?>

<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Nueva generación es un movimiento liberal">
    <meta name="author" content="Gustavo Luis">
    
    <title>Nueva Generación</title>

    <link rel="canonical" href="">
	<link rel="icon" href="public/images/logo2.jpg" type="image/jpg">

<!-- Bootstrap core CSS -->
<link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
<!-- CSS only -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"> 
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" />
<!-- Custom styles for this template -->
<link href="public/css/form-validation.css" rel="stylesheet">

    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
	  
      .bg-black {
		background-color: #000000;
	  }
	  
	  .hide {
		display: none;
	  }
	  
    </style>

  </head>
  <body class="bg-black text-white">
  	
<div class="container">
<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-black">
  <div class="container-fluid">
    <a class="navbar-brand" href="#"></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <?php include 'nav.php' ?>

  </div>
</nav>

  <main style="margin-top: 10%;">

    <div class="py-5 text-center">
      <a href="<?php echo $baseUrl ?>"/><img class="d-block mx-auto mb-4" src="public/images/logo2.jpg" alt="logo" ></a>
      <h2>Nueva Generación</h2>
	    <p class="lead">Somos una organización social</p>
    </div>
		
	  <?php 
		if (isset($messages)) {
			include 'messages.php'; 
		}
	  ?>

    <?php echo $content ?>
    
  </main>
  <footer class="my-5 pt-5 text-muted text-center text-small">
    <p class="mb-1">&copy; <?php echo date('Y') ?> Nueva Generación</p>
	
    <ul class="list-inline">
      <li class="list-inline-item"><a href="https://gustavoluis.com.ar">Powered by Gustavo Luis</a></li>
    </ul>

  </footer>
</div>


<div id="modales"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

<!-- JavaScript Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>

<script src="public/js/app.js?_<?php echo time() ?>"></script>
<script src="public/js/form-validation.js?_<?php echo time() ?>"></script>

</body>
</html>
