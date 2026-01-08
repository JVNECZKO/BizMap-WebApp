<?php

namespace App\Services;

use App\Models\Business;
use App\Models\BusinessPkdCode;
use App\Models\BusinessRawPayload;
use App\Models\ImportSession;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use OpenSpout\Reader\CSV\Options as CsvOptions;
use OpenSpout\Reader\CSV\Reader as CsvReader;
use OpenSpout\Reader\XLSX\Options as XlsxOptions;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Str;
use OpenSpout\Reader\Common\Creator\ReaderFactory;
use XMLReader;

class BusinessImportService
{
    protected array $canonicalColumns = [
        'lp' => 'lp',
        'lp.' => 'lp',
        'nazwa' => 'full_name',
        'nazwapodmiotu' => 'full_name',
        'nazwa_podmiotu' => 'full_name',
        'nip' => 'nip',
        'regon' => 'regon',
        'nazwisko' => 'nazwisko',
        'imie' => 'imie',
        'telefon' => 'telefon',
        'email' => 'email',
        'adreswww' => 'adres_www',
        'www' => 'adres_www',
        'kodpocztowy' => 'kod_pocztowy',
        'powiat' => 'powiat',
        'gmina' => 'gmina',
        'miejscowosc' => 'miejscowosc',
        'ulica' => 'ulica',
        'nrbudynku' => 'nr_budynku',
        'nrlokalu' => 'nr_lokalu',
        'glownykodpkd' => 'glowny_kod_pkd',
        'pozostalekodypkd' => 'pozostale_kody_pkd',
        'rokpkd' => 'rok_pkd',
        'statusdzialalnosci' => 'status_dzialalnosci',
        'datarozpoczeciadzialalnosci' => 'data_rozpoczecia_dzialalnosci',
        'datazakonczeniadzialalnosci' => 'data_zakonczenia_dzialalnosci',
        'datazawieszeniadzialalnosci' => 'data_zawieszenia_dzialalnosci',
        'datawznowieniadzialalnosci' => 'data_wznowienia_dzialalnosci',
        'wojewodztwo' => 'wojewodztwo',
        'województwo' => 'wojewodztwo',
    ];

    public function detectColumns(string $path, string $format): array
    {
        $preview = $this->preview($path, $format, 1);

        return array_keys($preview[0] ?? []);
    }

    public function preview(string $path, string $format, int $limit = 15): array
    {
        $rows = [];
        foreach ($this->rowGenerator($path, $format) as $row) {
            $rows[] = $row;
            if (count($rows) >= $limit) {
                break;
            }
        }

        return $rows;
    }

