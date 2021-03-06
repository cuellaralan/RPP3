<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

use \Firebase\JWT\JWT;
require_once __DIR__ .'./vendor/autoload.php';
// require './clases/paises.php';
require_once './clases/funciones.php';
require_once './clases/usuarios.php';
require_once './clases/mensajes.php';
require_once './clases/response.php';
require_once './clases/ventas.php';

$metodo = $_SERVER["REQUEST_METHOD"];

$app = AppFactory::create();
$app->setBasePath("/RPP3");
$app->addErrorMiddleware(false, true, true);
$key = "example_key";

// $response = new Lresponse();
// $response->status = 'success';

//obtengo headers
$headers = getallheaders();
//verifico token
$token = $headers['token'] ?? '';

$app->post('/login', function (Request $request, Response $response, array $args) {
    $archivo = './files/users.json';
    $key = "example_key";
    $responde = new Lresponse();
    $responde->status = 'success';
    $email = $request->getParsedBody()['email'] ?? '';
    $clave = $request->getParsedBody()['clave'] ?? '';
    
        if($email != '' && $clave != '')
        {
            $responde = usuario::verificarLogin($archivo,$email,$clave);
            $datos= $responde->data;
            // print_r(json_encode($response));
            if($responde->status == 'unsucces')
            {
                $responde->data = "Datos erroneos, verifique.";
            }
            else 
            {
                $payload = array(
                    "iss" => "http://example.org",
                    "aud" => "http://example.com",
                    "iat" => 1356999524,
                    "nbf" => 1357000000,
                    "name" => $datos->email,
                    "id" => $datos->id,
                    "tipo" => $datos->tipo
                );
                $jwt = JWT::encode($payload, $key);
                $responde->data = $jwt;           
                // echo json_encode($response);
            }
        }
        
        else
        {
            $responde->data = "Error - Datos vacíos ";
        }
        $response->getBody()->write(json_encode($responde));
        return $response
            ->withHeader('Content-Type' , 'aplication/json')
            ->withStatus(200);
});
    
$app->post('/users', function (Request $request, Response $response, array $args) {
    $archivo = './files/users.json';
    $key = "example_key";
    $responde = new Lresponse();
    $responde->status = 'success';
    $email = $request->getParsedBody()['email'] ?? '';
    $clave = $request->getParsedBody()['clave'] ?? '';
    $tipo = $request->getParsedBody()['tipo'] ?? '';
    // $os = $request->getParsedBody()['obra_social'] ?? '';
    // $clave = $request->getParsedBody()['clave'] ?? '';
    //files
    $fotos = $_FILES['foto'] ?? '';

    if($email != '' && $clave != '' && $tipo != '' && $fotos != '' && ($tipo == 'admin' || 'user'))
    {
        //parametros para guardar foto
        $destino = './images/users/';
        $fotoName = $fotos['name'][0];
        $path = $fotos['tmp_name'][0];
        //
        $destiny1 = funciones::GuardaTemp($path, $destino, $fotoName, $email); 
        //
        $fotoName = $fotos['name'][1];
        $path = $fotos['tmp_name'][1];
        //
        $destiny2 = funciones::GuardaTemp($path, $destino, $fotoName, $email); 
        
        if($destino != $destiny1 && $destiny2)
        {
            $cliente = new usuario($email, $clave, $tipo, $destiny1,  $destiny2);
                $respuesta = $cliente->guardarUsuario($archivo);
                $responde = $respuesta;
        }
        else
        {
            // $response = new Lresponse();
            $responde->status = 'unsucces';
            $responde->data = 'error al subir imagen de producto';
            // echo $response;
        }
    }
    else
    {
        $responde->data = "Error - Datos vacíos o incorrectos";
    }
    $response->getBody()->write(json_encode($responde));
        return $response
            ->withHeader('Content-Type' , 'aplication/json')
            ->withStatus(200);
});
        
        
        
