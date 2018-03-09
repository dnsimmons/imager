# Imager

Imager is a Laravel package simplifying image processing operations using PHP's native GD library extension.

### Install

Add the service provider to your config/app.php along with an alias:

    'providers' => [
		...
        Dnsimmons\Imager\ImagerServiceProvider::class,
	];

    'aliases' => [
		...
	    'Imager' => Dnsimmons\Imager\Imager::class,	
	];

### Usage

Instantiate an Imager object passing the path to an input file.

	$path = storage_path('app/example.jpg');
	$imager = new \Imager($path);

Perform some manipulations before rendering the output.

	$imager->resize(640,480)->rotate(90)->render();


@todo ... improve docs