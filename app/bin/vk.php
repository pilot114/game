<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Column;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Row;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use VK\Client\Enums\VKLanguage;
use VK\Client\VKApiClient;

include __DIR__ . '/../vendor/autoload.php';

function getUserItems(int $year, ?int $month = null): array
{
    // https://vkhost.github.io/
    $accessToken = 'vk1.a.WY6yKs3UAcvXzYx9XUUf33Gp9IAe3VPdyRj0ZjPhQ42HlatQKp3NmP4LyOIVJFTnEuO6kdrxJbX6LoAPOZklh-ALzg2K06TOxb5c-jhBAfZxYZXPpisZzGjOnKqBbP3Pe3YUiHmJ4nP1_z0YTcLHfLMgmpqoqsUUeIZHI9EI_F_DOJXB1iYZhHb63ndF2gD0zK7GOTG3yqlfkVTBLr0WqQ';

    $fields = [
        'about', 'bdate', 'last_seen', 'sex',
        'city', 'country',
        'photo_400_orig',
        'home_town', 'schools', 'universities', 'education',
    ];

    $params = [
        'q'  => ['Смолин'],
        'count' => 1000,
        'fields' => $fields,
        'birth_year' => $year,
//        'age_from' => 70
    ];
    if ($month !== null) {
        $params['birth_month'] = $month;
    }

    $vk = new VKApiClient('5.131', VKLanguage::RUSSIAN);
    $response = $vk->users()->search($accessToken, $params);
    return $response['items'] ?? [];
}

class VkUser
{
    public int $id;
    public string $firstName;
    public string $lastName;

    public ?string $photo = null;
    public ?\DateTimeInterface $lastSeen = null;
    public ?\DateTimeInterface $birthDay = null;
    public ?string $sex = null;
    public ?string $city = null;
    public ?string $country = null;
    public ?string $homeTown = null;
    public ?int $cityId = null;
    public ?int $countryId = null;
    public ?string $schools = null;
    public ?string $universities = null;

    public function __construct(array $data) {
        $this->id = $data['id'];
        $this->sex = $data['sex'] === 1 ? 'жен' : ($data['sex'] === 2 ? 'муж' : null);
        $this->firstName = $data['first_name'];
        $this->lastName = $data['last_name'];

        if (!empty($data['photo_400_orig'])) {
            $this->photo = $data['photo_400_orig'];
        }
        if (!empty($data['bdate'])) {
            try {
                $this->birthDay = new DateTimeImmutable($data['bdate']);
            } catch (\DateMalformedStringException) {}
        }
        if (!empty($data['last_seen'])) {
            $this->lastSeen = (new DateTime())->setTimestamp($data['last_seen']['time']);
        }
        if (!empty($data['country'])) {
            $this->country = $data['country']['title'];
            $this->countryId = $data['country']['id'];
        }
        if (!empty($data['city'])) {
            $this->city = $data['city']['title'];
            $this->cityId = $data['city']['id'];
        }
        if (!empty($data['home_town'])) {
            $this->homeTown = $data['home_town'];
        }

        if (!empty($data['schools'])) {
            $tmp = [];
            foreach ($data['schools'] as $item) {
                $tmp[] = [
                    'city' => $item['city'] ?? null,
                    'country' => $item['country'] ?? null,
                    'name' => $item['name'] ?? null,
                    'year_graduated' => $item['year_graduated'] ?? $item['year_to'] ?? null,
                    'year_from' => $item['year_from'] ?? null,
                ];
            }
            $this->schools = json_encode($tmp);
        }
        if (!empty($data['universities'])) {
            $tmp = [];
            foreach ($data['universities'] as $item) {
                $tmp[] = [
                    'city' => $item['city'] ?? null,
                    'country' => $item['country'] ?? null,
                    'name' => ($item['name'] ?? null) . (isset($item['chair_name']) ? " ({$item['chair_name']})" : null),
                    'graduation' => $item['graduation'] ?? null,
                    'faculty_name' => $item['faculty_name'] ?? null,
                ];
            }
            $this->universities = json_encode($tmp);
        }
    }
}

