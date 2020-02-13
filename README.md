TO REMOVE

# PhpMetricsCollectorBundle

Integrates [PhpMetrics](http://www.phpmetrics.org) in Symfony2 debug toolbar.

![License](https://poser.pugx.org/halleck45/phpmetrics/license.svg)
[![Build Status](https://secure.travis-ci.org/Halleck45/PhpMetricsCollectorBundle.svg)](http://travis-ci.org/Halleck45/PhpMetricsCollectorBundle) 

## Overview 

![Overview of PhpMetricsCollectorBundle](doc/images/overview.png)

# Installation

Update your `composer.json` file:

    "halleck45/phpmetrics-collector-bundle": "*"
    
Then enable your bundle in `app/AppKernel.php`:

    if (in_array($this->getEnvironment(), array('dev', 'test'))) {
        (...)
        $bundles[] = new Hal\Bundle\PhpMetricsCollector\PhpMetricsCollectorBundle();
    }

# Contribute

Please run unit tests:

    phpunit -c phpunit.xml

# Author

+ Jean-François Lépine <[www.lepine.pro](http://www.lepine.pro)>

# License

See the LICENSE file.