    public function runChunk(ImportSession $session, array $mapping, array $staticValues = [], string $pkdVersion = '2007'): array
    {
        $normalizedMapping = $this->normalizeMapping($mapping);
        $processed = 0;
        $batch = [];
        $pkdLinks = [];
        $rawPayloads = [];
        $now = Carbon::now();

        $skip = $session->imported_rows;
        $limit = $session->chunk_size;

        foreach ($this->rowGenerator($session->path, $session->format) as $index => $row) {
            if ($index < $skip) {
                continue;
            }

            if ($processed >= $limit) {
                break;
            }

            $normalized = $this->mapRow($row, $normalizedMapping, $staticValues);

            if (! $normalized) {
                $session->imported_rows++;
                continue;
            }

            $businessData = $normalized['business'];
            $businessData['created_at'] = $now;
            $businessData['updated_at'] = $now;
            $batch[] = $businessData;

            foreach ($normalized['pkd_codes'] as $code) {
                $pkdLinks[] = [
                    'slug' => $businessData['slug'],
                    'pkd_code' => $code,
                    'pkd_version' => $pkdVersion,
                    'created_at' => $now,
                ];
            }

            $rawPayloads[] = [
                'slug' => $businessData['slug'],
                'payload' => json_encode($row),
                'source' => $session->format,
                'imported_at' => $now,
            ];

            $processed++;
            $session->imported_rows++;
        }

        if (empty($batch)) {
            $session->status = 'finished';
            $session->finished_at = $now;
            $session->save();

            return ['processed' => 0, 'imported' => $session->imported_rows];
        }

        DB::transaction(function () use ($batch, $pkdLinks, $rawPayloads, $pkdVersion) {
            Business::upsert(
                $batch,
                ['slug'],
                [
                    'nip',
                    'regon',
                    'full_name',
                    'slug',
                    'nazwisko',
                    'imie',
                    'telefon',
                    'email',
                    'adres_www',
                    'wojewodztwo',
                    'powiat',
                    'gmina',
                    'miejscowosc',
                    'ulica',
                    'nr_budynku',
                    'nr_lokalu',
                    'kod_pocztowy',
                    'glowny_kod_pkd',
                    'pozostale_kody_pkd',
                    'rok_pkd',
                    'status_dzialalnosci',
                    'data_rozpoczecia_dzialalnosci',
                    'data_zakonczenia_dzialalnosci',
                    'data_zawieszenia_dzialalnosci',
                    'data_wznowienia_dzialalnosci',
                    'imported_at',
                    'updated_at',
                ]
            );

            $slugs = array_column($batch, 'slug');
            $businesses = Business::query()
                ->whereIn('slug', $slugs)
                ->get(['id', 'slug']);

            $idBySlug = $businesses->pluck('id', 'slug');
            $ids = $businesses->pluck('id')->all();

            if (! empty($pkdLinks) && ! empty($ids)) {
                BusinessPkdCode::whereIn('business_id', $ids)->delete();

                $pivotInsert = [];
                foreach ($pkdLinks as $link) {
                    if (! isset($idBySlug[$link['slug']])) {
                        continue;
                    }

                    $pivotInsert[] = [
                        'business_id' => $idBySlug[$link['slug']],
                        'pkd_code' => $link['pkd_code'],
                        'pkd_version' => $pkdVersion,
                        'created_at' => $link['created_at'],
                    ];
                }

                if (! empty($pivotInsert)) {
                    BusinessPkdCode::insertOrIgnore($pivotInsert);
                }

                $aggregated = [];
                foreach ($pivotInsert as $pivot) {
                    $aggregated[$pivot['pkd_code']] = ($aggregated[$pivot['pkd_code']] ?? 0) + 1;
                }
                foreach ($aggregated as $code => $count) {
                    DB::table('pkd_popularity')->updateOrInsert(
                        ['pkd_code' => $code],
                        ['total' => DB::raw('COALESCE(total,0)+' . (int) $count), 'updated_at' => now(), 'created_at' => now()]
                    );
                }
            }

            if (! empty($rawPayloads) && ! empty($idBySlug)) {
                $rawInsert = [];
                foreach ($rawPayloads as $raw) {
                    if (! isset($idBySlug[$raw['slug']])) {
                        continue;
                    }

                    $rawInsert[] = [
                        'business_id' => $idBySlug[$raw['slug']],
                        'source' => $raw['source'],
                        'payload' => $raw['payload'],
                        'imported_at' => $raw['imported_at'],
                        'created_at' => $raw['imported_at'],
                    ];
                }

                if (! empty($rawInsert)) {
                    BusinessRawPayload::insert($rawInsert);
                }
            }
        });

        $session->status = 'running';
        $session->save();

        return ['processed' => $processed, 'imported' => $session->imported_rows];
    }

    public function wipeBusinesses(): void
    {
        DB::transaction(function () {
            Business::truncate();
            BusinessPkdCode::truncate();
            BusinessRawPayload::truncate();
        });
    }