$app->get('/mensajes/{id}', function (Request $request, Response $response, array $args) {
    $key = "example_key";
    //parametros para guardar foto
    $archivo = './files/mensajes.json.';
    $responde = new Lresponse();
    $id = $request->getQueryParams()['id'];
    // $destino = $request->getParsedBody()['id_usuario'] ?? '';
    //obtengo token
    $headers = getallheaders();
    //verifico token
    $token = $headers['token'] ?? '';
    if ($token == '') {
        // $response = new Lresponse();
        $responde->status = 'unsucces';
        $responde->data = 'error , token incorrecto';
        $verifica = false;
    }
    else{
        try {
            //code...
            $decoded = JWT::decode($token, $key, array('HS256'));
            $verifica = true;
            // print_r($decoded);
        } catch (\Throwable $th) {
            //throw $th;
            $verifica = false;
            $responde->data = $th;
        }
    }
    if($verifica == true && $id != '')
    {
        $tipo = $decoded->tipo;
        // $emisor = $decoded->id;
        $respuesta = mensaje::buscarMEnsajesId($archivo, $tipo, $id);
        $responde->data = $respuesta;
        $responde->status = 'succes';
    }
    else{
        $responde->data = 'Datos con errores'; 
    }
    $response->getBody()->write(json_encode($responde));
        return $response
            ->withHeader('Content-Type' , 'aplication/json')
            ->withStatus(200);
});
$app->group('/mensajes', function($group){
    $group->post('[/]', function (Request $request, Response $response, array $args) {
        $key = "example_key";
        //parametros para guardar foto
        $archivo = './files/mensajes.json.';
        $responde = new Lresponse();
        $mensaje = $request->getParsedBody()['mensaje'] ?? '';
        $destino = $request->getParsedBody()['id_usuario'] ?? '';
        //obtengo token
        $headers = getallheaders();
        //verifico token
        $token = $headers['token'] ?? '';
        if ($token == '') {
            // $response = new Lresponse();
            $responde->status = 'unsucces';
            $responde->data = 'error , token incorrecto';
            $verifica = false;
        }
        else{
            try {
                //code...
                $decoded = JWT::decode($token, $key, array('HS256'));
                $verifica = true;
                // print_r($decoded);
            } catch (\Throwable $th) {
                //throw $th;
                $verifica = false;
                $responde->data = $th;
            }
        }
        if($mensaje != '' && $destino != '' && $verifica == true)
        {
            $nuevoMensaje = new mensaje($mensaje, $destino, $decoded->id);
            $responde = $nuevoMensaje->guardarMensaje($archivo);
            if($responde->status == 'unsucces')
            {
                $responde->data = 'error al guardar mensaje';
            }
            else{
                $responde->data = 'guardado exitoso';
            }
        }
        else{
            $responde->data = 'Datos con errores'; 
        }
        $response->getBody()->write(json_encode($responde));
            return $response
                ->withHeader('Content-Type' , 'aplication/json')
                ->withStatus(200);
    });

    $group->get('', function (Request $request, Response $response, array $args) {
        $key = "example_key";
        //parametros para guardar foto
        $archivo = './files/mensajes.json.';
        $responde = new Lresponse();
        // $mensaje = $request->getParsedBody()['mensaje'] ?? '';
        // $destino = $request->getParsedBody()['id_usuario'] ?? '';
        //obtengo token
        $headers = getallheaders();
        //verifico token
        $token = $headers['token'] ?? '';
        if ($token == '') {
            // $response = new Lresponse();
            $responde->status = 'unsucces';
            $responde->data = 'error , token incorrecto';
            $verifica = false;
        }
        else{
            try {
                //code...
                $decoded = JWT::decode($token, $key, array('HS256'));
                $verifica = true;
                // print_r($decoded);
            } catch (\Throwable $th) {
                //throw $th;
                $verifica = false;
                $responde->data = $th;
            }
        }
        if($verifica == true)
        {
            $tipo = $decoded->tipo;
            $emisor = $decoded->id;
            $respuesta = mensaje::buscarMEnsajes($archivo, $tipo, $emisor);
            $responde->data = $respuesta;
            $responde->status = 'succes';
        }
        else{
            $responde->data = 'Datos con errores - '; 
        }
        $response->getBody()->write(json_encode($responde));
            return $response
                ->withHeader('Content-Type' , 'aplication/json')
                ->withStatus(200);
    });

    
});
// $app->group('/stock', function($group){
    //     $group->post('[/]', function (Request $request, Response $response, array $args) {
        //         $archivo = './files/producto.json';
        //         $verifica = true;
