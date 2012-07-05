<?php
$wgExtensionCredits['parserhook'][] = array(
    'path' => __FILE__,
    'name' => "Private Content",
    'description' => "Define some private namespace, categroy, and blocks for private viewing only",
    'version' => "0.1",
    'author' => "Chunliang Lu",
    'url' => "http://chunliang.name/",
);

$wgExtensionFunctions[] = 'wfPrivateContent';
$wgHooks['userCan'][] = 'privatePage';

function wfPrivateContent() {
    global $wgParser;
    global $wgHooks;
    $wgParser->setHook("private", "privateBlock");
}

function privateBlock($input, $args) {
    global $wgUser;
    if ($wgUser->isLoggedIn()) {
        return $input;
    } else {
        return "";
    }
}

function privatePage(&$title, &$user, $action, &$result) {

    global $publicPages;
    global $publicNamespaces;
    global $publicCategories;

    // check category.
    $pageNamespace = $title->getNamespace();
    $pageCategories = array_keys($title->getParentCategories());

    $isPublicPage = in_array($title, $publicPages);
    $isInPublicNamespace = in_array($pageNamespace, $publicNamespaces);
    $hasPublicCategory = false;
    foreach ($publicCategories as $publicCategory) {
        $publicCategory = 'Category:' + $publicCategory;
        if (in_array($publicCategory, $pageCategories)) {
            $hasPublicCategory = true;
            break;
        }
    }
   
   if ($user->isLoggedIn() || $isPublicPage || $isInPublicNamespace || $hasPublicCategory) {
        $result = true;
    } else {
        $result = false;
    }
    return $result;
}
