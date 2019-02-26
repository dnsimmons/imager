<?php

namespace Dnsimmons\Imager;

/**
* Imager is a Laravel package simplifying image processing operations 
* using PHP's native GD library extension.
*
* @package  Imager
* @author 	David Simmons <hello@dsimmons.me>
* @license 	https://opensource.org/licenses/LGPL-3.0 LGPL-3.0
* @version 	2.0.0
* @since    2018-01-01
*/
class Imager {

	/**
	 * Stores an image resource handle.
	 * @var null
	 */
	var $image_resource = null;

	/**
	 * Stores image mime type id.
	 * @var null
	 */
	var $image_mime = null;

	/**
	* Class constructor.
	*
	* @param 	string 	$image_path 	Source image file path
	* @uses     create()
	* @return 	object
	*/
	public function __construct(string $image_path){
		if(!$this->create($image_path)){
			return false;
		}
		return $this;
	}

	/**
	* Creates an image resource.
	*
	* @param 	string 	$image_path 	Source image file path
	* @return 	boolean
	*/
	private function create(string $image_path){
		if(!file_exists($image_path) || is_dir($image_path)){
			return false;
		}
		$this->image_mime = exif_imagetype($image_path);
		switch($this->image_mime){
			case IMAGETYPE_JPEG:
				$this->image_resource = imagecreatefromjpeg($image_path);
			break;
			case IMAGETYPE_PNG:
				$this->image_resource = imagecreatefrompng($image_path);
			break;	
			case IMAGETYPE_GIF:
				$this->image_resource = imagecreatefromgif($image_path);
			break;	
			default:
				return false;
			break;	
		}

		return true;
	}

	/**
	* Flips the current image either horizontally, vertically, or both.
	*
	* @param 	string 	$direction 	Flip direction (h, v, b)
	* @return 	object
	*/
	public final function flip(string $direction='h'){
		switch($direction){
			default:
			case 'h':
				imageflip($this->image_resource, IMG_FLIP_HORIZONTAL);
			break;
			case 'v':
				imageflip($this->image_resource, IMG_FLIP_VERTICAL);
			break;
			case 'b':
				imageflip($this->image_resource, IMG_FLIP_BOTH);
			break;
		}
		return $this;
	}

	/**
	* Replaces the current image resource with a resized version.
	*
	* @param 	integer $width 	 	New width of the image in pixels
	* @param 	integer $height  	New height of the image in pixels	
	* @return 	object
	*/
	public final function resize(int $width, int $height){
    	$this->image_resource = imagescale($this->image_resource, $width, $height, IMG_BILINEAR_FIXED);
    	return $this;
	}

	/**
	* Replaces the current image resource with a scaled version.
	* Scales based on largest source dimension to keep aspect ratio
	*
	* @param 	integer $width 		New width of the image in pixels
	* @param 	integer $height 	New height of the image in pixels	
	* @return 	object
	*/
	public final function scale(int $width, int $height){
		$orig_x = imagesx($this->image_resource);
		$orig_y = imagesy($this->image_resource);
	    if($orig_x > $orig_y){
	        $w = $width;
	        $h = ($orig_y * ($height / $orig_x));
	    }
	    if($orig_x < $orig_y){
	        $w = ($orig_x * ($width / $orig_y));
	        $h = $height;
	    }
	    if($orig_x == $orig_y){
	        $w = $width;
	        $h = $height;
	    }
		$output = imagecreatetruecolor($w, $h);
	    imagecopyresampled($output, $this->image_resource, 0, 0, 0, 0, $w, $h, $orig_x, $orig_y);
	    $this->image_resource = $output;
		return $this;
	}

