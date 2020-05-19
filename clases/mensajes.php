<?php

class mensaje
{
    public $id;
    public $destinoId;
    public $mensaje;
    public $emisorId;
    public $fecha;

    public function __construct($mensaje,$destino, $emisor)
    {
        $this->id = time();
        $this->mensaje = $mensaje;
        $this->destinoId = $destino;
        $this->emisorId = $emisor;
        setlocale(LC_TIME,"es_RA");
        $fecha = date("Y-m-d");
        // $hora = date("H-i-s");
        $this->fecha = $fecha; 
    }

    public function guardarMensaje($archivo)
    {
        $listaMensajes = funciones::Leer($archivo);
        // echo "formato: <br>";
        // print_r($listaPersonas);
        // Insertamos persona
        array_push($listaMensajes, $this);
        // print_r($listaPersonas);       
        // escribo archivo
        $retorno = funciones::Guardar($listaMensajes,$archivo,'w');
        $responde = new Lresponse();
            $responde->data = $this;
            $responde->status = $retorno;
        return $responde;
    }

    public static function buscarMEnsajes($archivo, $tipo, $emisor)
    {
        // echo "estoy en usuario";
        $mensajes = funciones::Leer($archivo);
        /*array_search ( mixed $needle , array $haystack [, bool $strict = false ] ) : mixed
        Busca en el haystack (pajar) por la needle (aguja).*/
        $responde = new Lresponse();
        $fechaultima = 0;
        $seleccion = array();
        foreach ($mensajes as $key => $value) {
            // var_dump($value); echo "$key";
            if($tipo == 'admin')
            {
                array_push($seleccion, $value->emisorId . '=>' . $value->destinoId . '=>' . $value->fecha);
            }
            else {
                if($emisor == $value->emisorId)
                {
                    array_push($seleccion, $value);
                }
            }
        }
        return $seleccion;
    }
    public static function buscarMEnsajesId($archivo, $tipo, $id)
    {
        // echo "estoy en usuario";
        $mensajes = funciones::Leer($archivo);
        /*array_search ( mixed $needle , array $haystack [, bool $strict = false ] ) : mixed
        Busca en el haystack (pajar) por la needle (aguja).*/
        $responde = new Lresponse();
        // $fechaultima = 0;
        $seleccion = array();
        $entro = false;
        foreach ($mensajes as $key => $value) {
            // var_dump($value); echo "$key";
            if($tipo == 'admin')
            {
                if($id == $value->emisorId)
                {
                    array_push($seleccion, $value);
                }
                // $entro = $value;
            }
            else {
                if($id == $value->emisorId || $id == $value->destinoId)
                {
                    array_push($seleccion, $value);
                }
            }
        }
        return $seleccion;
    }

}

?>