install package with composer  
publish vendor files  
add settings to .env  
  
```
REMEDY_TEST=true

TEST_REMEDYAPI_HOST=

REMEDYAPI_HOST=
REMEDYAPI_USERNAME=
REMEDYAPI_PASSWORD=

TEST_REMEDY_DB_HOST=

REMEDY_DB_HOST=
REMEDY_DB_PORT=
REMEDY_DB_DATABASE=
REMEDY_DB_USERNAME=
REMEDY_DB_PASSWORD=
```

You can specify all the settings with TEST_ prefix, but if they are not defined it will fall back to the standard settings. The only ones that will not are the HOST settings.  

add connection to database config  
```
'remedy' => [
    'driver' => 'sqlsrv',
    'host' => env('REMEDY_TEST', false) ? env('TEST_REMEDY_DB_HOST') : env('REMEDY_DB_HOST'),
    'port' => env('REMEDY_TEST', false) ? env('TEST_REMEDY_DB_PORT', env('REMEDY_DB_PORT')) : env('REMEDY_DB_PORT'),
    'database' => env('REMEDY_TEST', false) ? env('TEST_REMEDY_DB_DATABASE', env('REMEDY_DB_DATABASE')) : env('REMEDY_DB_DATABASE'),
    'username' => env('REMEDY_TEST', false) ? env('TEST_REMEDY_DB_USERNAME', env('REMEDY_DB_USERNAME')) : env('REMEDY_DB_USERNAME'),
    'password' => env('REMEDY_TEST', false) ? env('TEST_REMEDY_DB_PASSWORD', env('REMEDY_DB_PASSWORD')) : env('REMEDY_DB_PASSWORD'),
    'charset' => 'utf8',
    'prefix' => '',
],
```

**Model Scopes**
The default scope on each model is set to select only the most relevant properties. If you want to see all available properties, call withoutGlobalScope("standard") on your model instance. For example:
```
WorkOrder::withoutGlobalScope('standard')->get();
```