	/**
	* Replaces the current image resource with a cropped image portion.
	*
	* @param 	integer 	$x 		 	Crop origin X co-ordinate
	* @param 	integer 	$y 			Crop origin Y co-ordinate
	* @param 	integer 	$width 		Width of the cropped area in pixels
	* @param 	integer 	$height 	Height of the cropped area in pixels	
	* @return 	object
	*/
	public final function crop(int $x, int $y, int $width, int $height){
		$this->image_resource = imagecrop($this->image_resource, [
			'x' 		=> $x, 
			'y' 		=> $y, 
			'width' 	=> $width, 
			'height' 	=> $height
		]);
		return $this;
	}

	/**
	* Replaces the current image resource with a rotation applied.
	*
	* @param 	integer 	$degrees 	Degrees of rotation (-360 to 360)
	* @return 	object
	*/
	public final function rotate(int $degrees){
		$this->image_resource = imagerotate($this->image_resource, $degrees, 0);
		return $this;
	}

	/**
	* Apply a greyscale image processing filter to the image resource
	*
	* @return 	object
	*/
	public final function greyscale(){
		imagefilter($this->image_resource, IMG_FILTER_GRAYSCALE);
		return $this;
	}

	/**
	* Apply a brightness image processing filter to the image resource
	*
	* @param 	integer $level 	Saturation level (-100 to 100)
	* @return 	object
	*/
	public final function brightness(int $level){
		imagefilter($this->image_resource, IMG_FILTER_BRIGHTNESS, $level);
		return $this;
	}

	/**
	* Apply a contrast image processing filter to the image resource
	*
	* @param 	integer $level 	Saturation level (-100 to 100)
	* @return 	object
	*/
	public final function contrast(int $level){
		imagefilter($this->image_resource, IMG_FILTER_CONTRAST, $level);
		return $this;
	}

	/**
	* Apply a colorize image processing filter to the image resource
	*
	* @param 	integer $r 	Color RGB Red value
	* @param 	integer $g 	Color RGB Green value
	* @param 	integer $b 	Color RGB Blue value
	* @return 	object
	*/
	public final function colorize(int $r, int $g, int $b){
		imagefilter($this->image_resource, IMG_FILTER_COLORIZE, $r, $g, $b);
		return $this;
	}

	/**
	* Apply a negative image processing filter to the image resource
	*
	* @return 	object
	*/
	public final function negative(){
		imagefilter($this->image_resource, IMG_FILTER_NEGATE);
		return $this;
	}

	/**
	* Apply a sepia image processing filter to the image resource
	*
	* @return 	object
	*/
	public final function sepia(){
		imagefilter($this->image_resource, IMG_FILTER_GRAYSCALE);
		imagefilter($this->image_resource, IMG_FILTER_BRIGHTNESS,-30);
		imagefilter($this->image_resource, IMG_FILTER_COLORIZE, 90, 55, 30);
		return $this;
	}

	/**
	* Apply a emboss image processing filter to the image resource
	*
	* @return 	object
	*/
	public final function emboss(){
		imagefilter($this->image_resource, IMG_FILTER_EMBOSS);
		return $this;
	}

	/**
	* Apply a sketch image processing filter to the image resource
	*
	* @return 	object
	*/
	public final function sketch(){
		imagefilter($this->image_resource, IMG_FILTER_MEAN_REMOVAL);
		return $this;
	}

	/**
	* Apply a pixelation image processing filter to the image resource
	*
	* @param 	integer $size 	Pixel size (in pixels)
	* @return 	object
	*/
	public final function pixelate(int $size=4){
		imagefilter($this->image_resource, IMG_FILTER_PIXELATE, $size);
		return $this;
	}

	/**
	* Apply a smoothing image processing filter to the image resource
	*
	* @param 	integer $level 	Smoothing level (0 to 100)
	* @return 	object
	*/
	public final function smooth(int $level){
		imagefilter($this->image_resource, IMG_FILTER_SMOOTH, $level);
		return $this;
	}

