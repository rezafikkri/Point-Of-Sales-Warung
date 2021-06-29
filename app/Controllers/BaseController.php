<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class BaseController extends Controller
{
    protected function addDelimiterMessages(
        string $openDelimiter,
        string $closeDelimiter,
        array $messages,
        array $ignore = null
    ): array {
        $newMessages = [];

        foreach ($messages as $key => $value) {
            // if ignore not null and key exists in ignore array
            if ($ignore && in_array($key, $ignore)) {
                $newMessages[$key] = $value;
            } else {
                $newMessages[$key] = $openDelimiter.$value.$closeDelimiter;
            }
        }

        return $newMessages;
    }

    protected function createIndoErrorMessages(array $rules): array
    {
        $messages = [];

        foreach ($rules as $rule) {
            switch ($rule) {
                case 'required':
                    $messages = array_merge($messages, [$rule => '{field} tidak boleh kosong!']);
                    break;
                case 'in_list':
                    $messages = array_merge($messages, [$rule => '{field} harus salah satu dari: {param}!']);
                    break;
                case 'min_length':
                    $messages = array_merge($messages, [$rules[$i] => '{field} paling sedikit {param} karakter!']);
                    break;
                case 'max_length':
                    $messages = array_merge($messages, [$rules[$i] => '{field} tidak bisa melebihi {param} karakter.']);
                    break;
                case 'is_unique':
                    $messages = array_merge($messages, [$rules[$i] => '{field} sudah ada.']);
                    break;
                case 'integer':
                    $messages = array_merge($messages, [$rules[$i] => '{field} harus berupa angka dan tanpa desimal.']);
                    break;
            }
        }
        return $messages;
    }
}
