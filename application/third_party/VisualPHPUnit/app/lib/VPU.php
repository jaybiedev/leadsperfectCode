<?php
/**
 * VisualPHPUnit
 *
 * PHP Version 5.3<
 *
 * @author    Nick Sinopoli <NSinopoli@gmail.com>
 * @copyright 2011-2015 VisualPHPUnit
 * @license   http://opensource.org/licenses/BSD-3-Clause The BSD License
 * @link      https://github.com/VisualPHPUnit/VisualPHPUnit VisualPHPUnit
 */
namespace app\lib;

use \RecursiveDirectoryIterator;
use \RecursiveIteratorIterator;
use \DomainException;
use \PHPUnit_Framework_TestResult;
use \PHPUnit_Util_Log_JSON;
use \PHPUnit_Framework_TestSuite;
use \PHPUnit_Util_Configuration;
use app\lib\LogJSONWithStringComparison;

/**
 * VPU
 *
 * Main class for processing test data
 *
 * @author Nick Sinopoli <NSinopoli@gmail.com>
 */
class VPU
{

    /**
     * The error messages collected by the custom error handler.
     *
     * @var array
     */
    protected $errors = array();

    /**
     * Adds percentage statistics to the provided statistics.
     *
     * @param array $statistics
     *            The statistics.
     * @return array
     */
    protected function addPercentages($statistics)
    {
        $results = array();
        foreach ($statistics as $name => $stats) {
            $results[$name] = $stats;
            foreach ($stats as $key => $value) {
                if ($key == 'total') {
                    continue;
                }
                // Avoid divide by zero error
                if ($stats['total']) {
                    $results[$name]['percent' . ucfirst($key)] = round($stats[$key] / $stats['total'] * 100, 1);
                } else {
                    $results[$name]['percent' . ucfirst($key)] = 0;
                }
            }
        }
        
        return $results;
    }

    /**
     * Returns the class name without the namespace.
     *
     * @param string $class
     *            The class name.
     * @return string
     */
    protected function classnameOnly($class)
    {
        $name = explode('\\', $class);
        return end($name);
    }

    /**
     * Organizes the output from PHPUnit into a more manageable array
     * of suites and statistics.
     *
     * @param string $pu_output
     *            The JSON output from PHPUnit.
     * @param string $source
     *            The executing source (web or cli).
     * @return array
     */
    public function compileSuites($pu_output, $source)
    {
        $results = $this->parseOutput($pu_output);
        
        $collection = array();
        $statistics = array(
            'suites' => array(
                'succeeded' => 0,
                'skipped' => 0,
                'incomplete' => 0,
                'failed' => 0,
                'total' => 0
            )
        );
        $statistics['tests'] = $statistics['suites'];
        
        foreach ($results as $result) {
            if (! isset($result['event']) || $result['event'] != 'test') {
                continue;
            }
            
            $suite_name = $this->classnameOnly($result['suite']);
            
            if (! isset($collection[$suite_name])) {
                $collection[$suite_name] = array(
                    'tests' => array(),
                    'name' => $suite_name,
                    'status' => 'succeeded',
                    'time' => 0
                );
            }
            $result = $this->formatTestResults($result, $source);
            $collection[$suite_name]['tests'][] = $result;
            $collection[$suite_name]['status'] = $this->getSuiteStatus(
                $result['status'],
                $collection[$suite_name]['status']
            );
            $collection[$suite_name]['time'] += $result['time'];
            $statistics['tests'][$result['status']] += 1;
            $statistics['tests']['total'] += 1;
        }
        
        foreach ($collection as $suite) {
            $statistics['suites'][$suite['status']] += 1;
            $statistics['suites']['total'] += 1;
        }
        
        $final = array(
            'suites' => $collection,
            'stats' => $this->addPercentages($statistics)
        );
        
        return $final;
    }

    /**
     * Converts the first nested layer of PHPUnit-generated JSON to an
     * associative array.
     *
     * @param string $str
     *            The JSON output from PHPUnit.
     * @return array
     */
    protected function convertJson($str)
    {
        $str = str_replace('&quot;', '"', $str);
        
        $tags = array();
        $nest = 0;
        $start_mark = 0;
        $in_quotes = false;
        
        $length = strlen($str);
        for ($i = 0; $i < $length; $i ++) {
            $char = $str{$i};
            
            if ($char == '"') {
                // Escaped quote in debug output
                if (! $in_quotes || $str{$i - 1} == "\\") {
                    $i = strpos($str, '"', $i + 1) - 1;
                    $in_quotes = true;
                } else {
                    $in_quotes = false;
                }
                continue;
            }
            
            if ($char == '{') {
                $nest ++;
                if ($nest == 1) {
                    $start_mark = $i;
                }
            } elseif ($char == '}' && $nest > 0) {
                if ($nest == 1) {
                    $tags[] = substr($str, $start_mark + 1, $i - $start_mark - 1);
                    $start_mark = $i;
                }
                $nest --;
            }
        }
        
        return $tags;
    }

