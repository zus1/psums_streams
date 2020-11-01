<?php

use PsumsStreams\Classes\Autoinclude;

include_once("classes/autoinclude.php");
spl_autoload_register([Autoinclude::class, "autoload"]);

