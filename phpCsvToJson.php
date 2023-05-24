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
        try {
            $this->fileOutput = file($fileOutput);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function convertToJson(): string
    {
        foreach ($this->fileOutput as $index => $value) {
            try {
                $columnFields = explode($this->separator, $value);
            } catch (Exception $e) {
                return 'Error: ' . $e->getMessage();
            }
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

$myJsonFile = new Converter($csvFile, $delimiter ?: ',');
echo "\n\n" . $myJsonFile->convertToJson() . "\n\n";
