<?php

declare(strict_types=1);

namespace OCA\Mailtemplate;

use OC\Mail\EMailTemplate as ParentTemplate;
use OCP\Defaults;
use OCP\IURLGenerator;
use OCP\L10N\IFactory;
use OCP\IL10N;

class EMailTemplate extends ParentTemplate
{
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

	public function __construct(
		Defaults $defaults,
		IURLGenerator $urlGenerator,
		IFactory $l10nFactory,
		?int $logoWidth,
		?int $logoHeight,
		string $emailId,
		array $data = []
	) {
		parent::__construct($defaults, $urlGenerator, $l10nFactory, $logoWidth, $logoHeight, $emailId, $data);

		// Initialize localization object
		$this->l = $l10nFactory->get('mailtemplate');

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
		$this->spacerUrl = $urlGenerator->getAbsoluteURL($urlGenerator->imagePath('mailtemplate', 'spacer.png'));
		$this->logoUrl = $urlGenerator->getAbsoluteURL($urlGenerator->imagePath('mailtemplate', 'ionos_logo_de.png'));
		$this->emailIconUrl = $urlGenerator->getAbsoluteURL($urlGenerator->imagePath('mailtemplate', 'email.png'));
		$this->listItemIconUrl = $urlGenerator->getAbsoluteURL($urlGenerator->imagePath('mailtemplate', 'list-item-icon.png'));
	}

	/**
	 * Load HTML template files for email components
	 *
	 * @return void
	 */
	private function loadHtmlTemplateFiles(): void {
		foreach (self::HTML_TEMPLATE_FILES as $property => $file) {
			$content = file_get_contents(__DIR__ . '/' . $file);
			if ($content === false) {
				continue;
			}

			// Replace PHP-style concatenations for asset URLs that were previously in the HTML
			$search = [
				"' . \$spacerUrl . '",
				"' . \$logoUrl . '",
				"' . \$emailIconUrl . '",
				"' . \$listItemIconUrl . '",
			];
			$replace = [
				$this->spacerUrl,
				$this->logoUrl,
				$this->emailIconUrl,
				$this->listItemIconUrl,
			];
			$content = str_replace($search, $replace, $content);

			// Replace concatenated localization calls like ' . $this->l->t('...') . '
			$content = preg_replace_callback(
				"/\'\s*\.\s*\\\$this->l->t\('([^']+)'\)\s*\.\s*\'/",
				function(array $m) {
					// return translated string
					return $this->l->t($m[1]);
				},
				$content
			);

			$this->$property = $content . PHP_EOL;
		}
	}
}