//         $key = "example_key";
//         $responde = new Lresponse();
//         //obtengo body
//         $producto = $request->getParsedBody()['producto'] ?? '';
//         $marca = $request->getParsedBody()['marca'] ?? '';
//         $precio = $request->getParsedBody()['precio'] ?? '';
//         $stock = $request->getParsedBody()['stock'] ?? '';
//         $foto = $_FILES['foto'] ?? '';
//         //obtengo token
//         $headers = getallheaders();
//         //verifico token
//         $token = $headers['token'] ?? '';
//         if ($token == '') {
//             // $response = new Lresponse();
//             $responde->status = 'unsucces';
//             $responde->data = 'error , token incorrecto';
//         }
//         else{
//             try {
    //                 //code...
//                 $decoded = JWT::decode($token, $key, array('HS256'));
//                 if ($decoded->tipo != 'admin') {
//                     $verifica = false;
//                 }
//                 // print_r($decoded);
//             } catch (\Throwable $th) {
//                 //throw $th;
//                 $verifica = false;
//                 $responde->data = $th;
//             }
//         }
//         if($producto != '' && $marca != '' && $precio != '' && $stock != '' && $foto != '')
//         {
//             //parametros para guardar foto
//             $fotoName = $foto['name'];
//             $path = $foto['tmp_name'];
//             $destino = './imagenes/';
//             //
//             $destiny = funciones::GuardaTemp($path, $destino, $fotoName, $producto . $marca); 
//             if($destino != $destiny)
//             {
    //                 $producto = new producto($producto, $marca, $precio, $stock, $destiny);
    //                 $responde = $producto->guardarProducto($archivo);
    //                 // echo $response;
    //             }
    //             else
    //             {
        //                 // $response = new Lresponse();
        //                 $responde->status = 'unsucces';
//                 $responde->data = 'error al subir imagen de producto';
//                 // echo $response;
//             }
    
//         }
//         else
//         {
//             // $response = new Lresponse();
//             $responde->status = 'unsucces';
//             $responde->data = 'error: Usuario no permitido o datos vacíos';
//             // echo json_encode($response);
//         }
//         $response->getBody()->write(json_encode($responde));
//         return $response
//             ->withHeader('Content-Type' , 'aplication/json')
//             ->withStatus(200);
//     });

//     $group->get('[/]', function (Request $request, Response $response, array $args) {
//         //auxiliares
//         $archivo = './files/producto.json';
//         $verifica = true;
//         $key = "example_key";
//         $responde = new Lresponse();
//         //obtengo token
//         $headers = getallheaders();
//         $token = $headers['token'] ?? '';
//         //verifico token
//         if ($token == '') {
//             $responde->status = 'unsucces';
//             $responde->data = 'error , token incorrecto';
//     }
//     else{
//         try {
//             $decoded = JWT::decode($token, $key, array('HS256'));
//             $verifica = true;
//         } catch (\Throwable $th) {
//             //throw $th;
//             $verifica = false;
//             $responde->status = 'unsucces';
//             $responde->data = 'error , token incorrecto';
//         }
//     }
//     if($verifica == true)
//     {
//         $listaProd = funciones::Leer($archivo);
//         $responde->status = 'succes';
//         $responde->data = $listaProd;
//     }
//     else
//     {
//         $responde->data = "Token no informado";
//     }
//     $response->getBody()->write(json_encode($responde));
//         return $response
//             ->withHeader('Content-Type' , 'aplication/json')
//             ->withStatus(200);
//     });
// });
    
