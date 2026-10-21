<?php

namespace App\Rules;

use App\Support\LeadValidationRules;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * AU Savers (Single Site): at least one of MPAN / MPRN / SPID must be
 * given. On the Add/Edit form a meter only counts when its checkbox is
 * ticked (meter_toggles marks a form submission); a CSV import row has
 * no checkboxes, so there a filled value counts.
 *
 * Implicit, so it runs although the "meters" key it's attached to is
 * never itself submitted.
 */
class AtLeastOneMeter implements ValidationRule, DataAwareRule
{
    public bool $implicit = true;

    private array $data = [];

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        foreach (LeadValidationRules::METERS as $meter) {
            if (LeadValidationRules::meterEnabled($this->data, $meter) && filled($this->data[$meter] ?? null)) {
                return;
            }
        }

        $fail(LeadValidationRules::METER_REQUIRED_MESSAGE);
    }
}
