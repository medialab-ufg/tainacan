<?php
/*
 * Descrição: Remove a primeira ocorrencia de uma sequencia de caracteres em um string
 * Entrada: Sequencia de caracteres as ser removida, string de que carcteres serão removidos
 * Retorno: String sem a sequencia de caracteres indicada
 */
function remove_first_occurence($to_be_removed, $string)
{
    $length = strlen($to_be_removed);

    return substr($string, $length);
}

/*
 * Descrição: retira letras e outros caracteres de uma string
 * Entrada: String que se deve retirar letras e outros caracteres
 * Retorno: String que possui apenas números
 */
function just_numbers($str) {
    return preg_replace("/[^0-9]/", "", $str);
}

/*
 * Descrição: retira qualquer coisa que não seja letras em uma string
 * Entrada: String de que se deve deixar apenas letras
 * Retorno: String com apenas letras
 */
function just_letters($str) {
    return preg_replace("/[^a-z]/", "", $str);
}