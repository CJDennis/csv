<?php
// A wrapper around PHP's native CSV functions to allow CSV manipulation without using real files
// Copyright (c) 2017 Daniel Klein

class CSV {
    protected $delimiter;
    protected $enclosure;
    protected $escape;

    protected $data;

    public function __construct($delimiter = ',', $enclosure = '"', $escape = '\\') {
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->escape = $escape;
    }

    public function fetch_line() {
    }

    public function put_line() {
    }

    public function set_contents($contents, $length = 0) {
        if (!is_string($contents)) {
            throw new Exception('The contents must be a string');
        }
        $fh = fopen('php://temp', 'rw');
        fwrite($fh, $contents);
        rewind($fh);
        $this->data = array();
        $line = fgetcsv($fh, $length, $this->delimiter, $this->enclosure, $this->escape);
        while ($line !== false) {
            $this->data[] = $line;
            $line = fgetcsv($fh, $length, $this->delimiter, $this->enclosure, $this->escape);
        }
        if (!feof($fh)) {
            throw new Exception('An error occurred before the end of the data');
        }
        fclose($fh);
    }

    public function get_contents() {
        $fh = fopen('php://temp', 'rw');
        foreach ($this->data as $datum) {
            fputcsv($fh, $datum, $this->delimiter, $this->enclosure, $this->escape);
        }
        rewind($fh);
        $contents = '';
        while (!feof($fh)) {
            $contents .= fread($fh, 65536);
        }
        fclose($fh);
        return $contents;
    }

    public function set_all($data) {
        if (!is_array($data)) {
            throw new Exception('The data must be an array');
        }
        foreach ($data as $datum) {
            if (!is_array($datum)) {
                throw new Exception('Each line in the data must be an array');
            }
            foreach ($datum as $item) {
                if (!is_string($item)) {
                    throw new Exception('Each field must be a string');
                }
            }
        }
        $this->data = $data;
    }

    public function get_all() {
        if (!is_array($this->data)) {
            throw new Exception('No data has been set');
        }
        return $this->data;
    }
}
