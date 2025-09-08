<?php

declare(strict_types=1);

use OCP\Util;

Util::addScript(OCA\Mailtemplate\AppInfo\Application::APP_ID, OCA\Mailtemplate\AppInfo\Application::APP_ID . '-main');
Util::addStyle(OCA\Mailtemplate\AppInfo\Application::APP_ID, OCA\Mailtemplate\AppInfo\Application::APP_ID . '-main');

?>

<div id="mailtemplate"></div>
