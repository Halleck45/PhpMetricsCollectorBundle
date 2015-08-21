<?php
namespace Hal\Bundle\PhpMetricsCollector\Collector;

use Hal\Component\Token\Tokenizer;
use Hal\Component\Token\TokenType;
use Hal\Metrics\Complexity\Component\McCabe\McCabe;
use Hal\Metrics\Complexity\Text\Halstead\Halstead;
use Hal\Metrics\Complexity\Text\Length\Loc;
use Hal\Metrics\Design\Component\MaintainabilityIndex\MaintainabilityIndex;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class PhpMetricsCollector extends DataCollector
{
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {

        // filter loaded files
        $files = get_included_files();
        $files = array_filter($files, function ($v) {
            $excludes = array('vendor', 'pear', '\\.phar', 'bootstrap\.php', 'Test', '/app', 'AppKernel.php', 'autoload.php', 'cache/', 'app.php', 'app_dev.php', 'Form', 'PhpMetrics', 'classes.php');
            return !preg_match('!' . implode('|', $excludes) . '!', $v);
        });

        // Prepare datas
        $all = $average = array(
            'cfiles' => 0,
            'maintainability' => array(),
            'commentWeight' => array(),
            'complexity' => array(),
            'loc' => array(),
            'lloc' => array(),
            'cloc' => array(),
            'bugs' => array(),
            'difficulty' => array(),
            'intelligentContent' => array(),
            'vocabulary' => array(),
        );
        $scoreByFile = array();

        // group files into tmp folder
        $folder = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid();
        mkdir($folder);
            
        // run PhpMetrics
        $tokenizer = new Tokenizer();
        $tokenType = new TokenType();

        // halstead
        $halstead = new Halstead($tokenizer, $tokenType);
        $loc = new Loc($tokenizer);
        $complexity = new McCabe($tokenizer);
        $maintainability = new MaintainabilityIndex();
            
        foreach ($files as $file) {
            $rHalstead = $halstead->calculate($file);
            // loc
            $rLoc = $loc->calculate($file);
            // complexity
            $rComplexity = $complexity->calculate($file);
            // maintainability
            $rMaintenability = $maintainability->calculate($rHalstead, $rLoc, $rComplexity);

            // store result
            $files[$file] = array();
            $all['cfiles']++;
            $all['maintainability'][] = $scoreByFile[$file]['maintainability'] = $rMaintenability->getMaintainabilityIndex();
            $all['commentWeight'][] = $scoreByFile[$file]['commentWeight'] = $rMaintenability->getCommentWeight();
            $all['complexity'][] = $scoreByFile[$file]['complexity'] = $rComplexity->getCyclomaticComplexityNumber();
            $all['loc'][] = $scoreByFile[$file]['loc'] = $rLoc->getLoc();
            $all['lloc'][] = $scoreByFile[$file]['lloc'] = $rLoc->getLogicalLoc();
            $all['cloc'][] = $scoreByFile[$file]['cloc'] = $rLoc->getCommentLoc();
            $all['bugs'][] = $scoreByFile[$file]['bugs'] = $rHalstead->getBugs();
            $all['difficulty'][] = $scoreByFile[$file]['difficulty'] = $rHalstead->getDifficulty();
            $all['intelligentContent'][] = $scoreByFile[$file]['intelligentContent'] = $rHalstead->getIntelligentContent();
            $all['vocabulary'][] = $scoreByFile[$file]['vocabulary'] = $rHalstead->getVocabulary();
        }

        // average
        if ($all['cfiles'] > 0) {
            $average['maintainability'] = array_sum($all['maintainability']) / sizeof($all['maintainability']);
            $average['commentWeight'] = array_sum($all['commentWeight']) / sizeof($all['commentWeight']);
            $average['complexity'] = array_sum($all['complexity']) / sizeof($all['complexity']);
            $average['loc'] = array_sum($all['loc']);
            $average['lloc'] = array_sum($all['lloc']);
            $average['cloc'] = array_sum($all['cloc']);
            $average['bugs'] = array_sum($all['bugs']) / sizeof($all['bugs']);
            $average['difficulty'] = array_sum($all['difficulty']) / sizeof($all['difficulty']);
            $average['intelligentContent'] = array_sum($all['intelligentContent']) / sizeof($all['intelligentContent']);
            $average['vocabulary'] = array_sum($all['vocabulary']) / sizeof($all['vocabulary']);
        }
        $this->data = array(
            'average' => $average,
            'cfiles' => $all['cfiles'],
            'files' => $scoreByFile
        );
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

    public function getNumberOfFiles()
    {
        return $this->data['cfiles'];
    }

    public function getFiles() {
        return $this->data['files'];
    }

    public function getName()
    {
        return 'phpmetrics_collector';
    }
}
