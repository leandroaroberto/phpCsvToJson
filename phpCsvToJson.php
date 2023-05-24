<?php

class Converter
{
    private $fileOutput;
    private $fieldNames = array();
    private $finalOutput = '{ "values" : [';
    private $separator;

    public function __construct(string $fileOutput, string $separator = ',')
    {
        $this->readTheFile($fileOutput);
        $this->separator = $separator;
    }

    private function readTheFile(string $fileOutput): void
    {
        if (file_exists($fileOutput)) {
            $this->fileOutput = file($fileOutput);
        } else {
            throw new \Error('File not found');
        }
    }

    public function convertToJson(): string
    {
        foreach ($this->fileOutput as $index => $value) {
            $columnFields = explode($this->separator, $value);
            $this->finalOutput .= $index != 0 ? '{' : '';
            foreach ($columnFields as $fieldIndex => $field) {
                if ($index == 0) {
                    array_push($this->fieldNames, $field);
                    continue;
                }
                $this->finalOutput .= '"'. $this->fieldNames[$fieldIndex] .'": "'. $field .'"';
                $this->finalOutput .= $fieldIndex < (count($columnFields)) -1 ? ',': '';
            }
            $this->finalOutput .= $index != 0 ? '},' : '';
        }
        $this->finalOutput = substr($this->finalOutput, 0, -1);
        $this->finalOutput .= ']}';
        return $this->finalOutput;
    }
}

$csvFile = readline('Enter a csv file name: ');
$delimiter = readline("Enter the delimiter (leave it blank for default ','):");

try {
    $myJsonFile = new Converter($csvFile, $delimiter ?: ',');
    $response = $myJsonFile->convertToJson();
    echo "\n\n" . $response . "\n\n";
} catch (\Error $e) {
    echo "Error: " . $e->getMessage() . "\n\n";
}