abstract class BaseExcelService
{
    protected Spreadsheet $spreadsheet;
    protected Worksheet $activeWorksheet;

    public function __construct()
    {
        $this->spreadsheet = new Spreadsheet();
        $this->activeWorksheet = $this->spreadsheet->getActiveSheet();
    }

    protected function eachItem(Closure $fn, iterable $items): self
    {
        foreach ($items as $i => $item) {
            $row = $i + 2;
            $data = $fn($item, $row);

            if ($i === 0) {
                $this->activeWorksheet->fromArray(array_keys($data));
            }
            $this->activeWorksheet->fromArray(array_values($data), startCell: "A$row");
        }
        return $this;
    }

    protected function eachColumn(Closure $fn): self
    {
        foreach ($this->activeWorksheet->getColumnIterator() as $column) {
            $fn($column);
        }
        return $this;
    }

    protected function eachRow(Closure $fn): self
    {
        foreach ($this->activeWorksheet->getRowIterator() as $row) {
            $fn($row);
        }
        return $this;
    }

    protected function writeFile(string $name, string $type = 'Xlsx'): void
    {
        $writer = IOFactory::createWriter($this->spreadsheet, $type);
        $writer->save($name);
    }

    // TODO: read interface

    protected function readWorksheet(string $name, ?int $index = null): array
    {
        $this->spreadsheet = IOFactory::load($name);
        if ($index === null) {
            $this->activeWorksheet = $this->spreadsheet->getActiveSheet();
        } else {
            $this->activeWorksheet = $this->spreadsheet->getSheet($index);
        }
        return $this->activeWorksheet->toArray();
    }
}

class VkExcelService extends BaseExcelService
{
    public function handle(array $items, string $name): void
    {
        $this
            ->eachItem($this->addUser(...), $items)
            ->eachColumn($this->setWidth(...))
            ->eachRow($this->setStyle(...))
            ->writeFile($name)
        ;
    }

    protected function addUser(VkUser $user, int $rowIndex): array
    {
        echo "$rowIndex - $user->id\n";

        return [
            'ссылка'              => $this->addUrl($user->id, $rowIndex),
            'имя'                 => $user->firstName,
            'фамилия'             => $user->lastName,
            'пол'                 => $user->sex,
            'день рождения'       => $user->birthDay,
            'фото'                => $user->photo ? $this->addPhoto($user->photo, $rowIndex) : null,
            'последнее посещение' => $user->lastSeen,
            'страна'              => $user->country,
            'город'               => $user->city,
            'родной город'        => $user->homeTown,
            'школы'               => $user->schools ? $this->addSchools($user->schools) : null,
            'вузы'                => $user->universities ? $this->addUniversities($user->universities) : null,
        ];
    }

    protected function addUniversities(string $universities): string
    {
        $universities = json_decode($universities, true);
        foreach ($universities as &$university) {
            $tmp = $university['name'];
            if ($university['faculty_name']) {
                $tmp .= " ({$university['faculty_name']})";
            }
            if ($university['graduation']) {
                $tmp .= ' - ' . $university['graduation'];
            }
            $university = $tmp;
        }
        return implode("\n", $universities);
    }

    protected function addSchools(string $schools): string
    {
        $schools = json_decode($schools, true);
        foreach ($schools as &$school) {
            $tmp = $school['name'];
            if ($school['year_from'] || $school['year_graduated']) {
                $tmp .= ': ';
            }
            if ($school['year_from']) {
                $tmp .= $school['year_from'];
            }
            if ($school['year_from'] && $school['year_graduated']) {
                $tmp .= '-';
            }
            if ($school['year_graduated']) {
                $tmp .= $school['year_graduated'];
            }
            $school = $tmp;
        }
        return implode("\n", $schools);
    }

