<?php

namespace App\Services;

use Exception;

/**
 * Class PasswordChecker - Проверка сложности паролей
 * @package App\Services
 */
class PasswordChecker
{
    
    /**
     * Проверка на сложность пароля
     */
    public static function check(string $login, string $password)
    {
        $strong = 0;
        $min_password_length = $_ENV['PASSWORD_MIN_LENGTH'] ?? 6;
        $min_strong = $_ENV['PASSWORD_MIN_STRONG'] ?? 6;
        $msg = [];


        // Длинна пароля 
        if (strlen($password) < $min_password_length) {
            throw new Exception("Пароль должен быть не менее $min_password_length символов");
        }
        $strong++;

        // Содержание логина
        if (strpos($password, $login) !== false) {
            throw new Exception("Пароль должен включать в себя логин");
        }
        $strong++;


        //Словарь
        $bad_passwords = file_get_contents(__DIR__ . '/BadPasswords.list');
        $bad_passwords = explode("\n", $bad_passwords);
        if (in_array(mb_strtolower($password), $bad_passwords)) {
            throw new Exception("Выберите другой пароль");
        } else {
            $strong++;
        }

        //Цифры
        if(preg_match("/([0-9]+)/", $password)) {
            $strong++;
        } else {
            $msg[] = "Пароль должен содержать цифры";
        }

        // Нижний регистр
        if (preg_match("/([a-zа-я]+)/u", $password)) {
            $strong++;
        } else {
            $msg[] = "Пароль должен содержать буквы в нижнем регистре";
        }
        
        //Верхний регистр        
        if (preg_match("/([A-ZА-Я]+)/u", $password)) {
            $strong++;
        } else {
            $msg[] = "Пароль должен содержать буквы в верхнем регистре";
        }
                
        // Спец символы
        if (preg_match("/([!@#$%^&*]+)/", $password)) {
            $strong++;
        } else {
            $msg[] = "Пароль должен содержать спецсимволы";
        }


        if ($strong < $min_strong) {
            throw new Exception($msg[0] ?? "Неизвестная ошибка");
        }

    }
}
