<?php

if(!ClassInfo::exists('MemberProfilePage') || !ClassInfo::exists('ChargifySubscriptionPage')) {
	$view = new DebugView();
	$clink = 'https://github.com/ajshort/silverstripe-chargify';
	$mlink = 'https://github.com/ajshort/silverstripe-memberprofiles';

	if(!headers_sent()) {
		header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error');
	}

	$view->writeHeader();
	$view->writeInfo('Dependency Error', 'The Chargify Select Preregister module requires both the Chargify and MemberProfiles modules.');
	if(!ClassInfo::exists('ChargifySubscriptionPage')){
		$view->writeParagraph("Please install the <a href=\"$clink\">Chargify</a> module.");
	}
	if(!ClassInfo::exists('MemberProfilePage')) {
		$view->writeParagraph("Please install the <a href=\"$mlink\">MemberProfiles</a> module.");
	}
	$view->writeFooter();

	exit;
}
