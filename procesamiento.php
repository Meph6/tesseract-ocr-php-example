<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

require __DIR__ . '/vendor/autoload.php';
include('UploadException.php');

use UploadException as ArchivoError;
use thiagoalessio\TesseractOCR\TesseractOCR as tesseract;
use Symfony\Component\Process\Exception\ProcessFailedException as ProcessError;
use Symfony\Component\Process\Process;

if(isset($_POST['enviar'])){
    $mensaje = '';
    $carpeta_archivo =  '/var/www/html/RetoOCR/archivos/';

    if($_FILES['archivo']['error'] === 0){
        
        $ruta_archivo = $carpeta_archivo . basename($_FILES['archivo']['name']);
        
        if(substr($_FILES['archivo']['type'], -3) === 'pdf' && move_uploaded_file($_FILES['archivo']['tmp_name'], $ruta_archivo)){
            
            $mensaje = 'archivo subido correctamente:  '
                     . $_FILES['archivo']['name'].
                       '<br>'. (manejoConsola($ruta_archivo));
        }else{
            $mensaje = 'archivo corrupto o tipo de archivo no permitido';
        }
    }else{
        $mensaje =  new ArchivoError($_FILES['archivo']['error']);
        $mensaje = $mensaje->getMessage();
    }
    $url = 'index.php?mensaje='.$mensaje;
    $url = str_replace(PHP_EOL, ' ', $url);
    header("Location: $url");
    die();
}

/*
   La función se encarga convertir el archivo PDF
   ingresado en una imagen con mayor resolución y
   definición por medio de la herramienta ImageMagick
   con extensión .tiff; a continuación se muestra el comando
   que se ejecuta por consola:

   convert -density 300 «example.pdf» -depth 8 -strip -normalize -threshold 70% -background white -alpha off «example.tiff»

   una vez la conversión sea correcta, ejecuta 
   otro comando que se encarga de obtener el texto
   de la imagen que recién se creó por medio del
   método $ocr->run().
 */
function manejoConsola($ruta_archivo){
    $respuesta = '';
    $comando = new Process(['convert','-density','300',
                            $ruta_archivo, '-depth','8', '-strip',
                            '-normalize', '-threshold','70%',
                            '-background','white', '-alpha','off',
                            substr($ruta_archivo, 0, -4).'.tiff',
    ]);

    $comando->run();

    if($comando->isSuccessful()){
        $ocr = new tesseract(substr($ruta_archivo, 0, -4).'.tiff');
        $respuesta = $ocr->run();
    }else{
        $respuesta = new ProcessError($comando);
        $respuesta = $respuesta->getMessage();
    }
    return $respuesta;
}
