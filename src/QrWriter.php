<?php

namespace QrCode;

use SimpleXMLElement;
use Endroid\QrCode\QrCodeInterface;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\Exception\ValidationException;
use Endroid\QrCode\Exception\GenerateImageException;
use Endroid\QrCode\Exception\MissingLogoHeightException;

class QrWriter extends SvgWriter
{

	const FRAME_ES = 'es';
	const FRAME_EN = 'en';

	/**
	 * QR options
	 *
	 * @var array
	 */
	private $options;

	/**
	 * QR details
	 *
	 * @var array
	 */
	private $data;

	/**
	 * Main SVG object
	 *
	 * @var SimpleXMLElement
	 */
	private $svg;

	/**
	 * QR Size
	 *
	 * @var int
	 */
	private $size;

	/**
	 * Frame lang
	 *
	 * @var string
	 */
	private $lang;

	/**
	 * Instance new writer and define frame lang, null for disable
	 *
	 * @param string $frame
	 */
	public function __construct($frame = null) 
	{
		if (!empty($frame) && in_array($frame, ['es', 'en'])) {
			$this->lang = $frame;
		}
	}

	private function initSvgElements($qrCode)
	{
		$width = $this->data['outer_width'];
		$height = $this->data['outer_height'];

		if (!empty($this->lang)) {
			$height += $height * 0.2;
		}

		// Header
		$this->svg = new SimpleXMLElement('<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"/>');
		$this->svg->addAttribute('version', '1.1');
		$this->svg->addAttribute('width', $width . 'px');
		$this->svg->addAttribute('height', $height . 'px');
		$this->svg->addAttribute('viewBox', '0 0 ' . $width . ' ' . $height);

		$this->svg->addChild('defs');

		$this->initBlockDefinitions($qrCode);

		// Background
		$background = $this->svg->addChild('rect');
		$background->addAttribute('x', '0');
		$background->addAttribute('y', '0');
		$background->addAttribute('width', strval($width));
		$background->addAttribute('height', strval($height));
		$background->addAttribute('fill', '#' . sprintf('%02x%02x%02x', $qrCode->getBackgroundColor()['r'], $qrCode->getBackgroundColor()['g'], $qrCode->getBackgroundColor()['b']));
		$background->addAttribute('fill-opacity', strval($this->getOpacity($qrCode->getBackgroundColor()['a'])));

		// Frame
		$scale = $width / 340;
		$frame = $this->svg->addChild('g');
		$frame->addAttribute('style', 'transform: scale('.$scale.')');

		if ($this->lang == 'en') {
			$frame->addChild('polygon')->addAttribute('points', '145.8,366.7 151.4,366.7 148.6,359.5');
			$frame->addChild('path')->addAttribute('d', 'M315-0.3H25c-13.8,0-25,11.2-25,25v350c0,13.8,11.2,25,25,25h290c13.8,0,25-11.2,25-25v-350C340,10.9,328.8-0.3,315-0.3z
				M106.9,368.8c0,1.3-0.2,2.4-0.7,3.5c-0.5,1-1.2,1.9-2.1,2.6c-0.9,0.7-2,1.2-3.2,1.6s-2.6,0.6-4.2,0.6c-2.3,0-4.4-0.3-6.4-1
			c-2-0.7-3.8-1.8-5.4-3.2l3.9-4.7c2.5,2,5.2,3,8.2,3c0.9,0,1.7-0.1,2.1-0.4c0.5-0.3,0.7-0.7,0.7-1.3v-0.1c0-0.3-0.1-0.5-0.2-0.7
			c-0.1-0.2-0.3-0.4-0.7-0.6s-0.8-0.4-1.3-0.6c-0.5-0.2-1.2-0.4-2.1-0.6c-1.3-0.3-2.6-0.7-3.7-1c-1.2-0.4-2.2-0.8-3-1.4
			c-0.9-0.6-1.5-1.3-2-2.2c-0.5-0.9-0.7-2-0.7-3.3V359c0-1.2,0.2-2.3,0.7-3.2s1.1-1.8,1.9-2.6c0.9-0.7,1.9-1.3,3.1-1.7
			c1.2-0.4,2.6-0.6,4.1-0.6c2.2,0,4.1,0.3,5.8,0.9s3.2,1.4,4.6,2.5l-3.5,4.9c-1.2-0.8-2.3-1.4-3.5-1.9s-2.4-0.6-3.5-0.6
			c-0.8,0-1.5,0.2-1.9,0.5c-0.4,0.3-0.6,0.7-0.6,1.2v0.1c0,0.3,0.1,0.5,0.2,0.8s0.4,0.4,0.7,0.6c0.3,0.2,0.8,0.4,1.3,0.5
			c0.6,0.2,1.3,0.4,2.1,0.6c1.4,0.3,2.7,0.7,3.9,1.1s2.2,0.9,3,1.5c0.8,0.6,1.4,1.3,1.9,2.2S106.9,367.6,106.9,368.8L106.9,368.8z
				M131.6,373.7c-0.7,0.7-1.5,1.3-2.4,1.8c-0.9,0.5-1.9,0.9-3,1.2s-2.4,0.4-3.8,0.4c-1.8,0-3.5-0.3-5.1-1c-1.6-0.6-3-1.6-4.1-2.7
			s-2.1-2.5-2.8-4.1c-0.7-1.6-1-3.3-1-5.2V364c0-1.8,0.3-3.5,1-5.1c0.7-1.6,1.6-3,2.8-4.2c1.2-1.2,2.6-2.1,4.2-2.8
			c1.6-0.7,3.4-1,5.3-1c1.3,0,2.5,0.1,3.5,0.4s2,0.6,2.9,1.1c0.9,0.5,1.7,1,2.4,1.7c0.7,0.6,1.3,1.4,1.9,2.1l-5.3,4.1
			c-0.7-0.9-1.5-1.6-2.4-2.2c-0.9-0.5-1.9-0.8-3.1-0.8c-0.9,0-1.7,0.2-2.4,0.5c-0.7,0.3-1.4,0.8-1.9,1.4s-0.9,1.3-1.2,2.1
			c-0.3,0.8-0.4,1.7-0.4,2.6v0.1c0,0.9,0.1,1.8,0.4,2.6s0.7,1.5,1.2,2.1c0.5,0.6,1.2,1.1,1.9,1.4c0.7,0.3,1.5,0.5,2.4,0.5
			c0.6,0,1.2-0.1,1.8-0.2s1-0.4,1.5-0.6c0.4-0.3,0.9-0.6,1.3-1c0.4-0.4,0.8-0.8,1.2-1.2l5.3,3.7C133,372.2,132.3,373,131.6,373.7z
				M155.3,376.6l-1.8-4.5h-9.7l-1.8,4.5h-7.3l10.7-25.4h6.7l10.7,25.4H155.3z M189,376.6h-6.1l-10.8-13.8v13.8h-6.9v-25.2h6.5
			l10.4,13.3v-13.3h6.9V376.6z M230.2,376.6h-6.9v-14.5l-6.4,9.9h-0.1l-6.4-9.8v14.4h-6.9v-25.2h7.4l6,9.8l6-9.8h7.4V376.6z
				M255.1,376.6h-20.4v-25.2h20.2v5.9h-13.4v3.8h12.1v5.5h-12.1v4h13.5V376.6z M330,314.7c0,8.3-6.7,15-15,15H25c-8.3,0-15-6.7-15-15
			v-290c0-8.3,6.7-15,15-15h290c8.3,0,15,6.7,15,15V314.7z');
		} 
		
		if ($this->lang == 'es') {
			$frame->addChild('polygon')->addAttribute('points', '135.9,366.2 141.6,366.2 138.8,359.1');
			$frame->addChild('polygon')->addAttribute('points', '216.8,366.2 222.5,366.2 219.6,359.1');
			$frame->addChild('path')->addAttribute('d', 'M315-0.3H25c-13.8,0-25,11.2-25,25v350c0,13.8,11.2,25,25,25h290c13.8,0,25-11.2,25-25v-350C340,10.9,328.8-0.3,315-0.3z
				M73.4,376.2H53V351h20.2v5.9H59.8v3.8h12.1v5.5H59.8v4h13.5V376.2z M97.1,368.4c0,1.3-0.2,2.4-0.7,3.5c-0.5,1-1.2,1.9-2.1,2.6
				c-0.9,0.7-2,1.2-3.2,1.6s-2.6,0.6-4.2,0.6c-2.3,0-4.4-0.3-6.4-1c-2-0.7-3.8-1.8-5.4-3.2l3.9-4.7c2.5,2,5.2,3,8.2,3
				c0.9,0,1.7-0.1,2.1-0.4c0.5-0.3,0.7-0.7,0.7-1.3V369c0-0.3-0.1-0.5-0.2-0.7c-0.1-0.2-0.3-0.4-0.7-0.6s-0.8-0.4-1.3-0.6
				c-0.5-0.2-1.2-0.4-2.1-0.6c-1.3-0.3-2.6-0.7-3.7-1c-1.2-0.4-2.2-0.8-3-1.4c-0.9-0.6-1.5-1.3-2-2.2c-0.5-0.9-0.7-2-0.7-3.3v-0.1
				c0-1.2,0.2-2.3,0.7-3.2s1.1-1.8,1.9-2.6c0.9-0.7,1.9-1.3,3.1-1.7c1.2-0.4,2.6-0.6,4.1-0.6c2.2,0,4.1,0.3,5.8,0.9s3.2,1.4,4.6,2.5
				l-3.5,4.9c-1.2-0.8-2.3-1.4-3.5-1.9s-2.4-0.6-3.5-0.6c-0.8,0-1.5,0.2-1.9,0.5c-0.4,0.3-0.6,0.7-0.6,1.2v0.1c0,0.3,0.1,0.5,0.2,0.8
				s0.4,0.4,0.7,0.6c0.3,0.2,0.8,0.4,1.3,0.5c0.6,0.2,1.3,0.4,2.1,0.6c1.4,0.3,2.7,0.7,3.9,1.1s2.2,0.9,3,1.5c0.8,0.6,1.4,1.3,1.9,2.2
				S97.1,367.1,97.1,368.4L97.1,368.4z M121.8,373.2c-0.7,0.7-1.5,1.3-2.4,1.8c-0.9,0.5-1.9,0.9-3,1.2s-2.4,0.4-3.8,0.4
				c-1.8,0-3.5-0.3-5.1-1c-1.6-0.6-3-1.6-4.1-2.7s-2.1-2.5-2.8-4.1c-0.7-1.6-1-3.3-1-5.2v-0.1c0-1.8,0.3-3.5,1-5.1
				c0.7-1.6,1.6-3,2.8-4.2c1.2-1.2,2.6-2.1,4.2-2.8c1.6-0.7,3.4-1,5.3-1c1.3,0,2.5,0.1,3.5,0.4s2,0.6,2.9,1.1c0.9,0.5,1.7,1,2.4,1.7
				c0.7,0.6,1.3,1.4,1.9,2.1l-5.3,4.1c-0.7-0.9-1.5-1.6-2.4-2.2c-0.9-0.5-1.9-0.8-3.1-0.8c-0.9,0-1.7,0.2-2.4,0.5
				c-0.7,0.3-1.4,0.8-1.9,1.4s-0.9,1.3-1.2,2.1c-0.3,0.8-0.4,1.7-0.4,2.6v0.1c0,0.9,0.1,1.8,0.4,2.6s0.7,1.5,1.2,2.1
				c0.5,0.6,1.2,1.1,1.9,1.4c0.7,0.3,1.5,0.5,2.4,0.5c0.6,0,1.2-0.1,1.8-0.2s1-0.4,1.5-0.6c0.4-0.3,0.9-0.6,1.3-1
				c0.4-0.4,0.8-0.8,1.2-1.2l5.3,3.7C123.1,371.8,122.5,372.5,121.8,373.2z M145.4,376.2l-1.8-4.5h-9.7l-1.8,4.5h-7.3l10.7-25.4h6.7
				l10.7,25.4H145.4z M179.1,376.2H173l-10.8-13.8v13.8h-6.9V351h6.5l10.4,13.3V351h6.9V376.2z M204,376.2h-20.4V351h20.2v5.9h-13.4
				v3.8h12.1v5.5h-12.1v4H204V376.2z M226.3,376.2l-1.8-4.5h-9.7l-1.8,4.5h-7.3l10.7-25.4h6.7l10.7,25.4H226.3z M263,376.2h-6.9v-14.5
				l-6.4,9.9h-0.1l-6.4-9.8v14.4h-6.9V351h7.4l6,9.8l6-9.8h7.4V376.2z M287.9,376.2h-20.4V351h20.2v5.9h-13.4v3.8h12.1v5.5h-12.1v4
				h13.5V376.2z M330,314.7c0,8.3-6.7,15-15,15H25c-8.3,0-15-6.7-15-15v-290c0-8.3,6.7-15,15-15h290c8.3,0,15,6.7,15,15V314.7z');
		}
	}

	private function initBlockDefinitions()
	{
		$block_size = strval($this->data['block_size']);

		$this->generateClipWihCorners('full-corner', 0, 0, $block_size, $block_size, $block_size / 2);

		$this->generateClipWihCorners('top-corner', 0, 0, $block_size, $block_size * 2, $block_size / 2);
		$this->generateClipWihCorners('bottom-corner', 0, -$block_size, $block_size, $block_size * 2, $block_size / 2);
		$this->generateClipWihCorners('left-corner', 0, 0, $block_size * 2, $block_size, $block_size / 2);
		$this->generateClipWihCorners('right-corner', -$block_size, 0, $block_size * 2, $block_size, $block_size / 2);

		$this->generateClipWihCorners('left-top-corner', 0, 0, $block_size * 2, $block_size * 2, $block_size / 2);
		$this->generateClipWihCorners('right-top-corner', -$block_size, 0, $block_size * 2, $block_size * 2, $block_size / 2);
		$this->generateClipWihCorners('left-bottom-corner', 0, -$block_size, $block_size * 2, $block_size * 2, $block_size / 2);
		$this->generateClipWihCorners('right-bottom-corner', -$block_size, -$block_size, $block_size * 2, $block_size * 2, $block_size / 2);

		$this->generateClipWihCorners('left-top-eye-corner', 0, 0, $block_size * 2, $block_size * 2, $block_size);
		$this->generateClipWihCorners('right-top-eye-corner', -$block_size, 0, $block_size * 2, $block_size * 2, $block_size);
		$this->generateClipWihCorners('left-bottom-eye-corner', 0, -$block_size, $block_size * 2, $block_size * 2, $block_size);
		$this->generateClipWihCorners('right-bottom-eye-corner', -$block_size, -$block_size, $block_size * 2, $block_size * 2, $block_size);
	}

	private function generateClipWihCorners($id, $x, $y, $width, $height, $radius = 5)
	{
		$clip = $this->svg->defs->addChild('clipPath');
		$clip->addAttribute('id', $id);

		$rect = $clip->addChild('rect');
		$rect->addAttribute('x', $x);
		$rect->addAttribute('y', $y);
		$rect->addAttribute('width', $width);
		$rect->addAttribute('height', $height);
		$rect->addAttribute('rx', $radius);
		$rect->addAttribute('ry', $radius);
	}

	private function detectEyesPositions()
	{
		$eye_size = 0;

		foreach ($this->data['matrix'][0] as $column => $value) {
			if ($value === 0) {
				$eye_size = $column;
				break;
			}
		}

		$this->data['eyes'] = array();
		$eye_start = $this->size - $eye_size;

		foreach ($this->data['matrix'] as $row => $values) {
			$this->data['eyes'][$row] = array();

			foreach ($values as $column => $value) {
				$this->data['eyes'][$row][$column] = 0;

				if (
					$row < $eye_size && $column < $eye_size ||
					$row < $eye_size && $column >= $eye_start ||
					$column < $eye_size && $row >= $eye_start
				) {
					$this->data['eyes'][$row][$column] = 1;
				}
			}
		}

		$this->data['eye_size'] = $eye_size;
	}

	private function getMaskId($row, $column)
	{
		if ($this->isTopActive($row, $column) && !$this->isBottomActive($row, $column) && !$this->isHorizontalActive($row, $column)) {
			return 'bottom';
		}

		if (!$this->isTopActive($row, $column) && $this->isBottomActive($row, $column) && !$this->isHorizontalActive($row, $column)) {
			return 'top';
		}

		if ($this->isLeftActive($row, $column) && !$this->isRightActive($row, $column) && !$this->isVerticalActive($row, $column)) {
			return 'right';
		}

		if (!$this->isLeftActive($row, $column) && $this->isRightActive($row, $column) && !$this->isVerticalActive($row, $column)) {
			return 'left';
		}

		if (!$this->isVerticalActive($row, $column, true) && !$this->isHorizontalActive($row, $column, true)) {
			return 'full';
		}

		if ($border = $this->getActiveBorderId($row, $column)) {
			return $border;
		}

		return 'none';
	}

	private function getActiveBorderId($row, $column) {
		if (!$this->isTopActive($row, $column) && !$this->isLeftActive($row, $column)) {
			return 'left-top';
		}

		if (!$this->isTopActive($row, $column) && !$this->isRightActive($row, $column)) {
			return 'right-top';
		}

		if (!$this->isBottomActive($row, $column) && !$this->isLeftActive($row, $column)) {
			return 'left-bottom';
		}

		if (!$this->isBottomActive($row, $column) && !$this->isRightActive($row, $column)) {
			return 'right-bottom';
		}
	}

	private function isEyeZone($row, $column)
	{
		return boolval($this->data['eyes'][$row][$column]);
	}

	private function isActiveBlock($row, $column)
	{
		return boolval($this->data['matrix'][$row][$column]);
	}

	private function isTopActive($row, $column)
	{
		$row--;

		return $row >= 0 && $this->isActiveBlock($row, $column);
	}

	private function isBottomActive($row, $column)
	{
		$row++;

		return $row <= $this->size && $this->isActiveBlock($row, $column);
	}

	private function isLeftActive($row, $column)
	{
		$column--;

		return $column >= 0 && $this->isActiveBlock($row, $column);
	}

	private function isRightActive($row, $column)
	{
		$column++;

		return $column <= $this->size && $this->isActiveBlock($row, $column);
	}

	private function isVerticalActive($row, $column, $strict = false)
	{
		if ($strict && $this->isTopActive($row, $column) && $this->isBottomActive($row, $column)) {
			return true;
		}

		return $this->isTopActive($row, $column) || $this->isBottomActive($row, $column);
	}

	private function isHorizontalActive($row, $column, $strict = false)
	{
		if ($strict && $this->isLeftActive($row, $column) && $this->isRightActive($row, $column)) {
			return true;
		}

		return $this->isLeftActive($row, $column) || $this->isRightActive($row, $column);
	}

	public function writeString(QrCodeInterface $qrCode): string
	{
		$this->options = $qrCode->getWriterOptions();

		if ($qrCode->getValidateResult()) {
			throw new ValidationException('Built-in validation reader can not check SVG images: please disable via setValidateResult(false)');
		}

		$this->data = $qrCode->getData();
		$this->size = $this->data['block_count'];

		$this->initSvgElements($qrCode);
		$this->detectEyesPositions();

		foreach ($this->data['matrix'] as $row => $values) {
			foreach ($values as $column => $value) {
				if ($value === 1) {
					$this->addBlock($row, $column, $qrCode);
				}

				if ($value === 0 && $this->isEyeZone($row, $column) && $this->getActiveBorderId($row, $column)) {
					// TODO: Add corners
				}
			}
		}

		$logoPath = $qrCode->getLogoPath();

		if (is_string($logoPath)) {
			$this->addLogo($logoPath, $qrCode);
		}

		$xml = $this->svg->asXML();

		if (!is_string($xml)) {
			throw new GenerateImageException('Unable to save SVG XML');
		}

		if (isset($this->options['exclude_xml_declaration']) && $this->options['exclude_xml_declaration']) {
			$xml = str_replace("<?xml version=\"1.0\"?>\n", '', $xml);
		}

		return $xml;
	}

	private function addBlock($row, $column, $qrCode, $prefix = 'corner') {
		$x = strval($this->data['margin_left'] + $this->data['block_size'] * $column);
		$y = strval($this->data['margin_left'] + $this->data['block_size'] * $row);
		$id = $this->getMaskId($row, $column);

		if ($this->isEyeZone($row, $column)) {
			$id .= '-eye';
		}

		$block = $this->svg->addChild('rect');
		$block->addAttribute('x', 0);
		$block->addAttribute('y', 0);
		$block->addAttribute('width', intval($this->data['block_size']));
		$block->addAttribute('height', intval($this->data['block_size']));
		$block->addAttribute('style', 'transform: translate(' . $x . 'px, ' . $y . 'px)');
		$block->addAttribute('clip-path', 'url(#'.$id.'-'.$prefix);
		$block->addAttribute('fill', '#' . sprintf('%02x%02x%02x', $qrCode->getForegroundColor()['r'], $qrCode->getForegroundColor()['g'], $qrCode->getForegroundColor()['b']));
		$block->addAttribute('fill-opacity', strval($this->getOpacity($qrCode->getForegroundColor()['a'])));
	}

	private function addLogo($logoPath, $qrCode): void
	{
		$imageWidth = $this->data['outer_width'];
		$imageHeight = $this->data['outer_height']; 
		$logoWidth = $qrCode->getLogoWidth();
		$logoHeight = $qrCode->getLogoHeight();
		$forceXlinkHref = isset($this->options['force_xlink_href']) && $this->options['force_xlink_href'];

		$mimeType = $this->getMimeType($logoPath);
		$imageData = file_get_contents($logoPath);

		if (!is_string($imageData)) {
			throw new GenerateImageException('Unable to read image data: check your logo path');
		}

		if ('image/svg+xml' === $mimeType && (null === $logoHeight || null === $logoWidth)) {
			throw new MissingLogoHeightException('SVG Logos require an explicit height set via setLogoSize($width, $height)');
		}

		if (null === $logoHeight || null === $logoWidth) {
			$logoImage = imagecreatefromstring(strval($imageData));

			if (!is_resource($logoImage)) {
				throw new GenerateImageException('Unable to generate image: check your GD installation or logo path');
			}

			$logoSourceWidth = imagesx($logoImage);
			$logoSourceHeight = imagesy($logoImage);

			imagedestroy($logoImage);

			if (null === $logoWidth) {
				$logoWidth = $logoSourceWidth;
			}

			if (null === $logoHeight) {
				$aspectRatio = $logoWidth / $logoSourceWidth;
				$logoHeight = intval($logoSourceHeight * $aspectRatio);
			}
		}

		$logoX = $imageWidth / 2 - $logoWidth / 2;
		$logoY = $imageHeight / 2 - $logoHeight / 2;

		$imageDefinition = $this->svg->addChild('image');
		$imageDefinition->addAttribute('x', strval($logoX));
		$imageDefinition->addAttribute('y', strval($logoY));
		$imageDefinition->addAttribute('width', strval($logoWidth));
		$imageDefinition->addAttribute('height', strval($logoHeight));
		$imageDefinition->addAttribute('preserveAspectRatio', 'none');

		if ($forceXlinkHref) {
			$imageDefinition['xlink:href'] = 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
		} else {
			$imageDefinition->addAttribute('href', 'data:' . $mimeType . ';base64,' . base64_encode($imageData));
		}
	}

	private function getOpacity(int $alpha): float
	{
		return 1 - $alpha / 127;
	}
}
