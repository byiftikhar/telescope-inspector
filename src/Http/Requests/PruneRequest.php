<?php

namespace ByIftikhar\TelescopeInspector\Http\Requests;

use ByIftikhar\TelescopeInspector\Pruning\PruneFilter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class PruneRequest extends FormRequest
{
    private const DATE_FORMAT = 'Y-m-d H:i';

    /**
     * Telescope's own middleware already decided this user may use Telescope.
     * A project can narrow pruning further by defining a "pruneTelescope" gate.
     */
    public function authorize(): bool
    {
        return ! Gate::has('pruneTelescope') || Gate::allows('pruneTelescope');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'from' => ['nullable', 'date_format:'.self::DATE_FORMAT, 'before:to'],
            'to' => ['required', 'date_format:'.self::DATE_FORMAT],
            'types' => ['nullable', 'array'],
            'types.*' => ['string', Rule::in(PruneFilter::knownTypes())],
            'keep_exceptions' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'from.before' => 'The "From" date must be before the "To" date.',
            'to.required' => 'Pick the "To" date.',
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if (config('telescope.driver') !== 'database') {
                    $validator->errors()->add('to', 'Pruning by date needs Telescope\'s "database" storage driver.');
                }
            },
        ];
    }

    public function filter(): PruneFilter
    {
        return new PruneFilter(
            from: $this->date('from', self::DATE_FORMAT, config('app.timezone'))?->toImmutable(),
            to: $this->date('to', self::DATE_FORMAT, config('app.timezone'))->toImmutable(),
            types: array_values((array) $this->input('types', [])),
            keepExceptions: $this->boolean('keep_exceptions'),
        );
    }
}
