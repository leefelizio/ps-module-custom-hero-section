<?php
/**
 * Copyright 2024 Lee Felizio Castro
 * @author    Lee Felizio Castro <feliziolee@gmail.com>
 * @copyright Since 2024 Lee Felizio Castro
 * @license   https://opensource.org/license/mit MIT LICENSE
 */
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

header('Location: ../');
exit;
