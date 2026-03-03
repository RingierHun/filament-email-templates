<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Traits;

use NoteBrainsLab\FilamentEmailTemplates\Models\EmailTemplate;
use Illuminate\Support\Facades\Blade;

trait HasEmailTemplate
{
    /**
     * The unique key identifying which template to use.
     */
    public string $templateKey;

    /**
     * Variables to replace {{placeholders}} in the template.
     */
    public array $templateVariables = [];

    /**
     * Fetch the template by key and return the fully merged email.
     * Prioritizes body_html (Unlayer Export) for pixel-perfect 1:1 matching.
     */
    public function build()
    {
        try {
            $template = EmailTemplate::where('key', $this->templateKey)->first();

            if (!$template) {
                return $this->subject('Template Not Found')
                    ->html('<p>Email template configuration is missing for key: <strong>' . e($this->templateKey) . '</strong></p>');
            }

            // 1. Render Subject using Blade
            $rawSubject = $this->prepareBladeTemplate($template->subject ?? 'No Subject');
            $subject = Blade::render($rawSubject, $this->templateVariables ?? []);

            // 2. Render Body — Use body_html (The Unlayer Export)
            $html = $template->body_html ?? '';

            if (empty($html)) {
                $html = '<p>The email content is empty for template: ' . e($this->templateKey) . '</p>';
            }

            // 3. Clean up HTML (Protect against style stripping)
            $html = preg_replace('/([\w-]+):\s*;\s*/', '', $html);
            
            // 4. Inject Variables into the HTML
            $html = $this->injectTemplateVariables($html);

            // 5. Final sanity check: ensure full HTML document
            if (!str_contains(strtolower($html), '<html')) {
                $html = '<!DOCTYPE html><html><body style="margin:0;padding:0;">' . $html . '</body></html>';
            }

            return $this->subject($subject)->html($html);

        } catch (\Throwable $e) {
            return $this->subject('Error: ' . ($this->templateKey ?? 'Unknown Template'))
                ->html('<h3>Mail Template Rendering Error</h3><p>' . e($e->getMessage()) . '</p>');
        }
    }

    /**
     * Safely inject variables into the HTML string.
     */
    protected function injectTemplateVariables(string $html): string
    {
        foreach ($this->templateVariables as $key => $value) {
            if (!is_scalar($value) && !(is_object($value) && method_exists($value, '__toString'))) {
                continue;
            }

            $escapedValue = htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

            // Robust pattern to match standard and URL-encoded placeholders
            $pattern = '/\{\{\s*((?:<[^>]+>|&nbsp;|\s)*)' . preg_quote($key, '/') . '((?:<[^>]+>|&nbsp;|\s)*)\s*\}\}/i';
            $html = preg_replace($pattern, $escapedValue, $html);

            $urlPattern = '/%7B%7B((?:%[^%]+|&nbsp;|\s)*)' . preg_quote($key, '/') . '((?:%[^%]+|&nbsp;|\s)*)%7D%7D/i';
            $html = preg_replace($urlPattern, $escapedValue, $html);
        }

        return $html;
    }

    /**
     * Convert {{var}} → {{$var}} for Blade subject rendering.
     */
    protected function prepareBladeTemplate(string $content): string
    {
        return preg_replace('/\{\{\s*(?!\$|@|!)([\w\.]+)\s*\}\}/', '{{ \$$1 }}', $content);
    }
}
