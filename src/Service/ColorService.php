<?php

namespace App\Service;

class ColorService
{

    //función para el color de la foto de perfil predeterminada
    public function getRandomColor()
{
    $letters = '0123456789ABCDEF';
    $color = '#';
    for ($i = 0; $i < 6; $i++) {
        $color .= $letters[rand(0, 15)];
    }
    return $color;
}

}