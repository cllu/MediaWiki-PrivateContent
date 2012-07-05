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
    global $whiteListPages;
    global $privateNamespaces;
    global $privateCategories;
    // check category.
    $pageNamespace = $title->getNamespace();
    $pageCategories = array_keys($title->getParentCategories());

    $isWhiteListPage = in_array($title, $whiteListPages);
    $isInPrivateNamespace = in_array($pageNamespace, $privateNamespaces);
    $hasPrivateCategory = false;
    foreach ($privateCategories as $privateCategory) {
        $privateCategory = 'Category:' + $privateCategory;
        if (in_array($privateCategory, $pageCategories)) {
            $hasPrivateCategory = true;
            break;
        }
    }
    #print_r('debug');
    #print_r($privateNamespaces);
    #print_r($pageNamesp);
    #print_r($isInPrivateNamespace);
    #exit;

    if ($isWhiteListPage) {
        // always allow white list pages.
        $result = true;
    } else if ($isInPrivateNamespace && !$user->isLoggedIn()) {
        // check category
        $result = false;
    } else if ($hasPrivateCategory && !$user->isLoggedIn()){
        // check namespaces.
        $result = false;
    } else {
        $result = true;
    }
    return $result;
}
