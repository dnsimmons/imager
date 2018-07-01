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

**Basic Example**

	$imager = new \Imager('path/to/example.jpg');
	$imager->resize(640,480)->greyscale()->render();


Most of the examples provided in this documentation uses a call to `render()` which will output the image as raw data back to the caller. In the event you wish to write images to disk simply use `write()` and specify the path and filename for the output.

**Render Example**

	$imager = new \Imager('path/to/example.jpg');
	$imager->resize(640,480)->greyscale()->render();

**Write Example**

	$imager = new \Imager('path/to/example.jpg');
	$imager->resize(640,480)->greyscale()->write('path/to/output.jpg');

### Using Scripts

Imager also supports scripted processing using JSON. Let's repeat what we did above but instead deliver it as a script.

**Scripted Example**

	$imager = new \Imager('path/to/example.jpg');
	$imager->script('path/to/script.json')->render();

Here is our example commands script (script.json). Parameters are supplied in the same order as required by the actual methods for a given command.

	[
		{"command":"resize", "params":["640","480"]},
		{"command":"greyscale", "params":[]}
	]

## Commands (A-Z)

**anaglyph**( )

Applys a stereo 3D anaglyph effect to an image.

	$imager->anaglyph()->render();


**blackwhite**( )

Converts an images color to black and white.

	$imager->blackwhite()->render();

**blur**( *integer $level* )

Blurs an image with a given level from 0 to 10.

	$imager->smooth(10)->render();

**brightness**( *integer $level* )

Adjust the brightness of an image with a given level from -100 to 100.

	$imager->brightness(50)->render();





**colorize**( *integer $r, integer $g, integer $b* )

Apply a color mask to an image with given RGB values from -255 to 255.

	$imager->colorize(128,0,255)->render();

**convert**( *string $format* )

Convert an image to a specified image format (JPEG, PNG, or GIF).

	$imager->convert('JPEG')->render();

**contrast**( *integer $level* )

Adjust the contrast of an image with a given level from -100 to 100.

	$imager->contrast(50)->render();

**crop**( *integer $x, integer $y, integer $width, integer $height* )

Crops an image with specified width and height in pixels from a given x and y origin point.

	$imager->crop(0,0,320,240)->render();


**desaturate**( *integer $level* )

Adjust the saturation of an image with a given level of 0 to 100.

	$imager->desaturate(50)->render();


**emboss**( )

Apply a emboss filter to an image.

	$imager->emboss()->render();

**fisheye**( )

Applys a fisheye lens effect to an image.

	$imager->fisheye()->render();

**flip**( *string $direction* )

Flips an image either horizontally (h), vertically (v), or both directions (b).

	$imager->flip('b')->render();

**greyscale**( )

Converts an images color to greyscale.

	$imager->greyscale()->render();

**layer**( *string $image_path, integer $opacity* )

Layers an image with a given opacity (from 0 to 100) on top of an image using identical dimensions.

	$imager->layer('path/to/layer.png', 50)->render();

**negative**( )

Apply a negative filter to an image.

	$imager->negative()->render();


**noise**( *integer $level* )

Adds noise to an image with a given noise level from 0 to ?.

	$imager->noise(20)->render();

**pixelate**( *integer $size* )

Apply a pixelation filter to an image with a given pixel size.

	$imager->pixelate(4)->render();

**replace**( integer $r, integer $g, integer $b, integer $r2, integer $g2, integer $b2 )

Apply a color replacement to an image with given RGB values from -255 to 255.

	$imager->replace(255,0,0,0,0,255)->render();

**resize**( *integer $width, integer $height* )

Resizes an image to specified width and height in pixels.

	$imager->resize(640,480)->render();

**rotate**( *integer $degrees* )

Rotates an image by a given number of degrees.

	$imager->rotate(90)->render();

**scale**( *integer $width, integer $height* )

Scales an image to specified width and height in pixels based on largest dimension keeping aspect ratio.

	$imager->scale(640,480)->render();

**sepia**( )

Apply a sepia filter to an image.

	$imager->sepia()->render();

**sketch**( )

Apply a sketch filter to an image.

	$imager->sketch()->render();

**smooth**( *integer $level* )

Smooths an image with a given level from 0 to 100.

	$imager->smooth(10)->render();

**vignette**( *integer $size* )

Applys a vignette effect to an image with a given size of 0 to 10.

	$imager->vignette(0.4)->render();

**watermark**( *string $image_path, string $position* )

Applys an image in a given position on top of an image. Possible positions include center, top-left, top-right, bottom-left, and bottom-right.

	$imager->watermark('path/to/watermark.png', 'bottom-right')->render();

