<?php

namespace Hal\Bundle\PhpMetricsCollector\Collector;

use Hal\Application\Analyze;
use Hal\Application\Config\Config;
use Hal\Component\Issue\Issuer;
use Hal\Component\Output\TestOutput;
use Hal\Metric\ClassMetric;
use Hal\Metric\Consolidated;
use Hal\Metric\InterfaceMetric;
use Hal\Metric\Metric;
use Hal\Violation\Violations;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpKernel\Kernel;

class PhpMetricsCollector extends DataCollector
{
    /** @var Kernel */
    private $kernel;

    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    public function reset()
    {
        $this->data = [
            'average' => [],
            'cclasses' => 0,
            'classes' => [],
        ];
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        // filter loaded files
        $files = get_included_files();
        $excludes = [
            'vendor',
            'pear',
            '\\.phar',
            'bootstrap\.php',
            'Test',
            '/app',
            'AppKernel.php',
            'autoload.php',
            'cache/',
            'app.php',
            'app_dev.php',
            'Form',
            'classes.php',
        ];
        $projectDir = method_exists($this->kernel, 'getProjectDir')
            ? $this->kernel->getProjectDir()
            : $this->kernel->getRootDir() . '/../';
        $projectDir = realpath($projectDir);
        $files = array_filter($files, function ($filePath) use ($excludes, $projectDir) {
            return ! preg_match(
                '!' . implode('|', $excludes) . '!',
                mb_substr($filePath, mb_strlen($projectDir))
            );
        });
        $config = new Config();
        $config->set('exclude', $excludes);
        $config->set('files', $files);
        $metrics = (new Analyze($config, new TestOutput(), new Issuer(new TestOutput())))->run($files);
        foreach ($metrics->all() as $each) {
            $each->set('violations', new Violations());
        }
        $classMetrics = array_filter($metrics->all(), function (Metric $metric) {
            return $metric instanceof ClassMetric
                || $metric instanceof InterfaceMetric;
        });
        $consolidated = new Consolidated($metrics);
        $scoreByClass = [];
        /** @var ClassMetric|InterfaceMetric $each */
        foreach ($classMetrics as $each) {
            $scoreByClass[$each->getName()] = [
                'maintainability' => $each->get('mi'),
                'commentWeight' => $each->get('commentWeight'),
                'complexity' => $each->get('ccn'),
                'loc' => $each->get('loc'),
                'lloc' => $each->get('lloc'),
                'cloc' => $each->get('cloc'),
                'bugs' => $each->get('bugs'),
                'difficulty' => $each->get('difficulty'),
                'intelligentContent' => $each->get('intelligentContent'),
                'vocabulary' => $each->get('vocabulary'),
            ];
        }

        // average
        $average = [];
        if (count($classMetrics) > 0) {
            $calculateAvg = function ($property) use ($classMetrics) {
                return round(array_sum(array_map(function (Metric $classMetric) use ($property) {
                    return $classMetric->get($property);
                }, $classMetrics)) / count($classMetrics), 2);
            };
            $average['maintainability'] = $consolidated->getAvg()->mi;
            $average['commentWeight'] = $consolidated->getAvg()->commentWeight;
            $average['complexity'] = $consolidated->getAvg()->ccn;
            $average['loc'] = $calculateAvg('loc');
            $average['lloc'] = $calculateAvg('lloc');
            $average['cloc'] = $calculateAvg('cloc');
            $average['bugs'] = $consolidated->getAvg()->bugs;
            $average['difficulty'] = $consolidated->getAvg()->difficulty;
            $average['intelligentContent'] = $consolidated->getAvg()->intelligentContent;
            $average['vocabulary'] = $calculateAvg('vocabulary');
        }
        $this->data = [
            'average' => $average,
            'cclasses' => count($classMetrics),
            'classes' => $scoreByClass,
        ];
    }

    public function getMaintainabilityIndex()
    {
        return $this->data['average']['maintainability'];
    }

    public function getComplexity()
    {
        return $this->data['average']['complexity'];
    }

    public function getCommentWeight()
    {
        return $this->data['average']['commentWeight'];
    }

    public function getLoc()
    {
        return $this->data['average']['loc'];
    }

    public function getLogicalLoc()
    {
        return $this->data['average']['lloc'];
    }

    public function getCommentLoc()
    {
        return $this->data['average']['cloc'];
    }

    public function getBugs()
    {
        return $this->data['average']['bugs'];
    }

    public function getDifficulty()
    {
        return $this->data['average']['difficulty'];
    }

    public function getIntelligentContent()
    {
        return $this->data['average']['intelligentContent'];
    }

    public function getVocabulary()
    {
        return $this->data['average']['vocabulary'];
    }

    public function getNumberOfClasses()
    {
        return $this->data['cclasses'];
    }

    public function getClasses()
    {
        return $this->data['classes'];
    }

    public function getName()
    {
        return 'phpmetrics_collector';
    }
}
