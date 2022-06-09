<?php

$config = \TYPO3\CodingStandards\CsFixerConfig::create();
$config->getFinder()
    ->name('*.php')
    ->in(__DIR__ . '/..')
    ->exclude(['vendor', 'typo3temp', 'var', '.build'])
    ->exclude('Configuration')
    ->notName('ext_localconf.php')
    ->notName('ext_tables.php')
    ->notName('ext_emconf.php')
    ->notName('php-cs-fixer.php')
    // CodeSnippets and Examples in Documentation do not need header comments
    ->exclude('Documentation');

$copyRightHeader = <<<EOF
This file is part of the "Impersonate" Extension for TYPO3 CMS.

(c) 2019 Christian Eßl <indy.essl@gmail.com>, https://christianessl.at
    2022 Axel Böswetter <boeswetter@portrino.de>, https://www.portrino.de
EOF;
$config->setHeader($copyRightHeader);
return $config;