	/**
	* Apply a gaussian blurring image processing filter to the image resource
	*
	* @param 	integer $passes 	Number of filter passes to apply
	* @return 	object
	*/
	public final function blur(int $passes=1){
		for($i=0; $i<$passes; $i++){
			imagefilter($this->image_resource, IMG_FILTER_GAUSSIAN_BLUR);
		}
		return $this;
	}

	/**
	* Apply a sharpening filter using a 3x3 convolution
	*
	* @param 	integer $passes 	Number of filter passes to apply
	* @return 	object
	*/
	public final function sharpen(int $passes=1){
		for($i=0; $i<$passes; $i++){
			$this->convolution([0, -1, 0], [-1, 5, -1], [0, -1, 0]);
		}
		return $this;
	}

	/**
	* Apply a color replacement image processing filter to the image resource
	*
	* @param 	integer $r 	Target RGB Red value
	* @param 	integer $g 	Target RGB Green value
	* @param 	integer $b 	Target RGB Blue value
	* @param 	integer $r2 Replacement RGB Red value
	* @param 	integer $g2 Replacement RGB Green value
	* @param 	integer $b2 Replacement RGB Blue value
	* @return 	object
	*/
	public final function replace(int $r, int $g, int $b, int $r2, int $g2, int $b2){
		imagetruecolortopalette($this->image_resource, false, 255);
		$idx = imagecolorclosest($this->image_resource, $r, $g, $b);
		imagecolorset($this->image_resource, $idx, $r2, $g2, $b2);
		return $this;
	}

	/**
	* Apply a desaturation image processing filter to the image resource
	*
	* @param 	integer $level 	Saturation level (0 to 100)
	* @return 	object
	*/
	public final function desaturate(int $level){
		$orig_x = imagesx($this->image_resource);
		$orig_y = imagesy($this->image_resource);
		imagecopymergegray($this->image_resource, $this->image_resource, 0, 0, 0, 0, $orig_x, $orig_y, $level);
		return $this;
	}

	/**
	 * Overlays a vignette effect on top of the current image.
	 *
	 * @param  float 	$size 	Size (0 - 10)
	 * @return object
	 */
	public final function vignette(int $size=1){
		$orig_x = imagesx($this->image_resource);
		$orig_y = imagesy($this->image_resource);
    	$output = imagecreatetruecolor($orig_x, $orig_y);
    	imagesavealpha($output, true);
    	$transp = imagecolorallocatealpha($output, 0, 0, 0, 127);
    	imagefill($output, 0, 0, $transp);
	    for($x=0; $x<$orig_x; ++$x){
	      for($y=0; $y<$orig_y; ++$y){  
	        $index 		  = imagecolorat($this->image_resource, $x, $y);
	        $rgb   		  = imagecolorsforindex($this->image_resource, $index);
	        $l 			  = sin(M_PI / $orig_x * $x) * sin(M_PI / $orig_y * $y);
	        $l 			  = pow($l, $size);
	        $l 			  = 1 - 1 * (1 - $l);
	        $rgb['red']   *= $l;
	        $rgb['green'] *= $l;
	        $rgb['blue']  *= $l;
	        $rgb['alpha'] = (127 - (127 * ($l * 1)));
	        $color = imagecolorallocatealpha($output, $rgb['red'], $rgb['green'], $rgb['blue'], $rgb['alpha']);	        
	        imagesetpixel($output, $x, $y, $color);  
	      }
	    }
	    $this->image_resource = $output;
		return $this;
	}

