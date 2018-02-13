<?php
/**
 * This class compares two strings and outputs the similarities  as percentage
 *
 * @author Hakan BOLAT <bltt.hkn@gmail.com>, Shimba <sh.bumba@gmail.com>
 * @
 */

class StringPercentCompare
{
    private $compare;
    private $compared;
    private $compareCount;
    private $comparedCount;
    private $percent = null;
    private $logList = array();
    public $debug = false;
    public $removeExtraSpaces = true;
    public $removePunctuation = true;
    public $removeHtmlTags = true;
    public $removeWords = true;
    public $removeNonAlphanumeric = true; // remove non_alphanumeric character
    public $nonAlphanumericRegEX = '~[^a-zA-Z0-9.]~'; // except dot if u want to include dot use this reg '~[^a-zA-Z0-9]~'
    public $compareAnyWords = true;
    public $replaceWords = true;
    public $punctuationSymbols = array(
        '.',
        ',',
        '$',
        '*',
        ':',
        ';',
        '!',
        '?',
        '|',
        '<',
        '>',
        '#',
        '~',
        '"',
        '\'',
        '^',
        '(',
        ')'
    );
    public $removeList = array();
    public $replaceList = array();

    /**
     * Constructor
     *
     * @param string $compare
     * @param string $compared
     */
    public function __construct($compare, $compared)
    {
        $this->compare = $compare;
        $this->compared = $compared;
    }

    /**
     * Initialize
     */
    private function initialize()
    {
        $compare = $this->prepareString($this->compare);
        $compared = $this->prepareString($this->compared);

        if ($this->compareAnyWords && strlen($compare) > strlen($compared)) {
            list($compare, $compared) = array(
                $compared,
                $compare
            );
        }

        $this->compare = ' ' . $compare;
        $this->compared = ' ' . $compared;

        $this->compareCount = substr_count($compare, ' ') + 1;
        $this->comparedCount = \count(array_values(array_filter(explode(' ', $compared), function ($value) {
            return $value !== '';
        })));

        $this->addLog($this->compare);
        $this->addLog($this->compared);
    }

    /**
     * Prepare string
     *
     * @param $string
     * @return string
     */
    private function prepareString($string)
    {
        $string = mb_strtolower($string);

        if ($this->removeHtmlTags) {
            $string = strip_tags($string);
        }

        if ($this->removePunctuation) {
            $string = str_replace($this->punctuationSymbols, '', $string);
        }

        if ($this->replaceWords) {
            $string = str_replace(array_keys($this->replaceList), array_values($this->replaceList), $string);
        }

        if ($this->removeWords) {
            $string = str_replace($this->removeList, '', $string);
        }

        if ($this->removeNonAlphanumeric) {
            $string = preg_replace($this->nonAlphanumericRegEX, ' ', $string);
        }

        if ($this->removeExtraSpaces) {
            $string = preg_replace('#\s+#u', ' ', $string);
        }

        return trim($string);
    }

    /**
     * Create RegExp string
     *
     * @param $compare
     * @param $compared
     * @return mixed|string
     */
    public function setRegEx($compare, $compared)
    {
        $regex = '~(\\b';

        foreach ($compared as $key => $word) {
            $regex .= $word . ' ' . ($key !== ($this->comparedCount - 1) ? '|\\b' : '');
        }

        $regex .= ')~i';
        // Delete last whitespace character in regex
        if (is_numeric(strpos($regex, '|' . substr($compare, strrpos($compare, ' ', -1) + 1)))) {
            $searchString = '|' . substr($compare, strrpos($compare, ' ', -1) + 1) . ' ';
            $replaceString = '|' . substr($compare, strrpos($compare, ' ', -1) + 1);
            $regex = str_replace($searchString, $replaceString, $regex);
        }

        return $regex;
    }

    /**
     * Function to compare two strings and return the similarity in percentage
     *
     * @return bool
     */
    private function compare()
    {
        if (null !== $this->percent) {
            return false;
        }

        $compare = $this->compare;
        $compared = $this->compared;

        $compared = explode(' ', $compared);
        array_multisort(array_map('strlen', $compared), $compared);
        $compared = array_reverse($compared);
        $compared = array_values(array_filter($compared, function ($value) {
            return $value !== '';
        }));

        $regex = $this->setRegEx($compare, $compared);
        $this->addLog($regex);

        $wordsFound = preg_replace($regex, '- ', $compare . ' ');
        $this->addLog($wordsFound);

        $wordsFound = preg_replace('[- ]', '*', $wordsFound);
        $this->addLog($wordsFound);

        $wordsFound = preg_replace('~[^*]~', '', $wordsFound);
        $this->addLog($wordsFound);

        $wordsFoundCount = strlen($wordsFound);
        $percent = ($wordsFoundCount / $this->compareCount) * 100;

        if ($this->compareCount !== $this->comparedCount && (int) $percent === 100) {
            $percent -= 5;
        }

        $this->percent = number_format($percent, 2, '.', '');

        return true;
    }

    /**
     * Function to compare two strings and return the similarity in percentage
     *
     * @access public
     * @return float
     */
    public function getSimilarityPercentage()
    {
        $this->initialize();
        $this->compare();

        return $this->percent;
    }

    /**
     * Get log data
     *
     * @access public
     * @return array
     */
    public function getLog()
    {
        return $this->logList;
    }

    /**
     * Add log message
     *
     * @param $message
     * @return bool
     */
    private function addLog($message)
    {
        if (!$this->debug) {
            return false;
        }

        $this->logList[] = $message;

        return true;
    }
}
