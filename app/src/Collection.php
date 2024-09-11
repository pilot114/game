<?php

namespace Game;

/**
 * @template T
 */
class Collection
{
    /**
     * @param T[] $items
     */
    public function __construct(
        private array $items = []
    ) {
    }

    /**
     * @param T $item
     */
    public function add($item): void
    {
        $this->items[] = $item;
    }

    /**
     * @param T $item
     */
    public function remove($item): void
    {
        $index = array_search($item, $this->items);
        if ($index !== false) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }
    }

    /**
     * @return T[]
     */
    public function getAll(): array
    {
        return $this->items;
    }

    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @return ?T
     */
    public function getBy(string $name, mixed $value): mixed
    {
        foreach ($this->items as $item) {
            if ($item->$name === $value) {
                return $item;
            }
        }
        return null;
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    /**
     * @return ?T
     */
    public function getRandom(): ?object
    {
        if ($this->isEmpty()) {
            return null;
        }
        return $this->items[array_rand($this->items)];
    }

    public function __toString(): string
    {
        return implode(", ", $this->items);
    }
}