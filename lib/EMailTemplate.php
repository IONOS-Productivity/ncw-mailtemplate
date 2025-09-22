<?php

declare(strict_types=1);

namespace OCA\Mailtemplate;

use OC\Mail\EMailTemplate as ParentTemplate;
use OCP\Defaults;
use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\L10N\IFactory;
use OCA\Mailtemplate\AppInfo\Application;

class EMailTemplate extends ParentTemplate {
	private IL10N $l;

	// Generated asset URLs (filled in constructor)
	private string $spacerUrl = '';
	private string $logoUrl = '';
	private string $emailIconUrl = '';
	private string $listItemIconUrl = '';

	// HTML template files for email components
	private const HTML_TEMPLATE_FILES = [
		'head' => 'head.html',
		'header' => 'header.html',
		'heading' => 'heading.html',
		'bodyBegin' => 'bodyBegin.html',
		'bodyText' => 'bodyText.html',
		'listBegin' => 'listBegin.html',
		'listItem' => 'listItem.html',
		'listEnd' => 'listEnd.html',
		'buttonGroup' => 'buttonGroup.html',
		'button' => 'button.html',
		'bodyEnd' => 'bodyEnd.html',
		'footer' => 'footer.html',
		'tail' => 'tail.html'
	];

	/**
	 * @param Defaults $defaults
	 * @param IURLGenerator $urlGenerator
	 * @param IFactory $l10nFactory
	 * @param int|null $logoWidth
	 * @param int|null $logoHeight
	 * @param string $emailId
	 * @param array $data
	 */
	public function __construct(
		Defaults $defaults,
		IURLGenerator $urlGenerator,
		IFactory $l10nFactory,
		?int $logoWidth,
		?int $logoHeight,
		string $emailId,
		array $data = [],
	) {
		parent::__construct($defaults, $urlGenerator, $l10nFactory, $logoWidth, $logoHeight, $emailId, $data);

		// Initialize localization object
		$this->l = $l10nFactory->get(Application::APP_ID);

		// Generate URLs for template assets
		$this->generateTemplateAssetUrls($urlGenerator);

		// Load all HTML template files
		$this->loadHtmlTemplateFiles();
	}

	/**
	 * Generate URLs for template assets (images, etc.)
	 *
	 * @param IURLGenerator $urlGenerator
	 */
	private function generateTemplateAssetUrls(IURLGenerator $urlGenerator): void {
		// store on the instance so we can inject into templates
		$this->spacerUrl = $urlGenerator->getAbsoluteURL($urlGenerator->imagePath(Application::APP_ID, 'spacer.png'));
		$this->logoUrl = $urlGenerator->getAbsoluteURL($urlGenerator->imagePath(Application::APP_ID, 'ionos_logo_de.png'));
		$this->emailIconUrl = $urlGenerator->getAbsoluteURL($urlGenerator->imagePath(Application::APP_ID, 'email.png'));
		$this->listItemIconUrl = $urlGenerator->getAbsoluteURL($urlGenerator->imagePath(Application::APP_ID, 'list-item-icon.png'));
	}

	/**
	 * Load HTML template files for email components
	 *
	 * @return void
	 */
	private function loadHtmlTemplateFiles(): void {
		foreach (self::HTML_TEMPLATE_FILES as $property => $file) {
			$templatePath = __DIR__ . '/templates/email/' . $file;
			if (!file_exists($templatePath)) {
				continue;
			}

			// Make variables available in template scope
			$spacerUrl = $this->spacerUrl;
			$logoUrl = $this->logoUrl;
			$emailIconUrl = $this->emailIconUrl;
			$listItemIconUrl = $this->listItemIconUrl;
			$l = $this->l;

			// Render PHP template to HTML
			ob_start();
			include $templatePath;
			$content = ob_get_clean();

			$this->$property = $content . PHP_EOL;
		}
	}
}