    protected function mapRow(array $row, array $mapping, array $staticValues): ?array
    {
        $normalizedRow = $this->normalizeRowKeys($row);
        $data = [];

        foreach ($mapping as $source => $target) {
            if ($target === 'ignore' || $target === null) {
                continue;
            }

            $value = $normalizedRow[$source] ?? null;
            if (($value === null || $value === '') && array_key_exists($target, $staticValues)) {
                $value = $staticValues[$target];
            }

            $data[$target] = is_string($value) ? trim($value) : $value;
        }

        if (! isset($data['full_name']) || $data['full_name'] === '') {
            $data['full_name'] = $this->bestFullName($data);
        }

        if (! $data['full_name']) {
            return null;
        }

        $data['slug'] = Business::generateSlug($data['full_name'], $data['nip'] ?? null);
        if (empty($data['nip'])) {
            $data['slug'] .= '-' . substr(md5($data['full_name']), 0, 6);
        }

        $data['imported_at'] = $data['imported_at'] ?? now();

        $dateColumns = [
            'data_rozpoczecia_dzialalnosci',
            'data_zakonczenia_dzialalnosci',
            'data_zawieszenia_dzialalnosci',
            'data_wznowienia_dzialalnosci',
        ];

        foreach ($dateColumns as $column) {
            if (isset($data[$column])) {
                $data[$column] = $this->parseDate($data[$column]);
            }
        }

        $pkdCodes = [];
        if (array_key_exists('glowny_kod_pkd', $data)) {
            $data['glowny_kod_pkd'] = $this->normalizePkdCode($data['glowny_kod_pkd']);
        }
        if (! empty($data['glowny_kod_pkd'])) {
            $pkdCodes[] = $data['glowny_kod_pkd'];
        }

        if (array_key_exists('pozostale_kody_pkd', $data)) {
            $extra = $this->parsePkdList((string) $data['pozostale_kody_pkd']);
            if (! empty($extra)) {
                $pkdCodes = array_merge($pkdCodes, $extra);
                $data['pozostale_kody_pkd'] = implode('$##$', $extra);
            } else {
                $data['pozostale_kody_pkd'] = null;
            }
        }

        $pkdCodes = array_values(array_unique($pkdCodes));

        return [
            'business' => $data,
            'pkd_codes' => $pkdCodes,
        ];
    }

    protected function normalizeMapping(array $mapping): array
    {
        if (empty($mapping)) {
            return $mapping;
        }

        $normalized = [];
        foreach ($mapping as $source => $target) {
            $normalized[$this->canonicalKey($source)] = $target;
        }

        return $normalized;
    }

    protected function normalizeRowKeys(array $row): array
    {
        $normalized = [];
        foreach ($row as $key => $value) {
            $normalized[$this->canonicalKey((string) $key)] = $value;
        }

        return $normalized;
    }

    protected function parseDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function parsePkdList(string $value): array
    {
        $value = trim($value);
        if ($value === '') {
            return [];
        }

        $parts = preg_split('/\\$##\\$|,|;|\\s+/', $value);

        return collect($parts)
            ->map(fn($item) => $this->normalizePkdCode($item))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    protected function normalizePkdCode(?string $code): ?string
    {
        if ($code === null) {
            return null;
        }

        $clean = strtoupper(trim($code));
        $clean = preg_replace('/[^0-9A-Z\\.]/', '', $clean ?? '');
        $clean = str_replace([' ', "\t", "\n", "\r"], '', $clean);

        if ($clean === '') {
            return null;
        }

        if (! preg_match('/\\d/', $clean)) {
            return null;
        }

        if (str_contains($clean, '.')) {
            return $clean;
        }

        if (preg_match('/^(\\d{4})([A-Z])$/', $clean, $m)) {
            return substr($m[1], 0, 2) . '.' . substr($m[1], 2, 2) . '.' . $m[2];
        }

        if (preg_match('/^(\\d{4})$/', $clean, $m)) {
            return substr($m[1], 0, 2) . '.' . substr($m[1], 2, 2);
        }

        if (preg_match('/^(\\d{3})([A-Z])$/', $clean, $m)) {
            return substr($m[1], 0, 2) . '.' . substr($m[1], 2, 1) . '.' . $m[2];
        }

        if (preg_match('/^(\\d{3})$/', $clean, $m)) {
            return substr($m[1], 0, 2) . '.' . substr($m[1], 2, 1);
        }

        return $clean === '' ? null : $clean;
    }

    protected function bestFullName(array $data): ?string
    {
        if (! empty($data['full_name'])) {
            return $data['full_name'];
        }

        $pieces = [];

        if (! empty($data['imie'])) {
            $pieces[] = $data['imie'];
        }
        if (! empty($data['nazwisko'])) {
            $pieces[] = $data['nazwisko'];
        }

        return empty($pieces) ? null : trim(implode(' ', $pieces));
    }

    protected function canonicalKey(string $key): string
    {
        $cleaned = Str::of($key)->lower()->replace([' ', '-', '_', '.'], '');

        return (string) $cleaned;
    }

    protected function rowGenerator(string $path, string $format): \Generator
    {
        $format = strtolower($format);

        if (in_array($format, ['csv', 'xls', 'xlsx'])) {
            yield from $this->spreadsheetGenerator($path, $format);

            return;
        }

        if ($format === 'json') {
            yield from $this->jsonGenerator($path);

            return;
        }

        if ($format === 'xml') {
            yield from $this->xmlGenerator($path);

            return;
        }

        throw new \InvalidArgumentException('Unsupported format: ' . $format);
    }

    protected function spreadsheetGenerator(string $path, string $format): \Generator
    {
        $format = strtolower($format);

        if ($format === 'xls') {
            yield from $this->xlsGenerator($path);
            return;
        }

        $reader = $format === 'csv' ? $this->makeCsvReader() : $this->makeXlsxReader();
        $reader->open($path);

        $headers = [];
        $rowIndex = 0;

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $cells = $row->toArray();
                if ($rowIndex === 0) {
                    $headers = $this->prepareHeaders($cells);
                    $rowIndex++;
                    continue;
                }

                $rowIndex++;
                $assoc = [];
                foreach ($headers as $i => $header) {
                    $assoc[$header] = $cells[$i] ?? null;
                }

                yield $assoc;
            }
            break; // single sheet is enough for imports
        }

