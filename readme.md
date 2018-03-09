# Imager

Imager is a Laravel package simplifying image processing operations using PHP's native GD library extension.

### Install

Use composer to install the package

	composer require dnsimmons/imager

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



----------

### Commands

**resize**( *integer $width, integer $height* )

Resizes an image to specified width and height in pixels.

	$imager = new \Imager($path);
	$imager->resize(640,480)->render();

**scale**( *integer $width, integer $height* )

Scales an image to specified width and height in pixels based on largest dimension keeping aspect ratio.

	$imager = new \Imager($path);
	$imager->scale(640,480)->render();

**crop**( *integer $x, integer $y, integer $width, integer $height* )

Crops an image with specified width and height in pixels from a given x and y origin point.

	$imager = new \Imager($path);
	$imager->crop(0,0,320,240)->render();

**rotate**( *integer $degrees* )

Rotates an image by a given number of degrees.

	$imager = new \Imager($path);
	$imager->rotate(90)->render();

**flip**( *string $direction* )

Flips an image either horizontally (h), vertically (v), or both directions (b).

	$imager = new \Imager($path);
	$imager->flip('b')->render();

**brightness**( *integer $level* )

Adjust the brightness of an image with a given level from -100 to 100.

	$imager = new \Imager($path);
	$imager->brightness(50)->render();

**contrast**( *integer $level* )

Adjust the contrast of an image with a given level from -100 to 100.

	$imager = new \Imager($path);
	$imager->contrast(50)->render();

**greyscale**( )

Converts an images color to greyscale.

	$imager = new \Imager($path);
	$imager->greyscale()->render();


**blackwhite**( )

Converts an images color to black and white.

	$imager = new \Imager($path);
	$imager->blackwhite()->render();

**colorize**( *integer $r, integer $g, integer $b* )

Apply a color mask to an image with given RGB values from -255 to 255.

	$imager = new \Imager($path);
	$imager->colorize(128,0,255)->render();

**negative**( )

Apply a negative filter to an image.

	$imager = new \Imager($path);
	$imager->negative()->render();

**sepia**( )

Apply a sepia filter to an image.


	$imager = new \Imager($path);
	$imager->sepia()->render();

**emboss**( )

Apply a emboss filter to an image.


	$imager = new \Imager($path);
	$imager->emboss()->render();

**sketch**( )

Apply a sketch filter to an image.


	$imager = new \Imager($path);
	$imager->sketch()->render();