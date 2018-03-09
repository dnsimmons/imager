<?php

namespace Dnsimmons\Imager;

use Illuminate\Database\Eloquent\Model;

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
class Imager extends Model{

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
	 * Stores debugging flag.
	 * @var boolean
	 */
	var $debug = true;

	/**
	 * Stores an array of various error messages.
	 * @var array
	 */
	var $errors = [
		'error_read_error' 			=> 'Error reading language definition.',
		'error_read_source' 		=> 'Error reading source image.',
		'error_flip_direction' 		=> 'An invalid value was supplied for direction.',
		'error_resize_width' 		=> 'An invalid value was supplied for width.',
		'error_resize_height' 		=> 'An invalid value was supplied for height.',
		'error_scale_width' 		=> 'An invalid value was supplied for width.',
		'error_scale_height' 		=> 'An invalid value was supplied for height.',	
		'error_crop_x' 				=> 'An invalid value was supplied for x.',		
		'error_crop_y' 				=> 'An invalid value was supplied for y.',	
		'error_crop_width' 			=> 'An invalid value was supplied for width.',
		'error_crop_height' 		=> 'An invalid value was supplied for height.',
		'error_crop_x_bounds' 		=> 'Supplied X origin value is out of bounds.',		
		'error_crop_y_bounds' 		=> 'Supplied Y origin value is out of bounds.',	
		'error_crop_width_bounds' 	=> 'Supplied width value out of bounds with X origin.',
		'error_crop_height_bounds' 	=> 'Supplied height value out of bounds with Y origin.',
		'error_rotate_degrees' 		=> 'An invalid value was supplied for degrees.',
		'error_brightness_level' 	=> 'An invalid value was supplied for level.',
		'error_contrast_level' 		=> 'An invalid value was supplied for level.',
		'error_colorize_r' 		    => 'An invalid value was supplied for RGB red.',
		'error_colorize_g' 		    => 'An invalid value was supplied for RGB green.',
		'error_colorize_b' 		    => 'An invalid value was supplied for RGB blue.',
		'error_pixelate_size' 		=> 'An invalid value was supplied for size.',
		'error_smooth_level' 		=> 'An invalid value was supplied for level.',
		'error_blur_passes' 		=> 'An invalid value was supplied for passes.',
		'error_replace_r' 		    => 'An invalid value was supplied for target RGB red.',
		'error_replace_g' 		    => 'An invalid value was supplied for target RGB green.',
		'error_replace_b' 		    => 'An invalid value was supplied for target RGB blue.',
		'error_replace_r2' 		    => 'An invalid value was supplied for replacement RGB red.',
		'error_replace_g2' 		    => 'An invalid value was supplied for replacement RGB green.',
		'error_replace_b2' 		    => 'An invalid value was supplied for replacement RGB blue.',
		'error_desaturate_level' 	=> 'An invalid value was supplied for level.',
	];

	/**
	* Class constructor.
	*
	* @param 	string 	$image_path 	Source image file path
	* @uses     create()
	* @return 	object
	*/
	public function __construct($image_path){
		
		if(!$this->create($image_path)){
			$this->error(__METHOD__, 'error_read_source');
		}

		return $this;
	}

	/**
	 * Outputs an error if debug is enabled.
	 *
	 * @param  string  $method 	Calling method name
	 * @param  string  $key 	Error language key
	 * @param  boolean $exit    If true halt execution
	 * @return void
	 */
	private function error($method, $key, $exit=true){
		
		if($this->debug){
			if(isset($this->errors[$key])){
				die($method.' '.$this->errors[$key]);
			} else {
				$this->error(__METHOD__, 'error_read_error');
			}	
		}

		if($exit){
			exit();
		}
	}