    protected function addUrl(int $userId, int $rowIndex): string
    {
        $url = "https://vk.com/id$userId";
        $this->activeWorksheet->getCell("A$rowIndex")->getHyperlink()->setUrl($url);
        return $url;
    }

    protected function addPhoto(string $photo, int $rowIndex): null
    {
        try {
            $tempFile = sys_get_temp_dir() . "/$rowIndex.jpg";
            file_put_contents($tempFile, file_get_contents($photo));

            $drawing = new Drawing();
            $drawing->setPath($tempFile);
            $drawing->setCoordinates("F$rowIndex");
            $drawing->setWidthAndHeight(100, 100);
            $drawing->setWorksheet($this->activeWorksheet);
        } catch (\Throwable) {}
        return null;
    }

    protected function setWidth(Column $column): void
    {
        $letter = $column->getColumnIndex();
        if ($letter === 'F') {
            // ширина колонки с картинкой
            $this->activeWorksheet->getColumnDimension('F')->setWidth(12);
            return;
        }
        // автоширина
        $this->activeWorksheet->getColumnDimension($letter)->setAutoSize(true);
    }

    protected function setStyle(Row $row): void
    {
        $number = $row->getRowIndex();
        if ($number === 1) {
            $this->activeWorksheet->getStyle($number)
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            foreach ($row->getColumnIterator() as $cell) {
                $this->activeWorksheet->getStyle($cell->getCoordinate())
                    ->getFill()->setFillType(Fill::FILL_PATTERN_LIGHTGRAY);
            }
            return;
        }
        // высота колонок
        $this->activeWorksheet->getRowDimension($number)->setRowHeight(75);
        $this->activeWorksheet->getStyle($number)
            ->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    }
}

class CombineVkExcelService extends VkExcelService
{
    protected array $data  = [];
    protected int $nextRow = 2;

    public function append(string $filename): void
    {
        $import = new class() extends BaseExcelService {
            public function extractImages(): \Generator
            {
                foreach ($this->activeWorksheet->getDrawingCollection() as $drawing) {
                    $zipReader = fopen($drawing->getPath(),'r');
                    $imageContents = '';
                    while (!feof($zipReader)) {
                        $imageContents .= fread($zipReader,1024);
                    }
                    fclose($zipReader);
                    yield $imageContents;
                }
            }
        };

        $data = $import->readWorksheet($filename);

        // заголовки, просто перезаписываем
        $this->activeWorksheet->fromArray($data[0]);

        // данные
        array_shift($data);
        foreach ($data as $i => $row) {
            $realRow = $this->nextRow + $i;
            $this->activeWorksheet->fromArray($row, startCell: "A$realRow");
            $this->activeWorksheet->getCell("A$realRow")->getHyperlink()->setUrl($row[0]);
        }
        // картинки
        foreach ($import->extractImages() as $i => $extractImage) {
            $realRow = $this->nextRow + $i;

            $tempFile = sys_get_temp_dir() . "/$realRow.jpg";
            file_put_contents($tempFile, $extractImage);

            $this->addPhoto($tempFile, $realRow);
        }

        // стили и ширина
        $this
            ->eachColumn($this->setWidth(...))
            ->eachRow($this->setStyle(...))
        ;

        $this->nextRow += count($data);
    }

    public function save(string $name): void
    {
        $this->writeFile($name);
    }
}

error_reporting(E_ALL & ~E_NOTICE);
ini_set('memory_limit', '2G');

//$year = 2024;
//$items = getUserItems($year);
//if ($items === []) {
//    exit('Not found');
//}
//$items = array_map(fn(array $item) => new VkUser($item), $items);
//
//$service = new VkExcelService();
//$service->handle($items, "./excel/$year.xlsx");

$service = new CombineVkExcelService();
foreach (glob('./excel/*.xlsx') as $item) {
    $service->append($item);
    dump("$item done");
}
$service->save("./excel/all.xlsx");
