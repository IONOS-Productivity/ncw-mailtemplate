<?php

declare(strict_types=1);

namespace OCA\NcwMailtemplate\Tests\Unit;

use OCA\NcwMailtemplate\BrandResolver;
use OCP\IConfig;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BrandResolverTest extends TestCase {
	private IConfig&MockObject $config;

	private string $templatesBasePath;
	private string $imgBasePath;

	protected function setUp(): void {
		parent::setUp();
		$this->config = $this->createMock(IConfig::class);
		$this->templatesBasePath = dirname(__DIR__, 2) . '/lib/templates/email';
		$this->imgBasePath = dirname(__DIR__, 2) . '/img';
	}

	private function createResolver(string $brand = 'ionos'): BrandResolver {
		$this->config
			->method('getSystemValueString')
			->with('ncw.brand', BrandResolver::DEFAULT_BRAND)
			->willReturn($brand);

		return new BrandResolver($this->config);
	}

	// ── getBrand() ──────────────────────────────────────────────────────

	public function testGetBrandReturnsDefaultWhenNotConfigured(): void {
		$resolver = $this->createResolver('ionos');
		$this->assertSame('ionos', $resolver->getBrand());
	}

	public function testGetBrandReturnsConfiguredBrand(): void {
		$resolver = $this->createResolver('strato');
		$this->assertSame('strato', $resolver->getBrand());
	}

	public function testGetBrandFallsBackToDefaultForEmptyString(): void {
		$this->config
			->method('getSystemValueString')
			->with('ncw.brand', BrandResolver::DEFAULT_BRAND)
			->willReturn('');

		$resolver = new BrandResolver($this->config);
		$this->assertSame('ionos', $resolver->getBrand());
	}

	// ── Path traversal sanitization ─────────────────────────────────────

	public function testBrandWithPathTraversalFallsBackToDefault(): void {
		$resolver = $this->createResolver('../../../etc');
		$this->assertSame('ionos', $resolver->getBrand());
	}

	public function testBrandWithSlashesFallsBackToDefault(): void {
		$resolver = $this->createResolver('foo/bar');
		$this->assertSame('ionos', $resolver->getBrand());
	}

	public function testBrandWithDotsFallsBackToDefault(): void {
		$resolver = $this->createResolver('..');
		$this->assertSame('ionos', $resolver->getBrand());
	}

	public function testBrandWithSpecialCharsFallsBackToDefault(): void {
		$resolver = $this->createResolver('brand<script>');
		$this->assertSame('ionos', $resolver->getBrand());
	}

	public function testBrandWithSpacesFallsBackToDefault(): void {
		$resolver = $this->createResolver('my brand');
		$this->assertSame('ionos', $resolver->getBrand());
	}

	// ── Valid brand names with allowed characters ────────────────────────

	public function testBrandWithHyphensIsAccepted(): void {
		$resolver = $this->createResolver('my-brand');
		$this->assertSame('my-brand', $resolver->getBrand());
	}

	public function testBrandWithUnderscoresIsAccepted(): void {
		$resolver = $this->createResolver('my_brand');
		$this->assertSame('my_brand', $resolver->getBrand());
	}

	public function testBrandWithNumbersIsAccepted(): void {
		$resolver = $this->createResolver('brand123');
		$this->assertSame('brand123', $resolver->getBrand());
	}

	public function testBrandWithMixedCaseIsLowercased(): void {
		$resolver = $this->createResolver('MyBrand');
		$this->assertSame('mybrand', $resolver->getBrand());
	}

	public function testBrandWithUpperCaseIsLowercased(): void {
		$resolver = $this->createResolver('IONOS');
		$this->assertSame('ionos', $resolver->getBrand());
	}

	// ── resolveTemplatePath() ───────────────────────────────────────────

	public function testResolveTemplatePathReturnsDefaultBrandPath(): void {
		$resolver = $this->createResolver('ionos');
		$expected = $this->templatesBasePath . '/ionos/header.html';
		$this->assertSame($expected, $resolver->resolveTemplatePath('header.html'));
	}

	public function testResolveTemplatePathFallsBackToDefaultWhenBrandFileDoesNotExist(): void {
		$resolver = $this->createResolver('nonexistent_brand');
		$expected = $this->templatesBasePath . '/ionos/header.html';
		$this->assertSame($expected, $resolver->resolveTemplatePath('header.html'));
	}

	public function testResolveTemplatePathReturnsDefaultForAllKnownTemplates(): void {
		$resolver = $this->createResolver('ionos');

		$templateFiles = [
			'head.html',
			'header.html',
			'heading.html',
			'bodyBegin.html',
			'bodyText.html',
			'listBegin.html',
			'listItem.html',
			'listEnd.html',
			'buttonGroup.html',
			'button.html',
			'bodyEnd.html',
			'footer.html',
			'tail.html',
		];

		foreach ($templateFiles as $file) {
			$resolved = $resolver->resolveTemplatePath($file);
			$this->assertStringEndsWith('/ionos/' . $file, $resolved, "Template $file should resolve to ionos brand");
			$this->assertFileExists($resolved, "Template file $file should exist on disk");
		}
	}

	public function testResolveTemplatePathReturnsBrandSpecificWhenFileExists(): void {
		// Create a temporary brand directory with a template
		$tempBrand = 'test_brand_' . uniqid();
		$tempDir = $this->templatesBasePath . '/' . $tempBrand;
		mkdir($tempDir, 0777, true);
		$tempFile = $tempDir . '/header.html';
		file_put_contents($tempFile, '<h1>Test Brand Header</h1>');

		try {
			$resolver = $this->createResolver($tempBrand);
			$resolved = $resolver->resolveTemplatePath('header.html');
			$this->assertSame($tempFile, $resolved);
		} finally {
			// Clean up
			unlink($tempFile);
			rmdir($tempDir);
		}
	}

	public function testResolveTemplatePathFallsBackPerFileForPartialBrand(): void {
		// Create a brand that only overrides header.html but not footer.html
		$tempBrand = 'partial_brand_' . uniqid();
		$tempDir = $this->templatesBasePath . '/' . $tempBrand;
		mkdir($tempDir, 0777, true);
		$tempFile = $tempDir . '/header.html';
		file_put_contents($tempFile, '<h1>Partial Brand Header</h1>');

		try {
			$resolver = $this->createResolver($tempBrand);

			// header.html should come from the brand
			$resolvedHeader = $resolver->resolveTemplatePath('header.html');
			$this->assertStringContainsString($tempBrand, $resolvedHeader);

			// footer.html should fall back to ionos
			$resolvedFooter = $resolver->resolveTemplatePath('footer.html');
			$this->assertStringContainsString('/ionos/', $resolvedFooter);
		} finally {
			unlink($tempFile);
			rmdir($tempDir);
		}
	}

	// ── resolveImageName() ──────────────────────────────────────────────

	public function testResolveImageNameReturnsDefaultBrandPrefix(): void {
		$resolver = $this->createResolver('ionos');
		$this->assertSame('ionos/logo.png', $resolver->resolveImageName('logo.png'));
	}

	public function testResolveImageNameFallsBackToDefaultWhenBrandImageDoesNotExist(): void {
		$resolver = $this->createResolver('nonexistent_brand');
		$this->assertSame('ionos/logo.png', $resolver->resolveImageName('logo.png'));
	}

	public function testResolveImageNameReturnsDefaultForAllKnownImages(): void {
		$resolver = $this->createResolver('ionos');

		$imageFiles = [
			'spacer.png',
			'logo.png',
			'email.png',
			'list-item-icon.png',
		];

		foreach ($imageFiles as $file) {
			$resolved = $resolver->resolveImageName($file);
			$this->assertSame('ionos/' . $file, $resolved, "Image $file should resolve to ionos brand");

			// Also verify the file actually exists on disk
			$fullPath = $this->imgBasePath . '/' . $resolved;
			$this->assertFileExists($fullPath, "Image file $file should exist on disk at $fullPath");
		}
	}

	public function testResolveImageNameReturnsBrandSpecificWhenImageExists(): void {
		// Create a temporary brand image directory
		$tempBrand = 'test_brand_img_' . uniqid();
		$tempDir = $this->imgBasePath . '/' . $tempBrand;
		mkdir($tempDir, 0777, true);
		$tempFile = $tempDir . '/logo.png';
		file_put_contents($tempFile, 'fake-png-data');

		try {
			$resolver = $this->createResolver($tempBrand);
			$resolved = $resolver->resolveImageName('logo.png');
			$this->assertSame($tempBrand . '/logo.png', $resolved);
		} finally {
			unlink($tempFile);
			rmdir($tempDir);
		}
	}

	public function testResolveImageNameFallsBackPerImageForPartialBrand(): void {
		// Create a brand that only has logo.png but not spacer.png
		$tempBrand = 'partial_img_brand_' . uniqid();
		$tempDir = $this->imgBasePath . '/' . $tempBrand;
		mkdir($tempDir, 0777, true);
		$tempFile = $tempDir . '/logo.png';
		file_put_contents($tempFile, 'fake-png-data');

		try {
			$resolver = $this->createResolver($tempBrand);

			// logo.png should come from the brand
			$this->assertSame($tempBrand . '/logo.png', $resolver->resolveImageName('logo.png'));

			// spacer.png should fall back to ionos
			$this->assertSame('ionos/spacer.png', $resolver->resolveImageName('spacer.png'));
		} finally {
			unlink($tempFile);
			rmdir($tempDir);
		}
	}

	// ── DEFAULT_BRAND constant ──────────────────────────────────────────

	public function testDefaultBrandConstantIsIonos(): void {
		$this->assertSame('ionos', BrandResolver::DEFAULT_BRAND);
	}
}
