<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class BaseController extends Controller
{
    // property for add delimiter messages
    protected $openDelimiter = '<small class="form-message form-message--danger">';
    protected $closeDelimiter = '</small>';
    protected $ignore;

    protected function addDelimiterMessages(array $messages): array {
        $newMessages = [];

        foreach ($messages as $key => $value) {
            // if ignore not null and key exists in ignore array
            if ($this->ignore && in_array($key, $this->ignore)) {
                $newMessages[$key] = $value;
            } else {
                $newMessages[$key] = $this->openDelimiter.$value.$this->closeDelimiter;
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

    protected function hasErrors(array $fields): bool
    {
        foreach ($fields as $field) {
            if (array_key_exists($field, $this->validator->getErrors())) {
                return false;
            }
        }

        return true;
    }
}
