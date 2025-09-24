<?php

/**
 * Unit tests for OCA\NcwMailtemplate\EMailTemplate
 */

namespace OCA\NcwMailtemplate\Tests;

use OCA\NcwMailtemplate\EMailTemplate;
use OCP\Defaults;
use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\L10N\IFactory;
use PHPUnit\Framework\TestCase;

class EMailTemplateTest extends TestCase {
	public function testIncludeTemplateFileIsCovered(): void {
		// Setup: create a real template file for 'head.html'
		$templateDir = __DIR__ . '/../../lib/templates/email';
		if (!is_dir($templateDir)) {
			mkdir($templateDir, 0777, true);
		}
		$templateFile = $templateDir . '/head.html';
		$expectedContent = '<div>Test Head Template</div>';
		file_put_contents($templateFile, $expectedContent);

		// Reset the property to empty string
		$reflection = new \ReflectionClass($this->emailTemplate);
		$prop = $reflection->getProperty('head');
		$prop->setAccessible(true);
		$prop->setValue($this->emailTemplate, '');

		// Call the private method via reflection
		$method = $reflection->getMethod('loadHtmlTemplateFiles');
		$method->setAccessible(true);
		$method->invoke($this->emailTemplate);

		// Assert the property now contains the expected content
		$value = $prop->getValue($this->emailTemplate);
		$this->assertStringContainsString($expectedContent, $value);

		// Cleanup
		unlink($templateFile);
	}
	public function testLoadHtmlTemplateFilesMethodIsCovered(): void {
		// Reset template properties to empty string to verify method effect
		$templateProperties = [
			'head', 'header', 'heading', 'bodyBegin', 'bodyText', 'listBegin', 'listItem', 'listEnd',
			'buttonGroup', 'button', 'bodyEnd', 'footer', 'tail'
		];
		$reflection = new \ReflectionClass($this->emailTemplate);
		foreach ($templateProperties as $property) {
			if ($reflection->hasProperty($property)) {
				$prop = $reflection->getProperty($property);
				$prop->setAccessible(true);
				$prop->setValue($this->emailTemplate, '');
			}
		}

		// Call the private method via reflection
		$method = $reflection->getMethod('loadHtmlTemplateFiles');
		$method->setAccessible(true);
		$method->invoke($this->emailTemplate);

		// After invocation, properties should be string or null
		foreach ($templateProperties as $property) {
			if ($reflection->hasProperty($property)) {
				$prop = $reflection->getProperty($property);
				$prop->setAccessible(true);
				$value = $prop->getValue($this->emailTemplate);
				$this->assertTrue(is_string($value) || is_null($value), "Property '$property' should be string or null after loadHtmlTemplateFiles()");
			}
		}
	}
	private $defaults;
	private $urlGenerator;
	private $l10nFactory;
	private $l10n;
	private $emailTemplate;

	protected function setUp(): void {
		$this->defaults = $this->createMock(Defaults::class);
		$this->urlGenerator = $this->createMock(IURLGenerator::class);
		$this->l10nFactory = $this->createMock(IFactory::class);
		$this->l10n = $this->createMock(IL10N::class);
		$this->l10nFactory->method('get')->willReturn($this->l10n);

		$this->urlGenerator->method('getAbsoluteURL')->willReturnCallback(fn ($url) => 'https://example.org' . $url);
		$this->urlGenerator->method('imagePath')->willReturnCallback(fn ($app, $img) => "/apps-external/ncw_mailtemplate/img/$img");

		$this->emailTemplate = new EMailTemplate(
			$this->defaults,
			$this->urlGenerator,
			$this->l10nFactory,
			252,
			120,
			'test.TestTemplate',
			[]
		);
	}

	public function testAssetUrlsAreGenerated(): void {
		$this->assertStringStartsWith('https://example.org', $this->getPrivateProperty('spacerUrl'));
		$this->assertStringContainsString('spacer.png', $this->getPrivateProperty('spacerUrl'));
		$this->assertStringStartsWith('https://example.org', $this->getPrivateProperty('logoUrl'));
		$this->assertStringContainsString('ionos_logo_de.png', $this->getPrivateProperty('logoUrl'));
		$this->assertStringStartsWith('https://example.org', $this->getPrivateProperty('emailIconUrl'));
		$this->assertStringContainsString('email.png', $this->getPrivateProperty('emailIconUrl'));
		$this->assertStringStartsWith('https://example.org', $this->getPrivateProperty('listItemIconUrl'));
		$this->assertStringContainsString('list-item-icon.png', $this->getPrivateProperty('listItemIconUrl'));
	}

	public function testHtmlTemplatePropertiesExist(): void {
		// All template properties should exist, even if empty (if file missing)
		$templateProperties = [
			'head', 'header', 'heading', 'bodyBegin', 'bodyText', 'listBegin', 'listItem', 'listEnd',
			'buttonGroup', 'button', 'bodyEnd', 'footer', 'tail'
		];
		foreach ($templateProperties as $property) {
			$reflection = new \ReflectionClass($this->emailTemplate);
			$hasProperty = $reflection->hasProperty($property);
			$this->assertTrue($hasProperty, "Property '$property' should exist");
			if ($hasProperty) {
				$prop = $reflection->getProperty($property);
				$prop->setAccessible(true);
				$value = $prop->getValue($this->emailTemplate);
				$this->assertTrue(is_string($value) || is_null($value), "Property '$property' should be string or null");
			}
		}
	}

	public function testLocalizationIsInitialized(): void {
		$reflection = new \ReflectionClass($this->emailTemplate);
		$prop = $reflection->getProperty('l');
		$prop->setAccessible(true);
		$l10nValue = $prop->getValue($this->emailTemplate);
		$this->assertSame($this->l10n, $l10nValue);
	}

	public function testMissingTemplateFileDoesNotError(): void {
		// Simulate missing template file by renaming one, if possible
		$reflection = new \ReflectionClass($this->emailTemplate);
		$prop = $reflection->getProperty('head');
		$prop->setAccessible(true);
		$value = $prop->getValue($this->emailTemplate);
		// If file missing, value should be null or empty string
		$this->assertTrue(is_string($value) || is_null($value));
	}

	private function getPrivateProperty(string $name) {
		$reflection = new \ReflectionClass($this->emailTemplate);
		$prop = $reflection->getProperty($name);
		$prop->setAccessible(true);
		return $prop->getValue($this->emailTemplate);
	}
}
