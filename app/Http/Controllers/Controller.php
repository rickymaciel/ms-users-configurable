<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function success( $content ) {
		return array('status'=>true, 'content'=>$content);
	}
    
	public function error($content) {
		return array('status'=>false, 'message'=> $content);
	}


	/**
     * Reemplaza todos los acentos por sus equivalentes sin ellos
     *
     * @param $string
     *  string la cadena a sanear
     *
     * @return $string
     *  string saneada
     */
    function sanear_string($string)
    {

      $string = trim($string);

      $string = str_replace(
        array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
        array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
        $string
      );

      $string = str_replace(
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
        $string
      );

      $string = str_replace(
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
        $string
      );

      $string = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
        array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
        $string
      );

      $string = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
        $string
      );

      $string = str_replace(
        array('ñ', 'Ñ', 'ç', 'Ç', 'Ý', 'ý'),
        array('n', 'N', 'c', 'C', 'Y', 'y'),
        $string
      );

      $string = str_replace(
        array("\\", "¨", "º", "~",
          "#", "@", "|", "!", "\"",
          "·", "$", "%", "&", "/",
          "(", ")", "?", "'", "¡",
          "¿", "[", "^", "<code>", "]",
          "+", "}", "{", "¨", "´", "`",
          ">", "< ", ";", ",", ":",
          ".","*","_"),
        '',
        $string
      );

      return $string;
    }
}