        $reader->close();
    }

    protected function makeCsvReader(): CsvReader
    {
        $options = new CsvOptions(
            SHOULD_PRESERVE_EMPTY_ROWS: false,
            FIELD_DELIMITER: ';',
            FIELD_ENCLOSURE: '"',
        );

        return new CsvReader($options);
    }

    protected function makeXlsxReader(): XlsxReader
    {
        $options = new XlsxOptions(
            SHOULD_FORMAT_DATES: false,
            SHOULD_PRESERVE_EMPTY_ROWS: false,
            SHOULD_USE_1904_DATES: false,
            SHOULD_LOAD_MERGE_CELLS: false,
        );

        return new XlsxReader($options);
    }

    protected function jsonGenerator(string $path): \Generator
    {
        $file = new \SplFileObject($path);
        $file->setFlags(\SplFileObject::DROP_NEW_LINE);

        $lineCount = 0;
        while (! $file->eof()) {
            $line = trim((string) $file->fgets());
            if ($line === '') {
                continue;
            }

            $decoded = json_decode($line, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                yield $decoded;
                $lineCount++;
                continue;
            }

            break;
        }

        if ($lineCount === 0) {
            $content = json_decode(file_get_contents($path), true) ?? [];
            foreach ($content as $item) {
                if (is_array($item)) {
                    yield $item;
                }
            }
        }
    }

    protected function xmlGenerator(string $path): \Generator
    {
        $reader = new XMLReader();
        $reader->open($path, null, LIBXML_PARSEHUGE);

        while ($reader->read()) {
            if ($reader->nodeType === XMLReader::ELEMENT && $reader->depth === 1) {
                $node = $reader->expand();
                if ($node === false) {
                    continue;
                }

                $data = [];
                foreach ($node->childNodes as $child) {
                    if (in_array($child->nodeType, [XML_ELEMENT_NODE, XML_TEXT_NODE], true)) {
                        $data[$child->nodeName] = trim($child->textContent);
                    }
                }

                if (! empty($data)) {
                    yield $data;
                }
            }
        }

        $reader->close();
    }

    protected function xlsGenerator(string $path): \Generator
    {
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, false, false, false);

        if (empty($rows)) {
            return;
        }

        $headers = $this->prepareHeaders(array_shift($rows));
        foreach ($rows as $row) {
            $assoc = [];
            foreach ($headers as $i => $header) {
                $assoc[$header] = $row[$i] ?? null;
            }

            yield $assoc;
        }
    }

    protected function prepareHeaders(array $headers): array
    {
        return array_map(function ($header) {
            $header = is_string($header) ? trim($header) : $header;
            $canonical = $this->canonicalKey((string) $header);

            return $this->canonicalColumns[$canonical] ?? $canonical;
        }, $headers);
    }
}
