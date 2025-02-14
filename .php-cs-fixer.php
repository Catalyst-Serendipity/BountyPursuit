<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
	->in(__DIR__ . '/src');

return (new PhpCsFixer\Config())
	->setRiskyAllowed(true)
	->setRules([
		'align_multiline_comment' => [
			'comment_type' => 'phpdocs_only'
		],
		'array_indentation' => true,
		'array_syntax' => [
			'syntax' => 'short'
		],
		'binary_operator_spaces' => [
			'default' => 'single_space'
		],
		'blank_line_after_namespace' => true,
		'blank_line_after_opening_tag' => true,
		'blank_line_before_statement' => [
			'statements' => [
				'declare'
			]
		],
		'cast_spaces' => [
			'space' => 'single'
		],
		'concat_space' => [
			'spacing' => 'one'
		],
		'declare_strict_types' => true,
		'elseif' => true,
		'fully_qualified_strict_types' => true,
		'global_namespace_import' => [
			'import_constants' => true,
			'import_functions' => true,
			'import_classes' => null,
		],
		'header_comment' => [
			'comment_type' => 'comment',
			'header' => <<<BODY
Copyright (c) 2024 - present nicholass003
       _      _           _                ___   ___ ____
      (_)    | |         | |              / _ \ / _ \___ \
 _ __  _  ___| |__   ___ | | __ _ ___ ___| | | | | | |__) |
| '_ \| |/ __| '_ \ / _ \| |/ _` / __/ __| | | | | | |__ <
| | | | | (__| | | | (_) | | (_| \__ \__ \ |_| | |_| |__) |
|_| |_|_|\___|_| |_|\___/|_|\__,_|___/___/\___/ \___/____/

The use of this software is granted only to individuals or organizations who have obtained
a valid license from the copyright owner. The license is non-transferable and is limited to
personal, non-commercial use.

Any form of distribution, reproduction, or use for commercial purposes, whether directly or
indirectly, is strictly prohibited without the express written consent of the copyright owner.

Modification, decompilation, or reverse engineering of the software is not permitted.

By using the software, you agree to abide by the terms of this license.

The software is provided "as is," without warranty of any kind, express or implied,
including but not limited to the warranties of merchantability, fitness for a particular
purpose, and noninfringement. In no event shall the authors or copyright holders be
liable for any claim, damages, or other liability, whether in an action of contract,
tort, or otherwise, arising from, out of, or in connection with the software or the use
or other dealings in the software.

For inquiries regarding licensing options, please contact the copyright owner.

@author nicholass033

Developed by: Catalyst Serendipity


BODY,
			'location' => 'after_open'
		],
		'indentation_type' => true,
		'logical_operators' => true,
		'native_constant_invocation' => [
			'scope' => 'namespaced'
		],
		'native_function_invocation' => [
			'scope' => 'namespaced',
			'include' => ['@all'],
		],
		'new_with_braces' => [
			'named_class' => true,
			'anonymous_class' => false,
		],
		'no_closing_tag' => true,
		'no_empty_phpdoc' => false,
		'no_extra_blank_lines' => true,
		'no_superfluous_phpdoc_tags' => false,
		'no_trailing_whitespace' => true,
		'no_trailing_whitespace_in_comment' => true,
		'no_whitespace_in_blank_line' => true,
		'no_unused_imports' => true,
		'ordered_imports' => [
			'imports_order' => [
				'class',
				'function',
				'const',
			],
			'sort_algorithm' => 'alpha'
		],
		'phpdoc_align' => [
			'align' => 'vertical',
			'tags' => [
				'param',
			]
		],
		'phpdoc_line_span' => [
			'property' => 'single',
			'method' => null,
			'const' => null
		],
		'phpdoc_trim' => false,
		'phpdoc_trim_consecutive_blank_line_separation' => false,
		'return_type_declaration' => [
			'space_before' => 'one'
		],
		'single_blank_line_at_eof' => true,
		'single_import_per_statement' => true,
		'strict_param' => true,
		'unary_operator_spaces' => true,
	])
	->setFinder($finder)
	->setIndent("\t")
	->setLineEnding("\n");
