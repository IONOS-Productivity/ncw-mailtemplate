<?php

namespace OCA\NcwMailtemplate\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

class ApiController extends Controller {
	public function __construct($appName, IRequest $request) {
		parent::__construct($appName, $request);
	}

	public function index() {
		return new DataResponse(['message' => 'Hello world!']);
	}
}
