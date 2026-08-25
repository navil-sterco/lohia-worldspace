<?php

namespace App\Rules;

use Closure;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class Recaptcha implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            $fail('Please confirm you are not a robot.');
            return;
        }

        $response = Http::withOptions([
            'verify' => app()->environment('local') ? false : true,
        ])->asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret_key'),
            'response' => $value,
            'remoteip' => request()->ip(),
        ]);

        $result = $response->json();
        $score = $result['score'] ?? 0;
        $action = $result['action'] ?? null;

        if (!($result['success'] ?? false)) {
            $fail('reCAPTCHA verification failed. Please try again.');
            return;
        }

        if ($action !== 'contact_buy_property') {
            $fail('reCAPTCHA verification failed. Please try again.');
            return;
        }

        if ($score < config('services.recaptcha.min_score', 0.5)) {
            $fail('We were unable to verify your submission. Please try again or contact us directly.');
        }
    }
}
