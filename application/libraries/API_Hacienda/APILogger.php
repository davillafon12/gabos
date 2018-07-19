<?php

class APILogger{
    
    private $defaultPath = PATH_API_LOGGING;
    
    private function writeToFile($level, $data, $from){
        $dateLog = date("H:i:s d-m-Y", time());
        $dateFile = date("d_m_Y", time());
        file_put_contents($this->defaultPath."_".$dateFile.".log", $dateLog." | ".$level." | ".$from." | ".$data."\n", FILE_APPEND);
    }
    
    public function debug($from, $data){
        $this->writeToFile("DEBUG", $data, $from);
    }
    
    public function info($from, $data){
        $this->writeToFile("INFO", $data, $from);
    }
    
    public function error($from, $data){
        $this->writeToFile("ERROR", $data, $from);
    }
}