    /**
     * Normalizes the test results.
     *
     * @param array $test_results
     *            The parsed test results.
     * @param string $source
     *            The executing source (web or cli).
     * @return string
     */
    protected function formatTestResults($test_results, $source)
    {
        $status = $this->getTestStatus($test_results['status'], $test_results['message']);
        $name = substr($test_results['test'], strpos($test_results['test'], '::') + 2);
        $time = $test_results['time'];
        $message = $test_results['message'];
        $output = (isset($test_results['output'])) ? trim($test_results['output']) : '';
        $trace = $this->getTrace($test_results['trace'], $source);
        
        return compact('status', 'name', 'time', 'message', 'output', 'trace');
    }

    /**
     * Returns the errors collected by the custom error handler.
     *
     * @access public
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Determines the overall suite status based on the current status
     * of the suite and the status of a single test.
     *
     * @param string $test_status
     *            The status of the test.
     * @param string $suite_status
     *            The current status of the suite.
     * @return string
     */
    protected function getSuiteStatus($test_status, $suite_status)
    {
        if ($test_status === 'incomplete' && $suite_status !== 'failed' && $suite_status !== 'skipped') {
            return 'incomplete';
        }
        if ($test_status === 'skipped' && $suite_status !== 'failed') {
            return 'skipped';
        }
        if ($test_status === 'failed') {
            return 'failed';
        }
        return $suite_status;
    }

    /**
     * Retrieves the status from a PHPUnit test result.
     *
     * @param string $status
     *            The status supplied by VPU's transformed JSON.
     * @param string $message
     *            The message supplied by VPU's transformed JSON.
     * @return string
     */
    protected function getTestStatus($status, $message)
    {
        switch ($status) {
            case 'pass':
                return 'succeeded';
            case 'error':
                if (stripos($message, 'skipped') !== false) {
                    return 'skipped';
                }
                if (stripos($message, 'incomplete') !== false) {
                    return 'incomplete';
                }
                return 'failed';
            case 'fail':
                return 'failed';
            default:
                return '';
        }
    }

    /**
     * Filters the stack trace from a PHPUnit test result to exclude VPU's
     * trace.
     *
     * @param string $stack
     *            The stack trace.
     * @param string $source
     *            The executing source (web or cli).
     * @return string
     */
    protected function getTrace($stack, $source)
    {
        if (! $stack) {
            return '';
        }
        
        ob_start();
        if ($source == 'web') {
            print_r(array_slice($stack, 0, - 6));
        } else {
            print_r(array_slice($stack, 0, - 2));
        }
        $trace = trim(ob_get_contents());
        if (ob_get_length()) {
            ob_end_clean();
        }
        
        return $trace;
    }

    /**
     * Serves as the error handler.
     *
     * @param integer $number
     *            The level of the error raised.
     * @param string $message
     *            The error message.
     * @param string $file
     *            The file in which the error was raised.
     * @param integer $line
     *            The line number at which the error was raised.
     * @return bool
     */
    public function handleErrors($number, $message, $file, $line)
    {
        if ($number > error_reporting()) {
            return true;
        }
        
        switch ($number) {
            case E_WARNING:
                $type = 'E_WARNING';
                break;
            case E_NOTICE:
                $type = 'E_NOTICE';
                break;
            case E_USER_ERROR:
                $type = 'E_USER_ERROR';
                break;
            case E_USER_WARNING:
                $type = 'E_USER_WARNING';
                break;
            case E_USER_NOTICE:
                $type = 'E_USER_NOTICE';
                break;
            case E_STRICT:
                $type = 'E_STRICT';
                break;
            case E_RECOVERABLE_ERROR:
                $type = 'E_RECOVERABLE_ERROR';
                break;
            case E_DEPRECATED:
                $type = 'E_DEPRECATED';
                break;
            case E_USER_DEPRECATED:
                $type = 'E_USER_DEPRECATED';
                break;
            default:
                $type = 'Unknown';
                break;
        }
        $this->errors[] = compact('type', 'message', 'file', 'line');
        return true;
    }