	/**
	 * Apply a fisheye lens effect on the current image.
	 * 
	 * @return object
	 */
	public final function fisheye(){
		$orig_x = imagesx($this->image_resource);
		$orig_y = imagesy($this->image_resource);
		$orig_x_center = ($orig_x / 2);
		$orig_y_center = ($orig_y / 2);
		if($orig_x > $orig_y){
			$output_width = (2 * ($orig_y / pi()));
		}else{
			$output_width = (2 * ($orig_x / pi()));
		}
		$output_center = ($output_width / 2);
		$output = imagecreatetruecolor($output_width, $output_width); 
		$transp = imagecolortransparent($output, imagecolorallocate($output, 0, 0, 0));
		imagefill($this->image_resource, 0, 0, $transp);
		for($c=0; $c<imagecolorstotal($this->image_resource); $c++){
			$col = imagecolorsforindex($this->image_resource, $c);
			imagecolorset($output, $c, $col['red'], $col['green'], $col['blue']);
		}
		for($x=0; $x<=$output_width; ++$x){
			for($y=0; $y<=$output_width; ++$y){
				$otx 	= ($x - $output_center);
				$oty 	= ($y - $output_center);
				$oh  	= hypot($otx, $oty);
				$arc 	= (2 * $output_center * asin($oh / $output_center)) / (2);
				$factor = ($arc / $oh);
				if($oh <= $output_center){
					$color = imagecolorat($this->image_resource, round($otx * $factor + $orig_x_center), round($oty * $factor + $orig_y_center));
					$r = ($color >> 16) & 0xFF;
					$g = ($color >> 8) & 0xFF;
					$b = $color & 0xFF;
					$temp = imagecolorexact($output, $r, $g, $b);
					imagesetpixel($output, $x, $y, $temp);
				}
			}
		}
		$this->image_resource = $output;
		return $this;
	}

	/**
	 * Adds noise to the current image.
	 *
	 * @param  integer 	$level 	Noise level (0 to ?)
	 * @return object
	 */
	public final function noise(int $level=20){
		$orig_x = imagesx($this->image_resource);
		$orig_y = imagesy($this->image_resource);
		$output = imagecreatetruecolor($orig_x, $orig_y);
	    for($x=0; $x < $orig_x; $x++){
	        for($y=0; $y < $orig_y; $y++){
				$rgb    = imagecolorat($this->image_resource, $x, $y);
	            $r      = ($rgb >> 16) & 0xFF;
	            $g      = ($rgb >> 8) & 0xFF;
	            $b      = $rgb & 0xFF;
	            $random = mt_rand(-$level, $level);
				$color  = imagecolorallocate($this->image_resource, ($r + $random), ($g + $random), ($b + $random));
	            imagesetpixel($output, $x, $y, $color);
	        }
	    }
	    $this->image_resource = $output;
	    return $this;
	}

	/**
	 * Converts an image to two color black and white.
	 *
	 * @param  integer 	$level 	BW Threshold (0 to ?)
	 * @return object
	 */
	public final function blackwhite(int $level=20){
		$orig_x = imagesx($this->image_resource);
		$orig_y = imagesy($this->image_resource);
		$output = imagecreatetruecolor($orig_x, $orig_y);
	    for($x=0; $x < $orig_x; $x++){
	        for($y=0; $y < $orig_y; $y++){	
				$rgb    = imagecolorat($this->image_resource, $x, $y);
	            $r      = ($rgb >> 16) & 0xFF;
	            $g      = ($rgb >> 8) & 0xFF;
	            $b      = $rgb & 0xFF;
				$total = ($r + $g + $b);
				if($total > (((255 + $level) / 2) * 3)){
		            $r = 255;
		            $g = 255;
		            $b = 255;
		        } else {
		            $r = 0;
		            $g = 0;
		            $b = 0;
		        }			
	            $random = mt_rand(-$level, $level);
				$color  = imagecolorallocate($this->image_resource, $r, $g, $b);
	            imagesetpixel($output, $x, $y, $color);
	        }
	    }
	    $this->image_resource = $output;
	    return $this;
	}

