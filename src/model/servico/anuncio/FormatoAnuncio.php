<?php

namespace model\servico\anuncio;

enum FormatoAnuncio: string
{
    case NoticiaCompleta = 'Noticia_completa';
    case Horizontal = 'Horizontal';
    case Quadrado = 'Quadrado';
}
