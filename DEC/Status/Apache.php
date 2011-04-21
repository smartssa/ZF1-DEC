<?php
/**
 * Parse the /server-status?auto and give me a useful object.
 *
 * Total Accesses: 108761 (hits)
 * Total kBytes: 23258 (bandwidth)
 * CPULoad: .00486603 (cpu used)
 * Uptime: 1761806 (seconds)
 * ReqPerSec: .0617327 (hits/time)
 * BytesPerSec: 13.5181 (kb/time)
 * BytesPerReq: 218.977 (kb/reqs)
 * BusyWorkers: 1
 * IdleWorkers: 10
 * Scoreboard: ___._.____.W__
 *
 * @author dclarke
 *
 */
class DEC_Status_Apache {

    const STATUS_ACTIVE   = 1;
    const STATUS_CLEAN    = 2;
    const STATUS_WARN     = 4;
    const STATUS_ALERT    = 8;
    const STATUS_CRIT     = 16;
    const STATUS_DISABLED = 128;

    protected $_totalAccesses = 0;
    protected $_totalkBytes   = 0;
    protected $_cpuLoad       = 0;
    protected $_busyWorkers   = 0;
    protected $_idleWorkers   = 0;
    protected $_uptime        = 0;
    protected $_bytesPerReq   = 0;
    protected $_reqPerSec     = 0;
    protected $_bytesPerSec   = 0;
    /**
     * This is the maximum number of workers configured
     * not available via the scoreboard
     * @var int
     */
    protected $_configuredWorkerLimit;

    /**
     * Where to get the status from
     * @var string
     */
    protected $_statusUrl;

    /**
     * up, down, what?
     * @var int
     */
    protected $_status = null;

    /**
     * Whether or not the url has been fetched
     * @var bool
     */
    protected $_fetchedUrl = false;

    /**
     * fetch apache scoreboard from url
     * @param string $statusUrl
     * @param int $configuredWorkerLimit
     * @param bool $autoUpdate
     */
    public function __construct($statusUrl, $configuredWorkerLimit, $autoUpdate = true) {

        $this->setStatusUrl($statusUrl);
        $this->setConfiguredWorkerLimit($configuredWorkerLimit);

        if ($autoUpdate) {
            $this->fetchAndParse();
        }
    }

    public function fetchAndParse()
    {
        $client = new Zend_Http_Client($this->getStatusUrl(),
        array('timeout' => '1')); // 1 second, if it's slower than that the server should be flagged as 'oh shit'

        try {
            $result = $client->request()->getBody();
            $this->_fetchedUrl = true;
            // parse the result
            $lines = explode("\n", $result);
            foreach ($lines as $line) {
                if ($line != '') {
                    list($key, $value) = explode(':', $line);
                    $method = 'set' . str_replace(' ', '', $key);
                    if (method_exists($this, $method)) {
                        $this->$method($value);
                    }
                }
            }
            $this->setStatus();
        } catch (Zend_Http_Client_Exception $e) {
            $this->_fetchedUrl = false;
            $this->_status     = self::STATUS_DISABLED; 
            echo $e->getMessage();
        }

    }

    public function setStatus()
    {
        // calculate status/health of the server based on other values.
        $threshold = number_format(($this->getBusyWorkers() / $this->getConfiguredWorkerLimit()) * 10, 0);
        switch ($threshold) {
            case "0":
            case "1":
            case "2":
            case "3":
            case "4":
            case "5":
            case "6":
                $this->_status = self::STATUS_CLEAN + self::STATUS_ACTIVE;
                break;
            case "7":
            case "8":
                $this->_status = self::STATUS_WARN + self::STATUS_ACTIVE;
                break;
            case "9":
            case "10":
            default:
                $this->_status = self::STATUS_CRIT + self::STATUS_ACTIVE;
                break;
        }
        return $this;
    }

    public function getStatus()
    {
        return $this->_status;
    }

    public function isEnabled()
    {
        return $this->_status;
    }

    public function setConfiguredWorkerLimit($configuredWorkerLimit)
    {
        if ($configuredWorkerLimit < 1) {
            throw new DEC_Status_Apache_Exception('configuredWorkerLimit must be greater than 1, given: ' . $configuredWorkerLimit);
        }

        $this->_configuredWorkerLimit = $configuredWorkerLimit;
        return $this;
    }

    public function getConfiguredWorkerLimit()
    {
        return $this->_configuredWorkerLimit;
    }

    public function setStatusUrl($statusUrl)
    {
        if (! Zend_Uri::check($statusUrl)) {
            throw new DEC_Status_Apache_Exception('Url Provided is not a valid format: ' . $statusUrl);
        }

        $this->_statusUrl = $statusUrl;
        return $this;
    }

    public function getStatusUrl()
    {
        return $this->_statusUrl;
    }

    protected function setTotalAccesses($totalAccesses)
    {
        $this->_totalAccesses = (int)$totalAccesses;
        return $this;
    }

    public function getTotalAccesses()
    {
        return $this->_totalAccesses;
    }

    protected function setTotalkBytes($totalkBytes) {
        $this->_totalkBytes = $totalkBytes;
        return $this;
    }

    public function getTotalkBytes()
    {
        return $this->_totalkBytes;
    }

    protected function setCpuLoad($cpuLoad)
    {
        $this->_cpuLoad = $cpuLoad;
        return $this;
    }

    public function getCpuLoad()
    {
        return $this->_cpuLoad;
    }

    protected function setBusyWorkers($busyWorkers)
    {
        $this->_busyWorkers = (int)$busyWorkers;
        return $this;
    }

    public function getBusyWorkers()
    {
        return $this->_busyWorkers;
    }

    protected function setIdleWorkers($idleWorkers)
    {
        $this->_idleWorkers = (int) $idleWorkers;
        return $this;
    }

    public function getIdleWorkers()
    {
        return $this->_idleWorkers;
    }

    protected function setUptime($uptime)
    {
        $this->_uptime = (int) $uptime;
        return $this;
    }

    public function getUptime()
    {
        return $this->_uptime;
    }

    protected function setReqPerSec($reqPerSec)
    {
        $this->_reqPerSec = $reqPerSec;
        return $this;
    }

    public function getReqsPerSec()
    {
        return $this->_reqsPerSec;
    }

    protected function setBytesPerSec($bytesPerSec)
    {
        $this->_bytesPerSec = $bytesPerSec;
        return $this;
    }

    public function getBytesPerSec()
    {
        return $this->_bytesPerSec;
    }

    protected function setBytesPerReq($bytesPerReq)
    {
        $this->_bytesPerReq = $bytesPerReq;
        return $this;
    }

    public function getBytesPerReq()
    {
        return $this->_bytesPerReq;
    }
}
