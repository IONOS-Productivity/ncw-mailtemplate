<?php

declare(strict_types=1);

namespace OCA\NcwMailtemplate;

use OC\Mail\EMailTemplate as ParentTemplate;
use OCA\NcwMailtemplate\AppInfo\Application;
use OCP\Defaults;
use OCP\IConfig;
use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\IUser;
use OCP\IUserManager;
use OCP\L10N\IFactory;

class EMailTemplate extends ParentTemplate {
	private IL10N $l;
	private ?IUser $user = null;

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

		// Try to get user from various sources (for recipient's language)
		$this->user = $this->determineUser($this->data);

		// Get language: user's preference, or system default
		if ($this->user) {
			$lang = $this->l10nFactory->getUserLanguage($this->user);
		} else {
			$config = \OC::$server->get(IConfig::class);
			$lang = $config->getSystemValue('default_language', 'en');
		}
		$this->l = $this->l10nFactory->get(Application::APP_ID, $lang);

		// Generate URLs for template assets
		$this->generateTemplateAssetUrls($urlGenerator);

		// Load all HTML template files
		$this->loadHtmlTemplateFiles();
	}

	/**
	 * Determine the user for this email template
	 * Tries multiple sources: data array keys, current logged-in user, etc.
	 *
	 * Supported data keys (from various email types):
	 * - userid: from settings.Welcome
	 * - emailAddress: from settings.PasswordChanged, settings.EmailChanged
	 * - newEMailAddress: from settings.EmailChanged
	 * - shareWith: from file sharing emails (can be email or user ID)
	 * - displayname: from various emails
	 * - attendee_name: from calendar invitation emails
	 *
	 * @param array $data
	 * @return IUser|null
	 */
	private function determineUser(array $data): ?IUser {
		$userManager = \OC::$server->get(IUserManager::class);

		// Priority 1: Try to get recipient by user ID (most direct)
		$userIdKeys = ['userid', 'userId', 'uid'];
		foreach ($userIdKeys as $key) {
			if (isset($data[$key]) && is_string($data[$key])) {
				$user = $userManager->get($data[$key]);
				if ($user instanceof IUser) {
					return $user;
				}
			}
		}

		// Priority 2: Try to get recipient by email address
		$emailKeys = ['emailAddress', 'newEMailAddress', 'shareWith'];
		foreach ($emailKeys as $key) {
			if (isset($data[$key]) && is_string($data[$key]) && str_contains($data[$key], '@')) {
				$value = $data[$key];

				// Try to get user by email address
				$users = $userManager->getByEmail($value);
				if (!empty($users)) {
					return reset($users);
				}
			}
		}

		// Priority 3: Try attendee_name or displayname as user ID or display name search
		$nameKeys = ['attendee_name', 'displayname'];
		foreach ($nameKeys as $key) {
			if (isset($data[$key]) && is_string($data[$key])) {
				$value = $data[$key];

				// First try as user ID
				$user = $userManager->get($value);
				if ($user instanceof IUser) {
					return $user;
				}

				// Then try as display name search
				$users = $userManager->searchDisplayName($value, 1);
				if (!empty($users)) {
					return reset($users);
				}
			}
		}

		return null;
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
