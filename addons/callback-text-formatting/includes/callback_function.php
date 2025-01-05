<?php
function callback_text_formatting($text, $format = ''){
	if( empty($format) ) {
		return $text;
	}
	switch ($format) {
		case 'capitalize_each_word':
			return ucwords(strtolower($text));
		case 'capitalize_first_letter':
			return ucfirst(strtolower($text));
		case 'uppercase':
			return strtoupper($text);
		case 'lowercase':
			return strtolower($text);
		case 'snake_case':
			return strtolower(str_replace(' ', '_', $text));
		case 'kebab_case':
			return strtolower(str_replace(' ', '-', $text));
		case 'camel_case':
			$text = ucwords(strtolower($text));
			return lcfirst(str_replace(' ', '', $text));
		case 'pascal_case':
			return str_replace(' ', '', ucwords(strtolower($text)));
		case 'title_case':
			$minorWords = ['a', 'an', 'the', 'and', 'but', 'or', 'for', 'nor', 'on', 'at', 'to', 'by'];
			$words = explode(' ', strtolower($text));
			foreach ($words as $key => $word) {
				if ($key == 0 || !in_array($word, $minorWords)) {
					$words[$key] = ucfirst($word);
				}
			}
			return implode(' ', $words);
		case 'toggle_case':
			$result = '';
			for ($i = 0; $i < strlen($text); $i++) {
				$result .= ctype_lower($text[$i]) ? strtoupper($text[$i]) : strtolower($text[$i]);
			}
			return $result;
		case 'sentence_case':
			return ucfirst(strtolower($text));
		case 'reverse_case':
			$result = '';
			for ($i = 0; $i < strlen($text); $i++) {
				$result .= ctype_lower($text[$i]) ? strtoupper($text[$i]) : strtolower($text[$i]);
			}
			return $result;
		case 'trim_whitespace':
			return trim($text);
		case 'remove_special_characters':
			return preg_replace('/[^a-zA-Z0-9 ]/', '', $text);
		case 'add_padding':
			return str_pad($text, 3, '0', STR_PAD_LEFT);
		default:
			return $text; // Return the original text if no format matches
	}
}