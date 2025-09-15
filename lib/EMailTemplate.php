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

		$spacerUrl = $this->urlGenerator->getAbsoluteURL($this->urlGenerator->imagePath('mailtemplate', 'spacer.png'));
		$logoUrl = $this->urlGenerator->getAbsoluteURL($this->urlGenerator->imagePath('mailtemplate', 'ionos_logo_de.png'));
		$emailIconUrl = $this->urlGenerator->getAbsoluteURL($this->urlGenerator->imagePath('mailtemplate', 'email.png'));
		$listItemIconUrl = $this->urlGenerator->getAbsoluteURL($this->urlGenerator->imagePath('mailtemplate', 'list-item-icon.png'));

		$this->head = '
			<!-- start head -->
			<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
				<head>
					<meta name="x-apple-disable-message-reformatting" />
					<meta name="viewport" content="width=device-width, initial-scale=1.0" />
					<meta http-equiv="X-UA-Compatible" content="IE=edge" />
					<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
					<meta name="format-detection" content="telephone=no" />
					<meta name="format-detection" content="date=no" />
					<meta name="format-detection" content="address=no" />
					<meta name="format-detection" content="email=no" />
					<title></title>
					<style type="text/css" >
						table,td {
							mso-table-lspace: 0pt !important;
							mso-table-rspace: 0pt !important;
						}
						img {
							-ms-interpolation-mode:bicubic;
						}
						@media only screen and (max-width: 580px) {
							.h-15 {height:15px !important;}
							.w-20 {width:20px !important;}
							.minw-20 {min-width:20px !important;}
							.h-25 {height:25px !important;}
							.h-12 {height:12px !important;}
							.h-10 {height:10px !important;}
							.inline-block {display:inline-block !important;}
							.w-100 {width:100%% !important;}
							.minw-100 {min-width:100%% !important;}
							.v-top {vertical-align:top !important;}
							.w-80 {width:80px !important;}
							.minw-80 {min-width:80px !important;}
							.h-80 {height:80px !important;}
							.minh-80 {min-height:80px !important;}
							.w-280 {width:280px !important;}
							.w-580 {width:580px !important;}
							.minw-580 {min-width:580px !important;}
							.w-640 {width:640px !important;}
							.minw-640 {min-width:640px !important;}
						
						
							@media only screen and (min-width: 1900px) {
								.h-15 {height:15px !important;}
								.w-340 {width:340px !important;}
								.minw-340 {min-width:340px !important;}
								.w-20 {width:20px !important;}
								.minw-20 {min-width:20px !important;}
								.h-25 {height:25px !important;}
								.h-12 {height:12px !important;}
								.h-20 {height:20px !important;}
								.h-10 {height:10px !important;}
								.inline-block {display:inline-block !important;}
								.w-33 {width:33.3%% !important;}
								.minw-33 {min-width:33.3%% !important;}
								.v-top {vertical-align:top !important;}
								.text-left {text-align:left !important;}
								.text-right {text-align:right !important;}
								.w-100 {width:100%% !important;}
								.w-25 {width:25px !important;}
								.minw-25 {min-width:25px !important;}
								.w-100px {width:100px !important;}
								.minw-100px {min-width:100px !important;}
								.h-100 {height:100px !important;}
								.minh-100 {min-height:100px !important;}
								.w-353 {width:353px !important;}
								.h-32 {height:32px !important;}
								.w-175 {width:175px !important;}
								.w-680 {width:680px !important;}
								.minw-680 {min-width:680px !important;}
								.w-740 {width:740px !important;}
								.minw-740 {min-width:740px !important;}
							}
						}
						.button {
							background-color: rgb(17, 199, 230);
							border: 2px solid rgb(17, 199, 230);
							border-radius: 40px;
							border-image-outset: 0;
							border-image-repeat: stretch;
							border-image-slice: 100%;
							border-image-source: none;
							border-image-width: 1;
							box-shadow: rgba(0, 0, 0, 0) 0px 0px 0px 0px, rgba(0, 0, 0, 0) 0px 0px 0px 0px, rgba(0, 0, 0, 0) 0px 2px 12px 0px;
							box-sizing: border-box;
							color: rgb(11, 42, 99);
							cursor: pointer;
							display: inline-grid;
							font-feature-settings: normal;
							font-size: 12px;
							font-variation-settings: normal;
							font-weight: 600;
							height: 36px;
							line-height: 20px;
							outline-color: rgba(0, 0, 0, 0);
							outline-offset: 2px;
							outline-style: solid;
							outline-width: 2px;
							padding: 6px 12px;
							tab-size: 4;
							text-align: center;
							text-decoration-color: rgb(11, 42, 99);
							text-decoration-line: none;
							text-decoration-style: solid;
							text-decoration-thickness: auto;
							text-size-adjust: 100%;
							-webkit-tap-highlight-color: rgba(0, 0, 0, 0);
						}
						@media all {
							.inline-block {display:inline-block !important;}
						}
					</style>
					<!--[if mso]><style>* {font-family: Arial,sans-serif !important;}</style><![endif]-->
					<!--[if !mso]><!-->
					<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet" type="text/css">
					<link href="https://fonts.googleapis.com/css?family=Overpass:400,600,700" rel="stylesheet" type="text/css">
					<!--<![endif]-->
					<!--[if gte mso 9]>
						<xml><o:OfficeDocumentSettings><o:AllowPNG/><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml>
					<![endif]-->
				</head>
				<body topmargin="0" rightmargin="0" leftmargin="0" style="background-color:#FFFFFF;-webkit-text-size-adjust:none;font-family:\'Open Sans\', \'Google Sans\', Arial, sans-serif;font-stretch:normal;font-style:normal;font-weight:normal;font-variant:normal;font-size:1.2rem" link="#3B9CDA" text="#465A75" vlink="#3B9CDA" alink="#3B9CDA" bgcolor="#FFFFFF">
					<div class="body-bg" style="background-color:#FFFFFF;width:100%%" bgcolor="#FFFFFF">
						<div class="pad" style="padding-top:0px;padding-right:0px;padding-bottom:30px;padding-left:0px">
							<!--[if mso]>
								<style>.nomsoffice{mso-hide:all;visibility:hidden;}</style>
							<![endif]-->
							<a name="nltop"></a>
							<table class="layout-wrapper w-740 minw-740" cellpadding="0" cellspacing="0" border="0" width="740" align="center" style="width:740px;border-spacing:0" >
								<tr valign="top" >
									<td class="w-740 minw-740 bg-white" width="740" align="left" bgcolor="#FFFFFF" style="background-color:#FFFFFF;min-width:740px;width:740px" >
			<!-- End head -->
		';
		$this->header = '
			<!-- Start header -->
				<table cellpadding="0" cellspacing="0" border="0" width="100%%" >
					<tr>
						<td class="w-100 h-10" style="width:100%%;height:10px;line-height:10px" >
							<img src="' . $spacerUrl . '" width="1" height="10" class="w-1 h-10" style="display:block;width:1px;height:10px;border:0" alt=" " border="0" />
						</td>
					</tr>
				</table>
				<table cellpadding="0" cellspacing="0" border="0" width="100%%" bgcolor="#003D8F" style="width:100%%;border-radius:8px 8px 8px 8px;border-spacing:0;background-color:#003D8F" >
					<tr valign="top" >
						<td align="left" >
							<table cellpadding="0" cellspacing="0" border="0" width="100%%" >
								<tr>
									<td width="1" height="18" style="width:100%%;height:18px;line-height:18px" >
										<img src="' . $spacerUrl . '" width="1" height="18" style="display:block;width:1px;height:18px;border:0" alt=" " border="0" />
									</td>
								</tr>
							</table>
							<table cellpadding="0" cellspacing="0" border="0" width="100%%" style="width:100%%;border-spacing:0" >
								<tr valign="top" >
									<td class="w-20 minw-20" style="min-width:20px;width:20px;line-height:1px;font-size:0px" >
										<img src="' . $spacerUrl . '" width="20" height="1" class="w-20 h-1" style="display:block;width:20px;height:1px;border:0" alt=" " border="0" />
									</td>
									<td class="w-112 minw-112 h-32 minh-32" align="left" valign="middle" height="32" style="min-width:112px;width:112px;min-height:32px;height:32px" >
										<img src="' . $logoUrl . '" border="0" title="IONOS" alt="IONOS Logo" class="w-112 h-32" width="112" height="32" style="color:#465A75;margin:0;border:0;font-size:15px;font-family:\'Open Sans\', \'Google Sans\', Arial, sans-serif;line-height:0;display:block;width:112px;height:32px;padding:0" />
									</td>
									<td class="w-10 minw-10" style="min-width:10px;width:10px;line-height:1px;font-size:0px" >
										<img src="' . $spacerUrl . '" width="10" height="1" class="w-10 h-1" style="display:block;width:10px;height:1px;border:0" alt=" " border="0" />
									</td>
									<td class="header-bar-right text-right" align="right" valign="middle" style="font-size:14px;line-height:18px" >
									</td>
									<td class="w-20 minw-20" style="min-width:20px;width:20px;line-height:1px;font-size:0px" >
										<img src="' . $spacerUrl . '" width="20" height="1" class="w-20 h-1" style="display:block;width:20px;height:1px;border:0" alt=" " border="0" />
									</td>
								</tr>
							</table>
							<table cellpadding="0" cellspacing="0" border="0" width="100%%" >
								<tr>
									<td width="1" height="18" style="width:100%%;height:18px;line-height:18px" >
										<img src="' . $spacerUrl . '" width="1" height="18" style="display:block;width:1px;height:18px;border:0" alt=" " border="0" />
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<table cellpadding="0" cellspacing="0" border="0" width="100%%" >
					<tr>
						<td width="1" height="10" style="width:100%%;height:10px;line-height:10px" >
							<img src="' . $spacerUrl . '" width="1" height="10" style="display:block;width:1px;height:10px;border:0" alt=" " border="0" />
						</td>
					</tr>
				</table>
			<!-- End header -->
		';
		$this->heading = '
			<!-- Start heading -->
				<table cellpadding="0" cellspacing="0" border="0" width="100%%">
					<tr>
						<td class="w-100 h-10" style="width:100%%;height:10px;line-height:10px" >
							<img src="' . $spacerUrl . '" width="1" height="10" class="w-1 h-10" style="display:block;width:1px;height:10px;border:0" alt=" " border="0" />
						</td>
					</tr>
				</table>
				<table cellpadding="0" cellspacing="0" border="0" width="100%%" style="width:100%%;border-spacing:0" >
					<tr valign="top" >
						<td class="w-20 minw-20" style="min-width:20px;width:20px;line-height:1px;font-size:0px" >
						<img src="' . $spacerUrl . '" width="20" height="1" class="w-20 h-1" style="display:block;width:20px;height:1px;border:0" alt=" " border="0" />
						</td>
						<td class="heading-content" align="left" >
							<table cellpadding="0" cellspacing="0" border="0" width="100%%" style="width:100%%;border-spacing:0" >
								<tr valign="top" >
									<td class="heading-left" align="left" valign="middle" >
										<table cellpadding="0" cellspacing="0" border="0" style="border-spacing:0" >
											<tr valign="top" >
												<td class="heading-title" align="left" >
													<p class="heading" style="padding:0 0 0 0;margin:0;text-align:left;line-height:33px" >
														<font class="heading-font" style="font-family:\'Overpass Regular\', \'Roboto\', Arial, sans-serif;font-size:26px;font-weight:600;color:#0B2A63;line-height:33px" >
															%s
														</font>
													</p>
												</td>
											</tr>
										</table>
									</td>
									<td class="w-20 minw-20" style="min-width:20px;width:20px;line-height:1px;font-size:0px" >
										<img src="' . $spacerUrl . '" width="20" height="1" class="w-20 h-1" style="display:block;width:20px;height:1px;border:0" alt=" " border="0" />
									</td>
									<td class="w-100 minw-100 h-100 minh-100" align="right" height="100" style="min-width:100px;width:100px;min-height:100px;height:100px" >
										<img src="' . $emailIconUrl . '" border="0" alt=" " class="w-100 h-100" width="100" height="100" style="color:#465A75;margin:0;border:0;font-size:15px;font-family:\'Open Sans\', \'Google Sans\', Arial, sans-serif;line-height:0;display:block;width:100px;height:100px;padding:0" />
									</td>
								</tr>
							</table>
						</td>
						<td class="w-20 minw-20" style="min-width:20px;width:20px;line-height:1px;font-size:0px" >
							<img src="' . $spacerUrl . '" width="20" height="1" class="w-20 h-1" style="display:block;width:20px;height:1px;border:0" alt=" " border="0" />
						</td>
					</tr>
				</table>
				<table cellpadding="0" cellspacing="0" border="0" width="100%%" >
					<tr>
						<td class="w-100 h-10" style="width:100%%;height:10px;line-height:10px" >
							<img src="' . $spacerUrl . '" width="1" height="10" class="w-1 h-10" style="display:block;width:1px;height:10px;border:0" alt=" " border="0" />
						</td>
					</tr>
				</table>
				<table cellpadding="0" cellspacing="0" border="0" width="100%%" >
					<tr>
						<td class="w-100 h-12" style="width:100%%;height:12px;line-height:12px" >
							<img src="' . $spacerUrl . '" width="1" height="12" class="w-1 h-12" style="display:block;width:1px;height:12px;border:0" alt=" " border="0" />
						</td>
					</tr>
				</table>
			<!-- End heading -->
			';
		$this->bodyBegin = '
				<!-- Start bodyBegin -->
					<table cellpadding="0" cellspacing="0" border="0" width="100%%" bgcolor="#F2F5F8" style="width:100%%;border-radius:8px 8px 8px 8px;border-spacing:0;background-color:#F2F5F8" >
						<tr valign="top" >
							<td align="left" >
								<table cellpadding="0" cellspacing="0" border="0" width="100%%" >
									<tr>
										<td class="w-100 h-25" style="width:100%%;height:25px;line-height:25px" >
											<img src="' . $spacerUrl . '" width="1" height="25" class="w-1 h-25" style="display:block;width:1px;height:25px;border:0" alt=" " border="0" />
										</td>
									</tr>
								</table>
								<table cellpadding="0" cellspacing="0" border="0" width="100%%" style="width:100%%;border-spacing:0" >
									<tr valign="top" >
										<td class="w-20 minw-20" style="min-width:20px;width:20px;line-height:1px;font-size:0px" >
											<img src="' . $spacerUrl . '" width="20" height="1" class="w-20 h-1" style="display:block;width:20px;height:1px;border:0" alt=" " border="0" />
										</td>
										<td align="left" >
				<!-- End bodyBegin -->
			';
		$this->bodyText = '
				<!-- Start bodyText-->
					<p class="body-text" style="padding:0 0 0 0;margin:0;text-align:left;line-height:20px" >
						<font class="body-font" style="font-family:\'Open Sans\', \'Google Sans\', Arial, sans-serif;font-size:15px;font-weight:normal;color:#465A75;line-height:20px" >
							%s
						</font>
					</p>
					<table cellpadding="0" cellspacing="0" border="0" width="100%%" >
						<tr>
							<td class="w-100 h-15" style="width:100%%;height:15px;line-height:15px" >
								<img src="' . $spacerUrl . '" width="1" height="15" class="w-1 h-15" style="display:block;width:1px;height:15px;border:0" alt=" " border="0" />
							</td>
						</tr>
					</table>
				<!-- End bodyText -->
			';
		$this->listBegin = '
				<!-- Start listBegin-->
					<div style="border-radius:4px;overflow:hidden;">
				<!-- End listBegin-->
			';
		$this->listItem = '
				<!-- Start listItem-->
					<table cellpadding="0" cellspacing="0" border="0" width="100%%" style="width:100%%;border-spacing:0">
						<tbody>
							<tr valign="top">
								<td class="w-5 minw-5" style="min-width:5px;width:5px;line-height:1px;background-color:#3B9CDA;font-size:0px">
									<img src="' . $spacerUrl . '" width="5" height="1" class="w-5 h-1" style="display:block;width:5px;height:1px;border:0" alt=" " border="0" />
								</td>
								<td align="left" bgcolor="#DBEDF8" style="background-color:#DBEDF8">
									<table cellpadding="0" cellspacing="0" border="0" width="100%%">
										<tbody>
											<tr>
												<td width="1" height="15" style="width:100%%;height:15px;line-height:15px">
													<img src="' . $spacerUrl . '" width="1" height="15" style="display:block;width:1px;height:15px;border:0" alt=" " border="0">
												</td>
											</tr>
										</tbody>
									</table>
									<table cellpadding="0" cellspacing="0" border="0" width="100%%" style="width:100%%;border-spacing:0">
										<tbody>
											<tr valign="top">
												<td class="w-20 minw-20" style="min-width:20px;width:20px;line-height:1px;font-size:0px">
													<img src="' . $spacerUrl . '" width="20" height="1" class="w-20 h-1" style="display:block;width:20px;height:1px;border:0" alt=" " border="0" />
												</td>
												<td width="15">
													<p style="padding:2px 0 0 0;margin:0;text-align:left;line-height:20px">
														<img src="' . $listItemIconUrl . '" alt="' . $this->l->t('List item icon') . '">
													</p>
												</td>
												<td class="w-10 minw-10" style="min-width:10px;width:10px;line-height:1px;font-size:0px">
													<img src="' . $spacerUrl . '" width="10" height="1" class="w-10 h-1" style="display:block;width:10px;height:1px;border:0" alt=" " border="0" />
												</td>
												<td align="left">
													<p style="padding:0 0 0 0;margin:0;text-align:left;line-height:20px">
														<font style="font-family:\'Open Sans\', \'Google Sans\', Arial, sans-serif;font-size:15px;font-weight:normal;color:#465A75;line-height:20px" >
															%s
														</font>
													</p>
												</td>
												<td class="w-20 minw-20" style="min-width:20px;width:20px;line-height:1px;font-size:0px">
													<img src="' . $spacerUrl . '" width="20" height="1" class="w-20 h-1" style="display:block;width:20px;height:1px;border:0" alt=" " border="0" />
												</td>
											</tr>
										</tbody>
									</table>
									<table cellpadding="0" cellspacing="0" border="0" width="100%%">
										<tbody>
											<tr>
												<td width="1" height="15" style="width:100%%;height:15px;line-height:15px">
													<img src="' . $spacerUrl . '" width="1" height="15" style="display:block;width:1px;height:15px;border:0" alt=" " border="0">
												</td>
											</tr>
										</tbody>
									</table>
								</td>
									<td width="5" style="min-width:5px;width:5px;line-height:1px;background-color:#DBEDF8;font-size:0px">
										<img src="' . $spacerUrl . '" width="5" height="1" style="display:block;width:5px;height:1px;border:0" alt=" " border="0">
									</td>
								</tr>
						</tbody>
					</table>
				<!-- End listItem -->
			';
		$this->listEnd = '
				<!-- Start listEnd-->
					<table cellpadding="0" cellspacing="0" border="0" width="100%%" >
							<tr>
								<td width="1" height="15" style="width:100%%;height:15px;line-height:15px" >
									<img src="' . $spacerUrl . '" width="1" height="15" style="display:block;width:1px;height:15px;border:0" alt=" " border="0" />
								</td>
							</tr>
						</table>
					</div>
				<!-- End listEnd -->
			';
		$this->buttonGroup = '
				<!-- Start buttonGroup -->
					<table cellpadding="0" cellspacing="0" border="0" name="halign" width="100%%" style="width:100%%;border-spacing:0" >
						<tr valign="top" >
							<td align="center" >
								<a href="%3$s" class="button" target="_blank" style="background-color: rgb(17, 199, 230);border: 2px solid rgb(17, 199, 230);border-radius: 40px;box-sizing: border-box;color: rgb(11, 42, 99);cursor: pointer;display: inline-grid;font-size: 12px;font-weight: 600;height: 36px;line-height: 20px;padding: 6px 12px;text-align: center;text-decoration: none;">
									%7$s
								</a>
							</td>
							<td align="center" >
								<a href="%8$s" class="button" target="_blank" style="background-color: rgb(17, 199, 230);border: 2px solid rgb(17, 199, 230);border-radius: 40px;box-sizing: border-box;color: rgb(11, 42, 99);cursor: pointer;display: inline-grid;font-size: 12px;font-weight: 600;height: 36px;line-height: 20px;padding: 6px 12px;text-align: center;text-decoration: none;">
									%9$s
								</a>
							</td>
						</tr>
					</table>
					<table cellpadding="0" cellspacing="0" border="0" name="vspace-bottom-button" width="100%%" >
						<tr>
							<td width="1" height="15" style="width:100%%;height:15px;line-height:15px" >
								<img src="' . $spacerUrl . '" width="1" height="15" style="display:block;width:1px;height:15px;border:0" alt=" " border="0" />
							</td>
						</tr>
					</table>
					<table cellpadding="0" cellspacing="0" border="0" name="vspace-bottom-button" width="100%%" >
						<tr>
							<td width="1" height="15" style="width:100%%;height:15px;line-height:15px" >
								<img src="' . $spacerUrl . '" width="1" height="15" style="display:block;width:1px;height:15px;border:0" alt=" " border="0" />
							</td>
						</tr>
					</table>
				<!-- End buttonGroup -->
			';
		$this->button = '
				<!-- Start button -->
					<table cellpadding="0" cellspacing="0" border="0" name="halign" width="100%%" style="width:100%%;border-spacing:0" >
						<tr valign="top" >
							<td align="center" >
								<a href="%3$s" class="button" target="_blank" style="background-color: rgb(17, 199, 230);border: 2px solid rgb(17, 199, 230);border-radius: 40px;box-sizing: border-box;color: rgb(11, 42, 99);cursor: pointer;display: inline-grid;font-size: 12px;font-weight: 600;height: 36px;line-height: 20px;padding: 6px 12px;text-align: center;text-decoration: none;">
									%7$s
								</a>
							</td>
						</tr>
					</table>
					<table cellpadding="0" cellspacing="0" border="0" name="vspace-bottom-button" width="100%%" >
						<tr>
							<td class="w-100 h-15" style="width:100%%;height:15px;line-height:15px" >
								<img src="' . $spacerUrl . '" width="1" height="15" class="w-1 h-15" style="display:block;width:1px;height:15px;border:0" alt=" " border="0" />
							</td>
						</tr>
					</table>
					<table cellpadding="0" cellspacing="0" border="0" name="vspace-bottom-button" width="100%%" >
						<tr>
							<td class="w-100 h-15" style="width:100%%;height:15px;line-height:15px" >
								<img src="' . $spacerUrl . '" width="1" height="15" class="w-1 h-15" style="display:block;width:1px;height:15px;border:0" alt=" " border="0" />
							</td>
						</tr>
					</table>
				<!-- End button -->
			';
		$this->bodyEnd = '
				<!-- Start bodyEnd -->
											<p class="footer-greeting" style="padding:0 0 0 0;margin:0;text-align:left;line-height:20px" >
												<font class="footer-font" style="font-family:\'Open Sans\', \'Google Sans\', Arial, sans-serif;font-size:15px;font-weight:normal;color:#465A75;line-height:20px" >' . $this->l->t('Best regards') . '<br/>IONOS SE<br/></font>
											</p>
										</td>
										<td width="20" style="min-width:20px;width:20px;line-height:1px;font-size:0px" >
											<img src="' . $spacerUrl . '" width="20" height="1" style="display:block;width:20px;height:1px;border:0" alt=" " border="0" />
										</td>
									</tr>
								</table>
								<table cellpadding="0" cellspacing="0" border="0" width="100%%" >
									<tr>
										<td class="w-100 h-25" style="width:100%%;height:25px;line-height:25px" >
											<img src="' . $spacerUrl . '" width="1" height="25" class="w-1 h-25" style="display:block;width:1px;height:25px;border:0" alt=" " border="0" />
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
					<table cellpadding="0" cellspacing="0" border="0" name="vspace-bottom-section" width="100%%" >
						<tr>
							<td class="w-100 h-12" style="width:100%%;height:12px;line-height:12px" >
								<img src="' . $spacerUrl . '" width="1" height="12" class="w-1 h-12" style="display:block;width:1px;height:12px;border:0" alt=" " border="0" />
							</td>
						</tr>
					</table>
				<!-- End bodyEnd -->
			';
		$this->footer = '
				<!-- Start footer -->
				<table cellpadding="0" cellspacing="0" border="0" width="100%%" bgcolor="#F2F5F8" style="width:100%%;border-radius:8px 8px 8px 8px;border-spacing:0;background-color:#F2F5F8" >
						<tr valign="top" >
							<td align="left" >
								<table cellpadding="0" cellspacing="0" border="0" style="border-spacing:0" >
									<tr valign="top" >
										<td width="580" style="min-width:580px;width:580px;line-height:1px;font-size:0px" >
											<img src="' . $spacerUrl . '" width="580" height="1" style="display:block;width:580px;height:1px;border:0" alt=" " border="0"/>
										</td>
									</tr>
								</table>
								<table cellpadding="0" cellspacing="0" border="0" width="100%%" >
									<tr>
										<td width="1" height="25" style="width:100%%;height:25px;line-height:25px" >
											<img src="' . $spacerUrl . '" width="1" height="25" style="display:block;width:1px;height:25px;border:0" alt=" " border="0" />
										</td>
									</tr>
								</table>
								<table cellpadding="0" cellspacing="0" border="0" width="100%%" style="width:100%%;border-spacing:0" >
									<tr valign="top" >
										<td width="20" style="min-width:20px;width:20px;line-height:1px;font-size:0px" >
											<img src="' . $spacerUrl . '" width="20" height="1" style="display:block;width:20px;height:1px;border:0" alt=" " border="0" />
										</td>
										<td align="left" >
											<p class="footer-address" style="padding:0 0 0 0;margin:0;text-align:left;line-height:18px" >
												<font class="footer-address-font" style="font-family:\'Open Sans\', \'Google Sans\', Arial, sans-serif;font-size:14px;font-weight:normal;color:#465A75;line-height:18px" >IONOS SE
													<br/>Elgendorfer Straße 57
													<br/>56410 Montabaur
													<br/>' . $this->l->t('Germany') . '
												</font>
											</p>
											<p>
												<a href="https://ionos.eu" class="footer-link" style="font-family:\'Open Sans\', \'Google Sans\', Arial, sans-serif;font-size:14px;font-weight:normal;color:#465A75;line-height:18px" target="_blank">' . $this->l->t('Further information') . '</a>
											</p>
											<table cellpadding="0" cellspacing="0" border="0" width="100%%" >
												<tr>
													<td width="1" height="15" style="width:100%%;height:15px;line-height:15px" >
														<img src="' . $spacerUrl . '" width="1" height="15" style="display:block;width:1px;height:15px;border:0" alt=" " border="0" />
													</td>
												</tr>
											</table>
										</td>
										<td width="20" style="min-width:20px;width:20px;line-height:1px;font-size:0px" >
											<img src="' . $spacerUrl . '" width="20" height="1" style="display:block;width:20px;height:1px;border:0" alt=" " border="0" />
										</td>
									</tr>
								</table>
								<table cellpadding="0" cellspacing="0" border="0" width="100%%" >
									<tr>
										<td width="1" height="25" style="width:100%%;height:25px;line-height:25px" >
											<img src="' . $spacerUrl . '" width="1" height="25" style="display:block;width:1px;height:25px;border:0" alt=" " border="0" />
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				<!-- End footer -->
			';
		$this->tail = '
				<!-- Start tail-->
						</td>
					</tr>
				</table>
			</div>
		</body>
	</html>
	<!-- End tail -->    
			';
	}
}