	/**
	 * Overlays an image on top of the current image in a given position.
	 *
	 * @param  string 	$image_path 	Path to the overlay source image
	 * @param  string   $position 		Overlay position (top-left, top-right, center, bottom-left, bottom-right)
	 * @return object
	 */
	public final function watermark(string $image_path, string $position=''){
		if(!file_exists($image_path) || is_dir($image_path)){
			return false;
		}
		$mime = exif_imagetype($image_path);
		switch($mime){
			case IMAGETYPE_JPEG:
				$img = imagecreatefromjpeg($image_path);
			break;
			case IMAGETYPE_PNG:
				$img = imagecreatefrompng($image_path);
			break;	
			case IMAGETYPE_GIF:
				$img = imagecreatefromgif($image_path);
			break;	
			default:
				return false;
			break;	
		}
		$sx = imagesx($img);
		$sy = imagesy($img);
		$ox = imagesx($this->image_resource);
		$oy = imagesy($this->image_resource);
		switch($position){
			default:
			case 'center':
				imagecopy($this->image_resource, $img, ($ox / 2) - ($sx / 2), ($oy / 2) - ($sy / 2), 0, 0, $sx, $sy);
			break;
			case 'top-left':
				imagecopy($this->image_resource, $img, 0, 0, 0, 0, $sx, $sy);
			break;
			case 'top-right':
				imagecopy($this->image_resource, $img, ($ox - $sx), 0, 0, 0, $sx, $sy);
			break;
			case 'bottom-left':
				imagecopy($this->image_resource, $img, 0, ($oy - $sy), 0, 0, $sx, $sy);
			break;
			case 'bottom-right':
				imagecopy($this->image_resource, $img, ($ox - $sx), ($oy - $sy), 0, 0, $sx, $sy);
			break;
		}
		return $this;
	}

	/**
	 * Overlays an image on the image as a layer with identical dimensions and transparency options.
	 * 
	 * @param  string  $image_path Path to the layer image
	 * @param  integer $opacity    Opacity of the layer (0-100)
	 * @return object
	 */
	public final function layer(string $image_path, int $opacity=50){
		if(!file_exists($image_path) || is_dir($image_path)){
			return false;
		}
		$mime = exif_imagetype($image_path);
		switch($mime){
			case IMAGETYPE_JPEG:
				$img = imagecreatefromjpeg($image_path);
			break;
			case IMAGETYPE_PNG:
				$img = imagecreatefrompng($image_path);
			break;	
			case IMAGETYPE_GIF:
				$img = imagecreatefromgif($image_path);
			break;	
			default:
				return false;
			break;	
		}
		$ox = imagesx($this->image_resource);
		$oy = imagesy($this->image_resource);
		$output = imagecreatetruecolor($ox, $oy);
		$tranps = imagecolorallocatealpha($output, 255, 255, 255, 127);
		imagefill($output, 0, 0 , $tranps);
		imagecopy($output, $img, 0, 0, 0, 0, $ox, $oy);
		imagecopymerge($output, $this->image_resource, 0, 0, 0, 0, $ox, $oy, $opacity);
		$this->image_resource = $output;
		return $this;
	}

	/**
	 * Overlays a duplicate on the image skewed horizontally with red channel removed.
	 * 
	 * @return object
	 */
	public final function anaglyph(){
		$ox = imagesx($this->image_resource);
		$oy = imagesy($this->image_resource);
		$output = imagecreatetruecolor($ox, $oy);
		$tranps = imagecolorallocatealpha($output, 255, 255, 255, 127);
		imagefill($output, 0, 0 , $tranps);
		imagecopymerge($output, $this->image_resource, 0, 0, 0, 0, $ox, $oy, 100);
		$copy = $this->image_resource;
		imagefilter($copy, IMG_FILTER_COLORIZE, 0, 255, 255);
		imagecopymerge($output, $copy, 30, 0, 0, 0, $ox, $oy, 50);
		$output = imagecrop($output, [
			'x' 	 => 30, 
			'y' 	 => 0, 
			'width'  => ($ox-30), 
			'height' => $oy
		]);
		$this->image_resource = $output;
		return $this;
	}

