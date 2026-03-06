<?php

declare(strict_types=1);

namespace OCA\NcwMailtemplate;

use OCP\IConfig;

/**
 * Resolves brand-specific template and image paths with fallback to the default brand.
 *
 * Reads the `ncw.brand` system config value to determine the active brand.
 * For each template file or image, it first checks if a brand-specific version exists.
 * If not, it falls back to the default brand ('IONOS').
 */
class BrandResolver {
	public const DEFAULT_BRAND = 'IONOS';

	private string $brand;
	private string $templatesBasePath;
	private string $imgBasePath;

	public function __construct(IConfig $config) {
		$brand = $config->getSystemValueString('ncw.brand', self::DEFAULT_BRAND);

		// Sanitize: only allow alphanumeric, dash, and underscore to prevent path traversal
		if (!preg_match('/^[a-zA-Z0-9_-]+$/', $brand)) {
			$brand = self::DEFAULT_BRAND;
		}

		// Lowercase to match folder names on disk regardless of config casing
		$brand = strtolower($brand);

		$this->brand = $brand;
		$this->templatesBasePath = __DIR__ . '/templates/email';
		$this->imgBasePath = __DIR__ . '/../img';
	}

	/**
	 * Get the active brand identifier.
	 */
	public function getBrand(): string {
		return $this->brand;
	}

	/**
	 * Resolve a template file path with brand fallback.
	 *
	 * If the brand-specific file exists, return its path.
	 * Otherwise, fall back to the default brand's version.
	 *
	 * @param string $fileName The template file name (e.g. 'header.html')
	 * @return string The resolved absolute file path
	 */
	public function resolveTemplatePath(string $fileName): string {
		$defaultFolder = self::defaultBrandFolder();

		if ($this->brand !== $defaultFolder) {
			$brandPath = $this->templatesBasePath . '/' . $this->brand . '/' . $fileName;
			if (file_exists($brandPath)) {
				return $brandPath;
			}
		}

		return $this->templatesBasePath . '/' . $defaultFolder . '/' . $fileName;
	}

	/**
	 * Resolve an image name with brand fallback.
	 *
	 * Returns the app-relative image path (e.g. 'ionos/logo.png') for use
	 * with IURLGenerator::imagePath().
	 *
	 * If the brand-specific image exists on disk, return '<brand>/<imageName>'.
	 * Otherwise, fall back to '<defaultBrandFolder>/<imageName>'.
	 *
	 * @param string $imageName The image file name (e.g. 'logo.png')
	 * @return string The resolved app-relative image path
	 */
	public function resolveImageName(string $imageName): string {
		$defaultFolder = self::defaultBrandFolder();

		if ($this->brand !== $defaultFolder) {
			$brandImagePath = $this->imgBasePath . '/' . $this->brand . '/' . $imageName;
			if (file_exists($brandImagePath)) {
				return $this->brand . '/' . $imageName;
			}
		}

		return $defaultFolder . '/' . $imageName;
	}

	/**
	 * Returns the lowercase folder name for the default brand.
	 * Brand folders on disk use lowercase names.
	 */
	private static function defaultBrandFolder(): string {
		return strtolower(self::DEFAULT_BRAND);
	}
}
