<?php
return [
    'BE' => [
        'explicitADmode' => 'explicitAllow',
    ],
    'DB' => [
        'Connections' => [
            'Default' => [
                'charset' => 'utf8',
                'driver' => 'mysqli',
            ],
        ],
    ],
    'EXTENSIONS' => [
        'backend' => [
            'backendFavicon' => '',
            'backendLogo' => '',
            'loginBackgroundImage' => '',
            'loginFootnote' => '',
            'loginHighlightColor' => '',
            'loginLogo' => '',
            'loginLogoAlt' => '',
        ],
        'extensionmanager' => [
            'automaticInstallation' => '1',
            'offlineMode' => '0',
        ],
    ],
    'FE' => [
        'disableNoCacheParameter' => true,
    ],
    'SYS' => [
        'encryptionKey' => 'c802958efa4f30619cf2235cdcef68caecafacbd30d5b7c3fc653884ed6d3a8ca916828c1f5d05fbd11296ca97cbc4d3',
        'features' => [
            'yamlImportsFollowDeclarationOrder' => true,
        ],
        'sitename' => 'New TYPO3 site',
        'trustedHostsPattern' => '.*',
    ],
];