// $app->group('/ventas', function($group){
//     $group->post('[/]', function (Request $request, Response $response, array $args) {
//         //auxiliares
//         $archivo = './files/producto.json';
//         $verifica = true;
//         $key = "example_key";
//         $responde = new Lresponse();
//         //obtengo body
//         $idProd = $request->getParsedBody()['id_producto'] ?? '';
//         $cantidad = $request->getParsedBody()['cantidad'] ?? '';
//         $usuario = $request->getParsedBody()['usuario'] ?? '';
//         //obtengo token
//         $headers = getallheaders();
//         $token = $headers['token'] ?? '';
//         //verifico token
//         if ($token == '') {
//             $responde->status = 'unsucces';
//             $responde->data = 'error , token incorrecto';
//         }
//         else{
//             try {
//                 $decoded = JWT::decode($token, $key, array('HS256'));
//                 $decoded->tipo = 'user';
//                 if ($decoded->tipo != 'user') {
//                     $verifica = false;
//                     $responde->data = 'error , tipo de usuario no permitido';
//                 }
//                 // echo "try decoded";
//             } catch (\Throwable $th) {
//                 //throw $th;
//                 $verifica = false;
//                 $responde->data = $th;
//             }
//         }
//         if($idProd != '' && $cantidad != '' && $usuario != '')
//         {
//             $venta = new venta($idProd, $cantidad, $usuario);
//             //verifico stock producto
//             $respuesta = funciones::BuscaEnArrayxID($archivo, $idProd);
//             // print_r(jsosn_encode($respuesta));
//             if ($respuesta->status == 'succes') {
//                 $producto = $respuesta->data;
//                 if ($producto->stock > $cantidad) {
//                     $archivo = './files/ventas.json';
//                     $responde = $venta->guardarVenta($archivo, $producto);
//                 }
//                 else
//                 {
//                     $responde->data = "no hay stock de producto";   
//                 }
//             }
//         }
//         $response->getBody()->write(json_encode($responde));
//         return $response
//             ->withHeader('Content-Type' , 'aplication/json')
//             ->withStatus(200);
//     });

//     $group->get('[/]', function (Request $request, Response $response, array $args) {
//         //auxiliares
//         $archivo = './files/producto.json';
//         $verifica = true;
//         $key = "example_key";
//         $responde = new Lresponse();
//         //obtengo body
//         $idProd = $request->getParsedBody()['id_producto'] ?? '';
//         $cantidad = $request->getParsedBody()['cantidad'] ?? '';
//         $usuario = $request->getParsedBody()['usuario'] ?? '';
//         //obtengo token
//         $headers = getallheaders();
//         $token = $headers['token'] ?? '';
//         //verifico token
//         if ($token == '') {
//             $responde->status = 'unsucces';
//             $responde->data = 'error , token incorrecto';
//             // print_r(json_encode($response));
//         }
//         else{
//             try {
//                 //code...
//                 $decoded = JWT::decode($token, $key, array('HS256'));
//                 $verifica = true;
//                 // print_r($decoded);
//             } catch (\Throwable $th) {
//                 //throw $th;
//                 $verifica = false;
//                 $responde->status = 'unsucces';
//                 $responde->data = 'error , token incorrecto';
//                 // print_r(json_encode($response));
//             }
//         }
//         if($verifica == true)
//         {
//             $datos = $decoded;
//             if ($datos->tipo != 'admin' && $datos->tipo != 'user') {
//                 $responde->data = 'error , tipo de usuario invalido';
//             }
//             else {
//                 $respuesta = venta::traerVentas($datos->tipo, $datos->name);
//                 if ($respuesta == '') {
//                     $responde->data = "no existen ventas";
//                 }
//                 else{
//                     $responde->status = 'succes';
//                     $responde->data = $respuesta;
//                 }
//             }   
//         }
//         $response->getBody()->write(json_encode($responde));
//         return $response
//             ->withHeader('Content-Type' , 'aplication/json')
//             ->withStatus(200);
//     });

// });    
    $app->run();
    ?>

