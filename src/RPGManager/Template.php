<?php

namespace RPGManager;

class Template
{
    public function getDate()
    {
        $date = date("d-m-Y");
        $heure = date("H:i");
        return $date . " " . $heure;
    }

    public function writeActionLogs($file, $text)
    {
        $date = $this->getDate();
        $line = $date . " => " . $text . "\n";

        $filename = 'logs/' . $file;
        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename), 0755, true);
        }
        
        $file_log = fopen($filename, 'a');
        fwrite($file_log, $line);
        fclose($file_log);
    }

    public function writeAccessLog($log)
    {
        $this->writeActionLogs('access.log', $log);
    }

    public function writeErrorLog($log)
    {
        $this->writeActionLogs('error.log', $log);
    }

    public function writeActionLog($log)
    {
        $this->writeActionLogs('action.log', $log);
    }

    public function writeRequestLog($log, $time = 0)
    {
        $log .= " || => " . number_format($time * 1000, 2) . " ms";
        $this->writeActionLogs('request.log', $log);
    }
}
