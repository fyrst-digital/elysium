<?php

declare(strict_types=1);

namespace Blur\BlurElysiumSlider\Service\ImportExport;

class ImportResult
{
    public function __construct(
        private readonly int $imported = 0,
        private readonly array $errors = []
    ) {
    }

    public function getImported(): int
    {
        return $this->imported;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function addImported(): self
    {
        return new self($this->imported + 1, $this->errors);
    }

    public function addError(string $error): self
    {
        return new self($this->imported, array_merge($this->errors, [$error]));
    }
}
