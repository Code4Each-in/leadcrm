<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * Add Lead / Edit Lead take the Date of Birth as three dropdowns -
 * Day, Month, Year - rather than a date picker, so a partial date
 * from Companies House (month and year only) can be pre-selected
 * while the Day is left for the user to pick.
 *
 * The three parts are combined into the lead's date_of_birth before
 * validation (see mergeInto()), so LeadValidationRules' own
 * date_of_birth rules - a real date, not in the future - still apply.
 * All three blank means no Date of Birth. Month and Year are needed
 * together; the Day may be left as "Day" when it isn't known. A DATE
 * column can't hold a "00" day, so an unknown day is stored as the
 * 1st of the month with leads.dob_day_unknown set (see attributes() and
 * Lead::dateOfBirthLabel(), which shows it as "- May 1995").
 */
class DateOfBirthParts
{
    public const FIELDS = ['dob_day', 'dob_month', 'dob_year'];

    public const OLDEST_YEAR = 1900;

    /**
     * Sets date_of_birth on the request from the three parts - "Y-m-d"
     * when Month and Year are given (the 1st of the month when the Day
     * is left blank, or given as 0 / "00" - unknown), null otherwise
     * (the part rules then report what's missing). A request with none
     * of the parts at all (not from the lead form) is left alone.
     */
    public static function mergeInto(Request $request): void
    {
        if (!$request->hasAny(self::FIELDS)) {
            return;
        }

        [$day, $month, $year] = self::parts($request);

        $request->merge([
            'date_of_birth' => ctype_digit($month) && ctype_digit($year) && ($day === '' || ctype_digit($day))
                ? sprintf('%04d-%02d-%02d', $year, $month, self::dayUnknown($day) ? 1 : (int) $day)
                : null,
        ]);
    }

    /**
     * The lead attributes the parts set besides date_of_birth - whether
     * its day is unknown. Empty for a request with none of the parts
     * (not from the lead form), so the flag is then left as it was.
     *
     * @return array{dob_day_unknown?: bool}
     */
    public static function attributes(Request $request): array
    {
        if (!$request->hasAny(self::FIELDS)) {
            return [];
        }

        [$day, $month, $year] = self::parts($request);

        return ['dob_day_unknown' => self::dayUnknown($day) && $month !== '' && $year !== ''];
    }

    /**
     * @return array{0: string, 1: string, 2: string} day, month, year
     */
    private static function parts(Request $request): array
    {
        return array_map(fn (string $field) => trim((string) $request->input($field)), self::FIELDS);
    }

    private static function dayUnknown(string $day): bool
    {
        return $day === '' || (ctype_digit($day) && (int) $day === 0);
    }

    public static function rules(): array
    {
        return [

            // Optional - a Month and Year without a Day (the case
            // Companies House leaves behind) is saved with the day
            // unknown. 0 / "00" mean the same.
            'dob_day' => [
                'nullable',
                'numeric',
                'digits_between:1,2',
                'between:0,31',
            ],

            'dob_month' => [
                'nullable',
                'integer',
                'between:1,12',
                'required_with:dob_day,dob_year',
            ],

            'dob_year' => [
                'nullable',
                'integer',
                'between:' . self::OLDEST_YEAR . ',' . date('Y'),
                'required_with:dob_day,dob_month',
            ],
        ];
    }

    public static function messages(): array
    {
        return [

            'dob_month.required_with' =>
                'Please select the Month for the Date of Birth.',

            'dob_year.required_with' =>
                'Please select the Year for the Date of Birth.',

            'dob_day.*' =>
                'Please select a valid Day for the Date of Birth.',

            'dob_month.*' =>
                'Please select a valid Month for the Date of Birth.',

            'dob_year.*' =>
                'Please select a valid Year for the Date of Birth.',

            // e.g. 31 February - the parts are each fine, the date isn't.
            'date_of_birth.date' =>
                'Please select a valid Date of Birth.',
        ];
    }
}
