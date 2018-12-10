# Laravel json api response

Multi database architecture with class to switch current database connection laravel framework.

## Installation

Require this package with composer:

```
composer require dharmvijay/laravel-multi-database
```

## usage

```
<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Routing\Controller as BaseController;
use Dharmvijay\LaravelMultiDatabase\BelongsToDatabase;

class ApiController extends BaseController
{
    use Saas;

    public function __construct()
    {
        // some query to get database details from master db
        $database_host = "..."; 
        $database_port = "...";
        $database_name = "...";
        $database_user = "...";
        $database_password = "...";
        $this->connectDynamicUserDb($database_host, 
                                            $database_port,
                                            $database_name,
                                            $database_user,
                                            $database_password);
    }
}
```