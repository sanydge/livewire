<?php

namespace App\Http\Livewire;

use League\Csv\Reader;
use Livewire\Component;
use Livewire\WithFileUploads;

class CsvImporter extends Component
{
    use WithFileUploads;

    public bool $open = false;

    public $file;

    public string $model;

    public array $fileHeaders = [];

    public array $columnsToMap = [];

    public array $requiredColumns = [];

    protected $listeners = [
        'toggle'
    ];

    public function mount()
    {
        $this->columnsToMap = collect($this->columnsToMap)
            ->mapWithKeys(fn($column)=> [$column=>''])
            ->toArray();
    }

    public function rules()
    {
        $columnRules = collect($this->requiredColumns)
            ->mapWithKeys(function ($column){
                return ['columnsToMap.'. $column => ['required']];
            })
            ->toArray();

        return array_merge($columnRules, [
          'file' => ['required', 'mimes:csv', 'max:51200']
        ]);
    }

    public function updatedFile()
    {
        $this->validateOnly('file');

        $csv = $this->readCsv($this->file->getRealPath());

        $this->fileHeaders = $csv->getHeader();
    }

    public function import()
    {
        $this->validate();
        dd('import');
    }

    public function readCsv(string $path):Reader
    {
        $stream = fopen( $path, 'r');
        $csv = Reader::createFromStream($stream);
        $csv->setHeaderOffset(0);

        return $csv;

    }

    public function toggle()
    {
        $this->open = !$this->open;
    }

    public function render()
    {
        return view('livewire.csv-importer');
    }
}
