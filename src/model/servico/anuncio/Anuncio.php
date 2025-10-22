<?php

namespace model\servico\anuncio;
require_once __DIR__ . '/../Servico.php';

use DateTime;
use model\servico\anuncio\FormatoAnuncio;
use model\servico\Servico;

class Anuncio extends Servico
{
    private FormatoAnuncio $formato_anuncio;
    private string $categoria_anuncio;

    public function __construct(int $id, string $nome, string $descricao, string $imagem,
                                DateTime $data_registro, FormatoAnuncio $formato_anuncio, string $categoria_anuncio)
    {
        parent::__construct($id, $nome, $descricao, $imagem, $data_registro);
        $this->formato_anuncio = $formato_anuncio;
        $this->categoria_anuncio = $categoria_anuncio;
    }

    public function getFormatoAnuncio(): FormatoAnuncio
    {
        return $this->formato_anuncio;
    }

    public function setFormatoAnuncio(FormatoAnuncio $formato_anuncio): void
    {
        $this->formato_anuncio = $formato_anuncio;
    }

    public function getCategoriaAnuncio(): string
    {
        return $this->categoria_anuncio;
    }

    public function setCategoriaAnuncio(string $categoria_anuncio): void
    {
        $this->categoria_anuncio = $categoria_anuncio;
    }


}