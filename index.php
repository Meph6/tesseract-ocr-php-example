<?php
$mensaje = (isset($_GET['mensaje'])) ? $_GET['mensaje'] : 'default';
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <title>Tesseract OCR</title>
    </head>
    <body>
        <form action="procesamiento.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="MAX_FILE_SIZE" value="2000000"/>
            <input type="file" name="archivo" value="Seleccionar archivo"/>

            <button type="submit" name="enviar">Enviar</button>            
        </form>
        <h4><?=$mensaje?></h4>
    </body>
</html>
