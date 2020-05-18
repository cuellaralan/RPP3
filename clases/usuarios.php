<?php

class usuario
{
    public $id;
    public $email;
    public $clave;
    public $tipo;
    public $foto1;
    public $foto2;

    public function __construct($email,$clave, $tipo, $foto1, $foto2)
    {
        $this->id = time();
        $this->email = $email;
        $this->clave = $clave;
        $this->tipo = $tipo;
        $this->foto1 = $foto1;
        $this->foto2 = $foto2;
    }

    public function guardarUsuario($archivo)
    {
        $listaPersonas = funciones::Leer($archivo);
        // echo "formato: <br>";
        // print_r($listaPersonas);
        // Insertamos persona
        array_push($listaPersonas, $this);
        // print_r($listaPersonas);       
        // escribo archivo
        $retorno = funciones::Guardar($listaPersonas,$archivo,'w');
        $responde = new Lresponse();
            $responde->data = $this;
            $responde->status = $retorno;
        return $responde;
    }

    public static function verificarLogin($archivo,$mail,$pass)
    {
        // echo "estoy en usuario";
        $listaUsuarios = funciones::Leer($archivo);
        /*array_search ( mixed $needle , array $haystack [, bool $strict = false ] ) : mixed
        Busca en el haystack (pajar) por la needle (aguja).*/
        $responde = new Lresponse();
        foreach ($listaUsuarios as $key => $value) {
            // var_dump($value); echo "$key";
            if($value->email == $mail && $value->clave== $pass)
            {
                $responde->data = $value;
                $responde->status = 'succes';
                break;
            }
        }
        return $responde;
    }

    public static function verificarUser($archivo,$name,$lastname)
    {
        // echo "estoy en usuario";
        $retorno = false;
        $usuarios = funciones::Listar($archivo);
        foreach ($usuarios as $key => $value) {
            // var_dump($value); echo "$key";
            if($value->nombre == $name && $value->apellido== $lastname)
            {
                $retorno = $value;
                break;
            }
            
        }
        return $retorno;
    }

}

?>