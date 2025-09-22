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
		$this->assertStringContainsString('https://example.org', $this->getPrivateProperty('spacerUrl'));
		$this->assertStringContainsString('spacer.png', $this->getPrivateProperty('spacerUrl'));
		$this->assertStringContainsString('ionos_logo_de.png', $this->getPrivateProperty('logoUrl'));
		$this->assertStringContainsString('email.png', $this->getPrivateProperty('emailIconUrl'));
		$this->assertStringContainsString('list-item-icon.png', $this->getPrivateProperty('listItemIconUrl'));
	}

	public function testHtmlTemplatesAreLoaded(): void {
		foreach ([
			'head', 'header', 'heading', 'bodyBegin', 'bodyText', 'listBegin', 'listItem', 'listEnd',
			'buttonGroup', 'button', 'bodyEnd', 'footer', 'tail'
		] as $property) {
			$value = $this->getPrivateProperty($property);
			$this->assertIsString($value);
		}
	}

	private function getPrivateProperty(string $name) {
		$reflection = new \ReflectionClass($this->emailTemplate);
		$prop = $reflection->getProperty($name);
		$prop->setAccessible(true);
		return $prop->getValue($this->emailTemplate);
	}
}
