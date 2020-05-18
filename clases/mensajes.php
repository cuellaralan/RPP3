<?php

class mensaje
{
    public $id;
    public $destinoId;
    public $mensaje;
    public $emisorId;

    public function __construct($mensaje,$destino, $emisor)
    {
        $this->id = time();
        $this->mensaje = $mensaje;
        $this->destinoId = $destino;
        $this->emisorId = $emisor;
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

}

?>