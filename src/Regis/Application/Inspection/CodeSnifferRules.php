<?php

declare(strict_types=1);

namespace Regis\Application\Inspection;

use Regis\Domain\Inspection\Rule;

class CodeSnifferRules
{
    /** @var Rule[] */
    private $rules;

    public function __construct()
    {
        $this->rules = [
            'upper_case_constant_name' => new Rule('upper_case_constant_name', 'Generic.NamingConventions.UpperCaseConstantName', 'Ensures that constant names are all uppercase.'),
            'disallow_short_open_tags' => new Rule('disallow_short_open_tags', 'Generic.PHP.DisallowShortOpenTag.EchoFound', 'Makes sure that shorthand PHP open tags are not used.'),
            'line_length' => new Rule('line_length', 'Generic.Files.LineLength', 'Checks all lines in the file, and throws warnings if they are over 80 characters in length and errors if they are over 100.'),
            'disallow_tab_indent' => new Rule('disallow_tab_indent', 'Generic.WhiteSpace.DisallowTabIndent', 'Throws errors if tabs are used for indentation.'),
            'disallow_space_indent' => new Rule('disallow_space_indent', 'Generic.WhiteSpace.DisallowSpaceIndent', 'Throws errors if spaces are used for indentation.'),
            'scope_indent' => new Rule('disallow_space_indent', 'Generic.WhiteSpace.ScopeIndent', 'Checks that control structures are structured correctly, and their content is indented correctly. This sniff will throw errors if tabs are used for indentation rather than spaces.'),
        ];
    }

    public function get(string $ruleName): Rule
    {
        return $this->rules[$ruleName];
    }

    public function all(): array
    {
        return $this->rules;
    }
}