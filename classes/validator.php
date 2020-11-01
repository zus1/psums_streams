<?php

namespace PsumsStreams\Classes;

/**
 * Class Validator
 * @package PsumsStreams\Classes
 *
 * Main validation class for project
 * Uses filters for sanitation
 *
 */
class Validator
{
    public function filterAlphaNumeric($value) {
        return $this->filter($value, "/[^A-Za-z0-9]/");
    }

    public function filterAlphaNumericDash($value) {
        return $this->filter($value, "/[^A-Za-z0-9]_ /");
    }

    public function filterNumeric($value) {
        return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    public function filterAlpha($value) {
        return $this->filter($value, "/[^A-Za-z]/");
    }

    public function filterAlphaLatin($value) {
        return $this->filter($value, "/[^A-Za-zČĆŽŠĐčćžšđ]/");
    }

    public function filterAlphaDash($value) {
        return $this->filter($value, "/[^A-Za-z_ ]/");
    }

    public function filterAlphaNumUnderscore($value) {
        return $this->filter($value, "/[^A-Za-z0-9_-]/");
    }

    public function filter($value, string $pattern) {
        return preg_replace($pattern, "", $value);
    }
}