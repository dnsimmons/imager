# Imager

Imager is a Laravel package simplifying image processing operations using PHP's native GD library extension.

## Install

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

## Usage

### Basic Usage

The basic example provided below takes an input file example.jpg and resizes it to 640 x 480 pixels and then converts it to greyscale before outputting the raw image data to the browser.

	$imager = new \Imager('path/to/example.jpg');
	$imager->resize(640,480)->greyscale()->render();

### Using Scripts

Imager also supports scripted processing using JSON. Let's repeat what we did above but instead deliver it as a script.

	$imager = new \Imager('path/to/example.jpg');
	$imager->script('path/to/script.json')->render();

Here is our example commands script (script.json). Parameters are supplied in the same order as required by the actual methods for a given command.

	[
		{"command":"resize", "params":["640","480"]},
		{"command":"greyscale", "params":[]}
	]


## Conversions

## Rendering

## Commands

**resize**( *integer $width, integer $height* )

Resizes an image to specified width and height in pixels.

	$imager->resize(640,480)->render();

**scale**( *integer $width, integer $height* )

Scales an image to specified width and height in pixels based on largest dimension keeping aspect ratio.

	$imager->scale(640,480)->render();

**crop**( *integer $x, integer $y, integer $width, integer $height* )

Crops an image with specified width and height in pixels from a given x and y origin point.

	$imager->crop(0,0,320,240)->render();

**rotate**( *integer $degrees* )

Rotates an image by a given number of degrees.

	$imager->rotate(90)->render();

**flip**( *string $direction* )

Flips an image either horizontally (h), vertically (v), or both directions (b).

	$imager->flip('b')->render();

**brightness**( *integer $level* )

Adjust the brightness of an image with a given level from -100 to 100.

	$imager->brightness(50)->render();

**contrast**( *integer $level* )

Adjust the contrast of an image with a given level from -100 to 100.

	$imager->contrast(50)->render();

**desaturate**( *integer $level* )

Adjust the saturation of an image with a given level of 0 to 100.

	$imager->desaturate(50)->render();

**greyscale**( )

Converts an images color to greyscale.

	$imager->greyscale()->render();


**blackwhite**( )

Converts an images color to black and white.

	$imager->blackwhite()->render();

**colorize**( *integer $r, integer $g, integer $b* )

Apply a color mask to an image with given RGB values from -255 to 255.

	$imager->colorize(128,0,255)->render();

**replace**( integer $r, integer $g, integer $b, integer $r2, integer $g2, integer $b2 )

Apply a color replacement to an image with given RGB values from -255 to 255.

	$imager->replace(255,0,0,0,0,255)->render();

**negative**( )

Apply a negative filter to an image.

	$imager->negative()->render();

**sepia**( )

Apply a sepia filter to an image.

	$imager->sepia()->render();

**emboss**( )

Apply a emboss filter to an image.

	$imager->emboss()->render();

**sketch**( )

Apply a sketch filter to an image.

	$imager->sketch()->render();

**pixelate**( *integer $size* )

Apply a pixelation filter to an image with a given pixel size.

	$imager->pixelate(4)->render();

**noise**( integer $level )

Adds noise to an image with a given noise level from 0 to ?.

	$imager->noise(20)->render();