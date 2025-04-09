<?php

$config = \TYPO3\CodingStandards\CsFixerConfig::create();
$config->getFinder()->in(['Classes', 'Configuration', 'Tests']);

//$copyRightHeader = <<<EOF
//This file is part of the "Impersonate" Extension for TYPO3 CMS.
//
//(c) 2019 Christian Eßl <indy.essl@gmail.com>, https://christianessl.at
//    2022 Axel Böswetter <boeswetter@portrino.de>, https://www.portrino.de
//EOF;
//$config->setHeader($copyRightHeader);

return $config;