    /**
     * Parses and formats the JSON output from PHPUnit into an associative array.
     *
     * @param string $pu_output
     *            The JSON output from PHPUnit.
     * @return array
     */
    protected function parseOutput($pu_output)
    {
        $results = '';
        foreach ($this->convertJson($pu_output) as $elem) {
            $elem = '{' . $elem . '}';
            $pos = strpos($pu_output, $elem);
            $pu_output = substr_replace($pu_output, '|||', $pos, strlen($elem));
            $results .= $elem . ',';
        }
        
        $results = '[' . rtrim($results, ',') . ']';
        
        $results = json_decode($results, true);
        
        // For PHPUnit 3.5.x, which doesn't include test output in the JSON
        $pu_output = explode('|||', $pu_output);
        foreach ($pu_output as $key => $data) {
            if ($data) {
                $results[$key]['output'] = $data;
            }
        }
        
        return $results;
    }

    /**
     * Retrieves the files from any supplied directories, and filters
     * the list of tests by ensuring that the files exist and are PHP files.
     *
     * @param array $tests
     *            The directories/filenames containing the tests to
     *            be run through PHPUnit.
     * @return array
     */
    protected function parseTests($tests)
    {
        $collection = array();
        
        foreach ($tests as $test) {
            if (is_dir($test)) {
                $it = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator(realpath($test)),
                    RecursiveIteratorIterator::LEAVES_ONLY
                );
                while ($it->valid()) {
                    $ext = strtolower(pathinfo($it->key(), PATHINFO_EXTENSION));
                    if (! $it->isDot() && $ext == 'php') {
                        $collection[] = $it->key();
                    }
                    
                    $it->next();
                }
                continue;
            }
            
            $ext = strtolower(pathinfo($test, PATHINFO_EXTENSION));
            if (file_exists($test) && $ext == 'php') {
                $collection[] = realpath($test);
            }
        }
        // Avoid returning duplicates
        return array_keys(array_flip($collection));
    }

    /**
     * Runs supplied tests through PHPUnit.
     *
     * @param array $tests
     *            The directories/filenames containing the tests
     *            to be run through PHPUnit.
     * @return string
     */
    public function runTests($tests)
    {
        $suite = new PHPUnit_Framework_TestSuite();
        
        $tests = $this->parseTests($tests);
        $original_classes = get_declared_classes();
        foreach ($tests as $test) {
            require $test;
        }
        $new_classes = get_declared_classes();
        $tests = array_diff($new_classes, $original_classes);
        foreach ($tests as $test) {
            if (is_subclass_of($test, 'PHPUnit_Framework_TestCase')) {
                $suite->addTestSuite($test);
            }
        }
        
        $result = new PHPUnit_Framework_TestResult();
        $result->addListener(new LogJSONWithStringComparison("/tmp/res.json"));
        
        // We need to temporarily turn off html_errors to ensure correct
        // parsing of test debug output
        $html_errors = ini_get('html_errors');
        ini_set('html_errors', 0);

        ob_start();
        $suite->run($result);
        $results = file_get_contents("/tmp/res.json");
        if (ob_get_length()) {
            ob_end_clean();
        }
        
        ini_set('html_errors', $html_errors);
        return $results;
    }

    /**
     * Checks that the provided XML configuration file contains the necessary
     * JSON listener.
     *
     * @param string $xml_config
     *            The path to the PHPUnit XML configuration
     *            file.
     * @return void
     */
    protected function checkXmlConfiguration($xml_config)
    {
        $file = simplexml_load_file($xml_config);
        if ($file !== false) {
            if (isset($file['bootstrap'])) {
                $bootstrap = dirname($xml_config) . DIRECTORY_SEPARATOR . (string) $file['bootstrap'];
                if (is_file($bootstrap)) {
                    require_once $bootstrap;
                } else {
                    throw new DomainException("XML Configuration bootstrap file '" .
                        ((string) $file['bootstrap']) . "' not found");
                }
            }
        }
        $configuration = PHPUnit_Util_Configuration::getInstance($xml_config);
        $listeners = $configuration->getListenerConfiguration();
        
        $required_listener = 'PHPUnit_Util_Log_JSON';
        $found = false;
        foreach ($listeners as $listener) {
            if ($listener['class'] === $required_listener) {
                $found = true;
                break;
            }
        }
        if (! $found) {
            throw new DomainException(
                "XML Configuration file doesn't contain the required {$required_listener} listener."
            );
        }
    }

    /**
     * Runs PHPUnit with the supplied XML configuration file.
     *
     * @param string $xml_config
     *            The path to the PHPUnit XML configuration
     *            file.
     * @return string
     */
    public function runWithXml($xml_config)
    {
        $this->checkXmlConfiguration($xml_config);
        
        // We need to temporarily turn off html_errors to ensure correct
        // parsing of test debug output
        $html_errors = ini_get('html_errors');
        ini_set('html_errors', 0);

        $bin_path = Library::retrieve('composer_bin_path');
        $command = "$bin_path/phpunit --configuration $xml_config --stderr";
        $results = shell_exec($command);

        ini_set('html_errors', $html_errors);

        return $results;
    }
}
