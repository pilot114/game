### TODO

- сделать генератор на world.yaml
- развивать и улучшать различные способности
- контейнеры
- применение инвентаря
- улучшение системы боя

### Карта

https://penfox.ru/blog/kak-bystro-generirovat-fentezi-karty-5-luchshix-instrumentov/

### releases

image php:8.3-cli-alpine

### fix phptui

        foreach ($image->getPixelIterator() as $y => $pixels) {
            foreach ($pixels as $x => $pixel) {
                if ($y % 4 !== 0 || $x % 4 !== 0) {
                    continue;
                }
    
minimal term resolution: 120x30 (2/1)
264x75