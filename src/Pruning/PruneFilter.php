<?php

namespace ByIftikhar\TelescopeInspector\Pruning;

use Carbon\CarbonImmutable;
use Laravel\Telescope\EntryType;
use ReflectionClass;

/**
 * Which Telescope entries a prune covers. Times are the server's wall clock,
 * the same way Telescope stores created_at.
 */
final readonly class PruneFilter
{
    /**
     * @param  list<string>  $types  Telescope entry types (EntryType values); empty means every type.
     */
    public function __construct(
        public ?CarbonImmutable $from,
        public CarbonImmutable $to,
        public array $types = [],
        public bool $keepExceptions = false,
    ) {}

    /**
     * Every entry type Telescope can record.
     *
     * @return list<string>
     */
    public static function knownTypes(): array
    {
        return array_values((new ReflectionClass(EntryType::class))->getConstants());
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'from' => $this->from?->format('Y-m-d H:i'),
            'to' => $this->to->format('Y-m-d H:i'),
            'types' => $this->types,
            'keep_exceptions' => $this->keepExceptions,
        ];
    }
}