	/**
	* Creates an image resource.
	*
	* @param 	string 	$image_path 	Source image file path
	* @return 	boolean
	*/
	private function create($image_path){
		
		if(!file_exists($image_path) || is_dir($image_path)){
			$this->error(__METHOD__, 'error_read_source');
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

	////////////////////////////////////////////////////////////////////////////////
	// Dimensional Methods
	////////////////////////////////////////////////////////////////////////////////

	/**
	* Flips the current image either horizontally, vertically, or both.
	*
	* @param 	string 	$direction 	Flip direction (h, v, b)
	* @return 	object
	*/
	public final function flip($direction){

		if($direction != 'h' && $direction != 'v' && $direction != 'b'){
			$this->error(__METHOD__, 'error_flip_direction');
		}

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
	public final function resize($width, $height){

		if(!is_numeric($width) || $width < 0){
			$this->error(__METHOD__, 'error_resize_width');
		}
		if(!is_numeric($height) || $height < 0){
			$this->error(__METHOD__, 'error_resize_height');
		}

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
	public final function scale($width, $height){

		$orig_x = imagesx($this->image_resource);
		$orig_y = imagesy($this->image_resource);

		if(!is_numeric($width) || $width < 0){
			$this->error(__METHOD__, 'error_scale_width');
		}
		if(!is_numeric($height) || $height < 0){
			$this->error(__METHOD__, 'error_scale_height');
		}

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
	public final function crop($x, $y, $width, $height){

		$orig_x = imagesx($this->image_resource);
		$orig_y = imagesy($this->image_resource);

		if(!is_numeric($x) || $x < 0){
			$this->error(__METHOD__, 'error_crop_x');
		}
		if(!is_numeric($y) || $y < 0){
			$this->error(__METHOD__, 'error_crop_y');
		}
		if(!is_numeric($width) || $width < 0){
			$this->error(__METHOD__, 'error_crop_width');
		}
		if(!is_numeric($height) || $height < 0){
			$this->error(__METHOD__, 'error_crop_height');
		}
		if($x > $orig_x){
			$this->error(__METHOD__, 'error_crop_x_bounds');
		}
		if($y > $orig_y){
			$this->error(__METHOD__, 'error_crop_y_bounds');
		}
		if($width > ($orig_x - $x)){
			$this->error(__METHOD__, 'error_crop_width_bounds');
		}
		if($height > ($orig_y - $y)){
			$this->error(__METHOD__, 'error_crop_height_bounds');
		}

		$params = ['x' => $x, 'y' => $y, 'width' => $width, 'height' => $height];
		$this->image_resource = imagecrop($this->image_resource, $params);

		return $this;
	}

	/**
	* Replaces the current image resource with a rotation applied.
	*
	* @param 	integer 	$degrees 	Degrees of rotation (-360 to 360)
	* @return 	object
	*/
	public final function rotate($degrees){
		
		if(!is_numeric($degrees)){
			$this->error(__METHOD__, 'error_rotate_degrees');
		}

		$this->image_resource = imagerotate($this->image_resource, $degrees, 0);

		return $this;
	}

	////////////////////////////////////////////////////////////////////////////////
	// Filter Methods
	////////////////////////////////////////////////////////////////////////////////

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
	public final function brightness($level){

		if(!is_numeric($level) || $level < -100 || $level > 100){
			$this->error(__METHOD__, 'error_brightness_level');
		}

		imagefilter($this->image_resource, IMG_FILTER_BRIGHTNESS, $level);
		return $this;
	}

	/**
	* Apply a contrast image processing filter to the image resource
	*
	* @param 	integer $level 	Saturation level (-100 to 100)
	* @return 	object
	*/
	public final function contrast($level){

		if(!is_numeric($level) || $level < -100 || $level > 100){
			$this->error(__METHOD__, 'error_contrast_level');
		}

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
	public final function colorize($r, $g, $b){

		if(!is_numeric($r) || $r < -255 || $r > 255){
			$this->error(__METHOD__, 'error_colorize_r');
		}
		if(!is_numeric($g) || $g < -255 || $g > 255){
			$this->error(__METHOD__, 'error_colorize_g');
		}
		if(!is_numeric($b) || $b < -255 || $b > 255){
			$this->error(__METHOD__, 'error_colorize_b');
		}

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
	public final function pixelate($size=4){

		if(!is_numeric($size) || $size < 1){
			$this->error(__METHOD__, 'error_pixelate_size');
		}

		imagefilter($this->image_resource, IMG_FILTER_PIXELATE, $size);

		return $this;
	}

	/**
	* Apply a smoothing image processing filter to the image resource
	*
	* @param 	integer $level 	Smoothing level (0 to 100)
	* @return 	object
	*/
	public final function smooth($level){

		if(!is_numeric($level) || $level < 0 || $level > 100){
			$this->error(__METHOD__, 'error_smooth_level');
		}

		imagefilter($this->image_resource, IMG_FILTER_SMOOTH, $level);
		return $this;
	}

	/**
	* Apply a gaussian blurring image processing filter to the image resource
	*
	* @param 	integer $passes 	Number of filter passes to apply
	* @return 	object
	*/
	public final function blur($passes=1){

		if(!is_numeric($passes) || $passes < 1){
			$this->error(__METHOD__.': A invalid value was supplied for passes.');
		}

		for($i=0; $i<$passes; $i++){
			imagefilter($this->image_resource, IMG_FILTER_GAUSSIAN_BLUR);
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
	public final function replace($r, $g, $b, $r2, $g2, $b2){

		if(!is_numeric($r) || $r < -255 || $r > 255){
			$this->error(__METHOD__, 'error_replace_r');
		}
		if(!is_numeric($g) || $g < -255 || $g > 255){
			$this->error(__METHOD__, 'error_replace_g');
		}
		if(!is_numeric($b) || $b < -255 || $b > 255){
			$this->error(__METHOD__, 'error_replace_b');
		}
		if(!is_numeric($r2) || $r2 < -255 || $r2 > 255){
			$this->error(__METHOD__, 'error_replace_r2');
		}
		if(!is_numeric($g2) || $g2 < -255 || $g2 > 255){
			$this->error(__METHOD__, 'error_replace_g2');
		}
		if(!is_numeric($b2) || $b2 < -255 || $b2 > 255){
			$this->error(__METHOD__, 'error_replace_b2');
		}

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
	public final function desaturate($level){

		$orig_x = imagesx($this->image_resource);
		$orig_y = imagesy($this->image_resource);

		if(!is_numeric($level) || $level < 0 || $level > 100){
			$this->error(__METHOD__, 'error_desaturate_level');
		}

		imagecopymergegray($this->image_resource, $this->image_resource, 0, 0, 0, 0, $orig_x, $orig_y, $level);

		return $this;
	}

	////////////////////////////////////////////////////////////////////////////////
	// Effect Methods
	////////////////////////////////////////////////////////////////////////////////

	/**
	 * Overlays a vignette effect on top of the current image.
	 *
	 * @param  float 	$size 	Size (0 - 10)
	 * @return object
	 */
	public final function vignette($size=0.4){

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
	public final function noise($level=20){
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
	public final function blackwhite($level=20){
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
	public final function watermark($image_path, $position=''){
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
	public final function layer($image_path, $opacity=50){

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

		$params = ['x' => 30, 'y' => 0, 'width' => ($ox-30), 'height' => $oy];
		$output = imagecrop($output, $params);

		$this->image_resource = $output;
		return $this;

	}


	////////////////////////////////////////////////////////////////////////////////
	// Text Methods
	////////////////////////////////////////////////////////////////////////////////

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
	public final function text($text, $size, $angle, $x, $y, $r, $g, $b, $font_path=''){

		$color = imagecolorallocate($this->image_resource, $r, $g, $b);
		if($font_path != ''){
			imagettftext($this->image_resource, $size, $angle, $x, $y, $color, $font_path, $text);
		} else {
			$size = ($size > 5) ? 5 : $size;
			imagestring($this->image_resource, $size, $x, $y, $text, $color);
		}
		return $this;
	}

	////////////////////////////////////////////////////////////////////////////////
	// Experimental Methods
	////////////////////////////////////////////////////////////////////////////////

	/**
	 * Performs a convolution between an image and a 3x3 kernel.
	 * https://en.wikipedia.org/wiki/Kernel_(image_processing)
	 * 
	 * @param  array $matrix1 array of convolution values for top row
	 * @param  array $matrix2 array of convolution values for middle row
	 * @param  array $matrix3 array of convolution values for bottom row
	 * @return object
	 */
	public final function convolution($matrix1, $matrix2, $matrix3){
		$matrix = [$matrix1, $matrix2, $matrix3];
		imageconvolution($this->image_resource, $matrix, 1, 127);
		return $this;
	}

	////////////////////////////////////////////////////////////////////////////////
	// Utility Methods
	////////////////////////////////////////////////////////////////////////////////

	/**
	 * Re-assigns the image output mime type to a given type.
	 *
	 * @param  string 	$format 	Conversion format (JPEG, PNG, GIF)
	 * @return object
	 */
	public final function convert($format){
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
	public final function write($image_path){
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
	public final function script($script_path){
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