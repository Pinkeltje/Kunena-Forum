<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Image
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Image;

\defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Image\Image;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use RuntimeException;

define('MIME_GIF', 'image/gif');
define('MIME_PNG', 'image/png');

/**
 * Helper class for image manipulation.
 *
 * @since   Kunena 6.0
 */
class KunenaImage extends Image
{
	/**
	 * Correct Image Orientation
	 *
	 * @param   string  $filename  filename
	 *
	 * @return  void
	 *
	 * @since   Kunena 5.0
	 */
	public static function correctImageOrientation($filename)
	{
		$img = new Image;

		try
		{
			$img->loadFile($filename);
		}
		catch (Exception $e)
		{
			throw new RuntimeException($e->getMessage(), 500);
		}

		$angle  = 0;
		$flip   = 0;

		$exif = exif_read_data($filename);

		if ($exif && isset($exif['Orientation']))
		{
			$orientation = $exif['Orientation'];

			if ($orientation != 1)
			{

				switch ($orientation)
				{
					case 1: // Nothing
						$angle  = 0;
						$flip = 0;
						break;

					case 2: // Horizontal flip
						$angle  = 0;
						$flip = 1;
						break;

					case 3: // 180 rotate left
						$angle  = 180;
						$flip = 0;
						break;

					case 4: // Vertical flip
						$angle  = 0;
						$flip = 2;
						break;

					case 5: // Vertical flip + 90 rotate
						$angle  = 90;
						$flip = 2;
						break;

					case 6: // 270 rotate left
						$angle  = 270;
						$flip = 0;
						break;

					case 7: // Horizontal flip + 90 rotate
						$angle  = 90;
						$flip = 1;
						break;

					case 8: // 90 rotate left
						$angle  = 90;
						$flip = 0;
						break;
				}
			}
		}

		if ($angle > 0)
		{
			$img->rotate($angle, -1, false);
		}

		if ($flip != 0)
		{
			if ($flip == 1)
			{
				$img->flip(IMG_FLIP_HORIZONTAL, false);
			}
			else
			{
				$img->flip(IMG_FLIP_VERTICAL, false);
			}
		}
	}

	/**
	 * Method to resize the current image. This is needed to handle gif and png with transparent background because the method resize() in Joomla\CMS\Image doesn't work with that.
	 *
	 * @param   mixed    $width        The width of the resized image in pixels or a percentage.
	 * @param   mixed    $height       The height of the resized image in pixels or a percentage.
	 * @param   boolean  $createNew    If true the current image will be cloned, resized and returned; else
	 *                                 the current image will be resized and returned.
	 * @param   integer  $scaleMethod  Which method to use for scaling
	 *
	 * @return  Image
	 *
	 * @since   4.0.0
	 * @throws  \LogicException
	 */
	public function resize($width, $height, $createNew = true, $scaleMethod = self::SCALE_INSIDE)
	{
	    /* $config = KunenaFactory::getConfig();
	    
	    switch ($config->avatarresizemethod)
	    {
	        case '0':*/
	            $resizemethod = 'imagecopyresized';
	          /*  break;
	        case '1':
	            $resizemethod = 'imagecopyresampled';
	            break;
	        default:
	            $resizemethod = 'self::imageCopyResampledBicubic';
	            break;
	    }*/

		// Make sure the resource handle is valid.
		if (!$this->isLoaded())
		{
			throw new \LogicException('No valid image was loaded.');
		}

		// Sanitize width.
		$width = $this->sanitizeWidth($width, $height);

		// Sanitize height.
		$height = $this->sanitizeHeight($height, $width);

		// Prepare the dimensions for the resize operation.
		$dimensions = $this->prepareDimensions($width, $height, $scaleMethod);

		// Instantiate offset.
		$offset    = new \stdClass;
		$offset->x = $offset->y = 0;

		// Get true color handle
		$handle = imagecreatetruecolor($dimensions->width, $dimensions->height);

		// Center image if needed and create the new true color image handle.
		if ($scaleMethod == self::SCALE_FIT)
		{
			// Get the offsets
			$offset->x = round(($width - $dimensions->width) / 2);
			$offset->y = round(($height - $dimensions->height) / 2);

			// Make image transparent, otherwise canvas outside initial image would default to black
			if (!$this->isTransparent())
			{
				$transparency = imagecolorAllocateAlpha($this->handle, 0, 0, 0, 127);
				imagecolorTransparent($this->handle, $transparency);
			}
		}

		$imgProperties = self::getImageFileProperties($this->getPath());

		if ($imgProperties->mime == MIME_GIF)
		{
			$trnprt_indx = imagecolortransparent($this->handle);

			if ($trnprt_indx >= 0 && $trnprt_indx < imagecolorstotal($this->handle))
			{
				$trnprt_color = imagecolorsforindex($this->handle, $trnprt_indx);
				$trnprt_indx  = imagecolorallocate($handle, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
				imagefill($handle, 0, 0, $trnprt_indx);
				imagecolortransparent($handle, $trnprt_indx);
			}
		}
		elseif ($imgProperties->mime == MIME_PNG)
		{
			imagealphablending($handle, false);
			imagesavealpha($handle, true);

			if ($this->isTransparent())
			{
				$transparent = imagecolorallocatealpha($this->handle, 255, 255, 255, 127);
				imagefilledrectangle($this->handle, 0, 0, $width, $height, $transparent);
			}
		}

		if ($this->isTransparent())
		{
			$trnprt_indx = imagecolortransparent($this->handle);

			if ($trnprt_indx >= 0 && $trnprt_indx < imagecolorstotal($this->handle))
			{
				// Get the transparent color values for the current image.
				$rgba  = imageColorsForIndex($this->handle, imagecolortransparent($this->handle));
				$color = imageColorAllocateAlpha($handle, $rgba['red'], $rgba['green'], $rgba['blue'], $rgba['alpha']);
			}
			else
			{
				$color = imageColorAllocateAlpha($handle, 255, 255, 255, 127);
			}

			// Set the transparent color values for the new image.
			imagecolortransparent($handle, $color);
			imagefill($handle, 0, 0, $color);

			imagecopyresized(
				$handle,
				$this->handle,
				$offset->x,
				$offset->y,
				0,
				0,
				$dimensions->width,
				$dimensions->height,
				$this->getWidth(),
				$this->getHeight()
				);
		}
		else
		{
			call_user_func_array($resizemethod, array(&$handle, &$this->handle, $offset->x, $offset->y, 0, 0,
				$dimensions->width, $dimensions->height, $this->getWidth(), $this->getHeight(), )
				);
		}

		// If we are resizing to a new image, create a new KunenaImage object.
		if ($createNew)
		{
			// @codeCoverageIgnoreStart
			$new = new KunenaImage($handle);

			return $new;

			// @codeCoverageIgnoreEnd
		}
		// Swap out the current handle for the new image handle.
		else
		{
			// Free the memory from the current handle
			$this->destroy();

			$this->handle = $handle;

			return $this;
		}
	}
}
