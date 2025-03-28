<?php

namespace Tests\Unit;

class SetClass
{
    public function __construct(private array $items = [])
    {}
    public function isEmpty(): bool {
        return $this->count() === 0;
    }

    public function add(string $item): void {
        if (!in_array($item, $this->items)) {
            $this->items[] = $item;
        }
    }

    public function count(): int {
        return count($this->items);
    }

    public function remove(string $item): void {
        $index = array_search($item, $this->items);
        if ($index !== false) {
            // Deletes the item from the array
            unset($this->items[$index]);
            // Re-index the array to avoid gaps in the keys
            $this->items = array_values($this->items);
        }
    }

    public function getByIndex(int $index): ?string {
        return $this->items[$index] ?? null;
    }
}