	/**
	 * Overlays a string of text on top of the current image in a given position.
	 * 
	 * @param  string $text      Text
	 * @param  integer $size      Font size (in pixels when using TTF, 1-5 otherwise)
	 * @param  integer $angle     Text angle (ignored when NOT using TTF font)
	 * @param  integer $x         Text X position
	 * @param  integer $y         Text Y position
	 * @param  integer $r         Text color RGB red value
	 * @param  integer $g         Text color RGB green value
	 * @param  integer $b         Text color RGB blue value
	 * @param  string $font_path Optional path to a TTF font file
	 * @return object 
	 */
	public final function text(string $text, int $size, int $angle, int $x, int $y, int $r, int $g, int $b, string $font_path=''){
		$color = imagecolorallocate($this->image_resource, $r, $g, $b);
		if($font_path != ''){
			imagettftext($this->image_resource, $size, $angle, $x, $y, $color, $font_path, $text);
		} else {
			$size = ($size > 5) ? 5 : $size;
			imagestring($this->image_resource, $size, $x, $y, $text, $color);
		}
		return $this;
	}

	/**
	 * Performs a convolution between an image and a 3x3 kernel.
	 * https://en.wikipedia.org/wiki/Kernel_(image_processing)
	 * 
	 * @param  array $matrix1 array of convolution values for top row
	 * @param  array $matrix2 array of convolution values for middle row
	 * @param  array $matrix3 array of convolution values for bottom row
	 * @return object
	 */
	public final function convolution(array $matrix1, array $matrix2, array $matrix3){
		$matrix = [$matrix1, $matrix2, $matrix3];
		imageconvolution($this->image_resource, $matrix, 1, 127);
		return $this;
	}

	/**
	 * Re-assigns the image output mime type to a given type.
	 *
	 * @param  string 	$format 	Conversion format (JPEG, PNG, GIF)
	 * @return object
	 */
	public final function convert(string $format){
		switch($format){
			default:
			case 'JPEG':
				$this->image_mime = IMAGETYPE_JPEG;
			break;
			case 'PNG':
				$this->image_mime = IMAGETYPE_PNG;
			break;	
			case 'GIF':
				$this->image_mime = IMAGETYPE_GIF;
			break;		
		}
		return $this;
	}

	/**
	* Renders the image as raw output to the browser with the appropriate content headers.
	*
	* @return 	void
	*/
	public final function render(){
		switch($this->image_mime){
			default:
			case IMAGETYPE_JPEG:
				header('Content-type: '.IMAGETYPE_JPEG);
				imagejpeg($this->image_resource);
			break;
			case IMAGETYPE_PNG:
				header('Content-type: '.IMAGETYPE_PNG);
				imagepng($this->image_resource);
			break;	
			case IMAGETYPE_GIF:
				header('Content-type: '.IMAGETYPE_GIF);
				imagegif($this->image_resource);
			break;		
		}
		imagedestroy($this->image_resource);
	}

	/**
	* Writes the image to disk.
	*
	* @param 	string $image_path  Output image file path
	* @return 	boolean
	*/
	public final function write(string $image_path){
		switch($this->image_mime){
			default:
			case IMAGETYPE_JPEG:
				imagejpeg($this->image_resource, $image_path);
			break;
			case IMAGETYPE_PNG:
				imagepng($this->image_resource, $image_path);
			break;	
			case IMAGETYPE_GIF:
				imagegif($this->image_resource, $image_path);
			break;		
		}
		imagedestroy($this->image_resource);	
		return true;
	}

	/**
	* Reads, parses, and runs a scripted set of commands from a JSON file.
	*
	* @param 	string $script_path JSON script file path
	* @return 	boolean
	*/
	public final function script(string $script_path){
		$json   = file_get_contents($script_path);
		$script = json_decode($json, TRUE);
		foreach($script as $item){
			$method = $item['command'];
			$params = $item['params'];
			if(method_exists($this, $method)){
				call_user_func_array([$this, $method], $params);
			}
		}
		return $this;		
	}

}