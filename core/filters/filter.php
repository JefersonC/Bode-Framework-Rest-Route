<?php

namespace filters;

/**
 * Classe responsável por tratar os métodos de entrada, principal defesa anti sql injection 
 * Nela tratamos os métodos de requisição HTTP assegurando que os mesmos retornem aquilo que foi esperado.
 */
class filter {

    static function email($value) {
        $regExp = '/^[a-zA-Z0-9\._-]+?@[a-zA-Z0-9_-]+?\.[a-zA-Z]{2,6}((\.[a-zA-Z]{2,4}){0,1})$/';

        if (preg_match($regExp, $value)) {
            return true;
        }

        return false;
    }

    static function required($value, $strict = true) {
        
        if(!$strict){
            return !empty($value);
        }
        
        if (
                $value === null ||
                $value === false ||
                $value === ''
        ) {
            return false;
        }

        return true;
    }

}
