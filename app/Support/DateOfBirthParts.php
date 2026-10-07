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
 * All three blank means no Date of Birth; once any part is chosen,
 * all three are required - in particular, a Month and Year without a
 * Day is refused.
 */
class DateOfBirthParts
{
    public const FIELDS = ['dob_day', 'dob_month', 'dob_year'];

    public const OLDEST_YEAR = 1900;

    /**
     * Sets date_of_birth on the request from the three parts - "Y-m-d"
     * when all three are given, null when they aren't (the part rules
     * then report what's missing). A request with none of the parts
     * at all (not from the lead form) is left alone.
     */
    public static function mergeInto(Request $request): void
    {
        if (!$request->hasAny(self::FIELDS)) {
            return;
        }

        [$day, $month, $year] = array_map(
            fn (string $field) => trim((string) $request->input($field)),
            self::FIELDS
        );

        $complete = ctype_digit($day) && ctype_digit($month) && ctype_digit($year);

        $request->merge([
            'date_of_birth' => $complete
                ? sprintf('%04d-%02d-%02d', $year, $month, $day)
                : null,
        ]);
    }

    public static function rules(): array
    {
        return [

            // A Month and Year without a Day is the case Companies
            // House leaves behind - the Day must then be picked.
            'dob_day' => [
                'nullable',
                'integer',
                'between:1,31',
                'required_with:dob_month,dob_year',
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

            'dob_day.required_with' =>
                'Please select the Day for the Date of Birth.',

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
