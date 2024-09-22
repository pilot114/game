<?php

// пример отрисовки цветных блоков
function colorExample(): void
{
    foreach (Color::cases() as $case) {
        $buffer = drawBox(width: 12, height: 2);
        echo format($buffer, $case);
    }
}

// пример прогресс-бара
function progressExample(): void
{
    $position = getCursor();
    for ($i = 0; $i <= 100; $i++) {
        echo moveCursor(1, $position[1]);
        $passed = str_repeat('#', $i / 2);
        $left = str_repeat(' ', 50 - strlen($passed));
        echo "Прогресс: [$passed$left] $i%";
        usleep(50_000);
    }
    echo moveCursor(...$position) . "\n";
}

// https://en.wikipedia.org/wiki/Box-drawing_characters
function drawBox(int $width, int $height): string
{
    $corners = ['╭', '╮', '╰', '╯'];
    $lines = ['─', '│'];
    $buffer = $corners[0] . str_repeat($lines[0], $width - 2) . "$corners[1]\n";
    $buffer .= str_repeat($lines[1] . str_repeat(" ", $width - 2) . "$lines[1]\n", $height - 2);
    $buffer .= $corners[2] . str_repeat($lines[0], $width - 2) . "$corners[3]\n";
    return $buffer;
}

enum Color: int
{
    case BLACK = 30;
    case RED = 31;
    case GREEN = 32;
    case YELLOW = 33;
    case BLUE = 34;
    case PURPLE = 35;
    case LIGHT_BLUE = 36;
    case WHITE = 37;
}

enum Format: int
{
    case CLEAR = 0;
    case BOLD = 1;
    case UNDERSCORE = 4;
    case BLINK = 5;
    case INVERSE = 7;
    case HIDE = 8;
}

enum Command: string
{
    case UP = "\033[A";
    case DOWN = "\033[B";
    case RIGHT = "\033[C";
    case LEFT = "\033[D";

    case HOME = "\033[H";
    case END = "\033[F";
    case PAGE_UP = "\033[5~";
    case PAGE_DOWN = "\033[6~";
    case INSERT = "\033[2~";
    case DELETE = "\033[3~";

    case ENTER = "\n";
    case TAB = "\t";
    case BACKSPACE = "\177";
    case ESCAPE = "\033";
}

function getCursor(): array
{
    echo "\033[6n";
    $position = '';
    while ($char = fread(STDIN, 1)) {
        $position .= $char;
        if ($char === 'R') {
            break;
        }
    }
    if (preg_match('/\[(\d+);(\d+)R/', $position, $matches)) {
        return [(int)$matches[2], (int)$matches[1]];
    }
    return [null, null];
}

function getSize(): array
{
    $output = [];
    exec('stty size', $output);
    if (!empty($output) && preg_match('/(\d+) (\d+)/', $output[0], $matches)) {
        return [(int)$matches[2], (int)$matches[1]];
    }
    return [null, null];
}

function moveCursor(int $x, int $y): string
{
    return "\033[{$y};{$x}H";
}

function format(string $text, Color $fg, ?Color $bg = null, ?Format $format = null): string
{
    $sequence = $fg->value;
    if ($bg !== null) {
        $bgValue = $bg->value + 10;
        $sequence .= ";$bgValue";
    }
    if ($format !== null) {
        $sequence .= ";$format->value";
    }
    return "\033[" . $sequence . "m" . $text . "\033[" . Format::CLEAR->value . "m";
}

function enableMouseTracking(): void
{
    echo "\033[?1000h";
    echo "\033[?1003h";
    echo "\033[?1006h";
}

function disableMouseTracking(): void
{
    echo "\033[?1000l";
    echo "\033[?1003l";
    echo "\033[?1006l";
}

enum MouseEvent: int
{
    case LEFT = 0;
    case MIDDLE = 1;
    case RIGHT = 2;
    case RELEASE = 3;
    case DRAG_LEFT = 32;
    case DRAG_MIDDLE = 33;
    case DRAG_RIGHT = 34;
    case MOVE = 35;
    case SCROLL_UP = 64;
    case SCROLL_DOWN = 65;
}

function decodeMouseEvent(string $input): array
{
    if (preg_match('/\033\[<(\d+);(\d+);(\d+)([Mm])/', $input, $matches)) {
        $code = (int)$matches[1];
        $x = (int)$matches[2];
        $y = (int)$matches[3];
        $isRelease = $matches[4] === 'm';
        $event = MouseEvent::tryFrom($code);
        return [$event, $x, $y, $isRelease];
    }
    return [null, null, null, null];
}


system('stty -icanon -echo && clear');
enableMouseTracking();

$write = $except = [];
$textMode = false;

try {
    while (true) {
        $read = [STDIN];
        $needHandleInput = stream_select($read, $write, $except, 0, microseconds: 1_000) > 0;
        if ($needHandleInput) {
            if ($textMode) {
                // в текстовом режиме читаем и выводим последовательность символов
                $input = fread(STDIN, 1024);
                echo $input;
                continue;
            }

            // это минимум для событий мыши
            $input = fread(STDIN, 12);
            if ($input === false) {
                continue;
            }

            // Обработка команд клавиатуры
            $command = Command::tryFrom($input);
            if ($command !== null) {
                if ($command === Command::ESCAPE) {
                    break;
                }
                // echo "Команда $command->name\n";
                continue;
            }

            // Обработка событий мыши
            [$event, $x, $y, $isRelease] = decodeMouseEvent($input);
            if ($event !== null) {
                // echo implode(' ', [$event->name, $x, $y, $isRelease]) . "\n";
                continue;
            }

            // Обработка одиночных символов
            $input = mb_convert_encoding($input, 'UTF-8', 'UTF-8');
            if (mb_strlen($input, 'UTF-8') == 1) {
                echo "Введен символ: $input\n";
            }
        }
        // тут обработка фоновых вычислений
    }
} finally {
    disableMouseTracking();
    system('stty sane && clear');
}

// TODO: ob_start() и ob_end_flush() в случаях, когда есть много echo подряд ()
