<?php
  /**
     * Helper Functions for Lagaren
     * Autoloads via Composer
     */

if (!function_exists('getFileExtention')) {

    function getFileExtention($column)
    {
        $extension = pathinfo($column, PATHINFO_EXTENSION).'.png';
        return $extension;
    }
}