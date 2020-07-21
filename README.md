# HttPeep
PHP HTTP client for API endpoint/resource requests

### Setup

#### Composer

If you use composer, enter the following command into terminal:

```composer require mykehowells/httpeep```

Then add ```require 'vendor/autoloader.php'``` to your PHP project.

#### Non-Composer

To start using HttPeep, clone this repo into your project;
Using SPL autoloader, enter the following code into your project to load the required files.

```php
set_include_path( CLASSES_DIR );
spl_autoload_extensions('.class.php');
spl_autoload_register();
```

This will include the files as they are called within your project and within he HttPeep files.

### Usage

A simple use case of HttPeep, is below;

```php
$client = new \HttPeep\Client( 'https://api.example.com' );

$response = $client->post( "/user/auth", [
     'token' => $_SESSION['api']['token'],
     'username' => $_POST['username'],
     'password' => md5( $_POST['password'] )
] );

var_export( $response );
```

You can also use the ```\HttPeep\Client->json()``` method to return JSON taken from the body.

```php
$client = new \HttPeep\Client( 'https://api.example.com' );

$response = $client->post( "/user/auth", [
     'token' => $_SESSION['api']['token'],
     'username' => $_POST['username'],
     'password' => md5( $_POST['password'] )
] )->json();

var_export( $response );
```

### Methods

Methods available from HttPeep are:

- GET
- POST
- DELETE
- PUT

### Client Config

When instantiating ```\HttPeep\Client()```, you can pass an array of config variables for cURL; for example:

```php
$client = new \HttPeep\Client( 'https://api.example.com', [
    'curl' => [
        'followlocation' => true, // CURLOPT_FOLLOWLOCATION - follow any Location: header sent as part of the HTTP header
        'maxredirs' => 10 // CURLOPT_MAXREDIRS - Maximum number of HTTP redirections to follow
    ]
] );
```

Default CURLOPTS that are set within the Client are:

| Option                  | Value         |
| ----------------------- | ------------- |
| CURLOPT_RETURNTRANSFER  | true          |
| CURLOPT_SSL_VERIFYPEER  | false         |
| CURLOPT_SSL_VERIFYHOST  | false         |
| CURLOPT_VERBOSE         | false         |
