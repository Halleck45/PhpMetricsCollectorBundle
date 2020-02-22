# PhpMetricsCollectorBundle

Integrates [PhpMetrics](http://www.phpmetrics.org) in Symfony 5 debug toolbar.

![License](https://poser.pugx.org/halleck45/phpmetrics/license.svg)
[![Build Status](https://secure.travis-ci.org/Halleck45/PhpMetricsCollectorBundle.svg)](http://travis-ci.org/Halleck45/PhpMetricsCollectorBundle) 

## Overview 

![Overview of PhpMetricsCollectorBundle](doc/images/overview.png)

# Installation

Install it via composer

    "composer require halleck45/phpmetrics-collector-bundle --dev"
    
For versions of Symfony 3.x, you need to enable your bundle in `app/AppKernel.php`:

    if (in_array($this->getEnvironment(), array('dev', 'test'))) {
        (...)
        $bundles[] = new Hal\Bundle\PhpMetricsCollector\PhpMetricsCollectorBundle();
    }
For more recent versions, this is not required.
# Contribute

Please run unit tests:

    phpunit -c phpunit.xml

# Authors

+ Jean-François LÉPINE <[www.lepine.pro](http://www.lepine.pro)>
+ Eric COURTIAL

# License

See the LICENSE file.