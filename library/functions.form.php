<?php

	function handleAttributes($attributes = '') {
		$newArray = array();
		if (strpos($attributes, '"') === false) return $newArray;
		$atts = trim_explode('" ', $attributes);
		foreach ($atts as $att) {
			$temp = trim_explode('="', $att, '"');
			$newArray[$temp[0]] = $temp[1];
		}
		return $newArray;
	} // handleAttributes

	function text($id, $defaultValue = '', $additionalAttributes = '') {
		return input('text', $id, $defaultValue, $additionalAttributes);
	} // text
	
	function password($id, $defaultValue = '', $additionalAttributes = '') {
		return input('password', $id, $defaultValue, $additionalAttributes);
	} // password
	
	function input($type = 'text', $id, $defaultValue = '', $additionalAttributes = '') {
		$table = get_table();
		$required = get_required_fields();
		$attributes = array("type" => $type, "id" => $id) + handleAttributes($additionalAttributes);
		
		if (!isset($attributes['name']))
			$attributes["name"] = $table . '[' . $id . ']';

		//if (get_field_value($attributes['name'])) {
		//	$attributes['value'] = str_replace('"', "&quot;", get_field_value($attributes['name']));
		//} else if (get_var($attributes['name'])) {
			$attributes['value'] = str_replace('"', "&quot;", value($attributes['name'], $defaultValue));
		//}
		
		if (in_array($attributes["name"], $required))
			$attributes['class'] = (isset($attributes['class'])) ? $attributes['class'] . " required" : $attributes['class'];
			
		$input = '<input';
		foreach ($attributes as $attributeName => $attributeValue) {
			$input .= ' ' . $attributeName . '="' . $attributeValue . '"';
		}
		$input .= '/>';
		return $input;
	} // input
	
	function submit($value = 'Submit', $additionalAttributes = '') {
		$submit = '<input type="submit" value="' . $value . '" ' . $additionalAttributes . '/>';
		return $submit;
	} // submit
	
	function textarea($id, $defaultValue = '', $additionalAttributes = '') {
		$table = get_table();
		$required = get_required_fields();

		$attributes = array("id" => $id) + handleAttributes($additionalAttributes);
		
		if (!isset($attributes['name']))
			$attributes["name"] = $table . '[' . $id . ']';
		if (!isset($attributes['cols']))
			$attributes['cols'] = "10";
		if (!isset($attributes['rows']))
			$attributes['rows'] = "4";
		if (in_array($attributes["name"], $required))
			$attributes['class'] = (isset($attributes['class'])) ? $attributes['class'] . " required" : $attributes['class'];
		
		$ta = '<textarea';
		foreach ($attributes as $attributeName => $attributeValue) {
			$ta .= ' ' . $attributeName . '="' . $attributeValue . '"';
		}
		$ta .= '>';
		//$ta .= ((get_field_value($attributes['name'])) ? get_field_value($attributes['name']) : ((get_var($attributes['name'])) ? get_var($attributes['name']) : $value));
		$ta .= value($attributes['id']);
		$ta .= '</textarea>';
		return $ta;
		
	} // textarea
	
	function checkbox($id, $additionalAttributes = '') {
		$table = get_table();
		$required = get_required_fields();
		$attributes = array("id" => $id) + handleAttributes($additionalAttributes);
		
		if (!isset($attributes['name']))
			$attributes["name"] = $table . '[' . $id . ']';
		if (!isset($attributes['value']))
			$attributes['value'] = 'on';
		
		if (get_field_value($attributes['name'])) {
			$attributes['checked'] = "checked";
		} else if (get_var($attributes['name'])) {
			$attributes['checked'] = "checked";
		}
		if (in_array($attributes["name"], $required))
			$attributes['class'] = (isset($attributes['class'])) ? $attributes['class'] . " required" : $attributes['class'];
		
		$cb = '<input type="checkbox"';
		foreach ($attributes as $attributeName => $attributeValue) {
			$cb .= ' ' . $attributeName . '="' . $attributeValue . '"';
		}
		$cb .= '/>';
		return $cb;
	} // checkbox
	
	function value($which, $defaultValue = '') {
		if (get_field_value($which)) {
			return str_replace('"', '&quot;', get_field_value($which));
		} else if (is_string($defaultValue) || is_numeric($defaultValue)) {
			return str_replace('"', '&quot;', $defaultValue);
		}
		return '';
	} // values
	
	function checked($which, $value = false, $checkedOnDefault = false) {
		if (get_field_value($which)) {
			if (!is_string($value) || (get_field_value($which) == $value)) {
				return ' checked="checked"';
			} else {
				return '';
			}
		} else if ($checkedOnDefault) {
			if (count(get_field_values()) > 1) {
				return '';
			} else {
				return ' checked="checked"';
			}
		}
		return '';
	} // checked
	
	function is_required($field) {
		$required_fields = get_required_fields();
		if ($required_fields && is_array($required_fields) && (isset($required_fields[$field]) || in_array($field, $required_fields))) {
			return true;
		}
		return false;
	} // is_required
	
	function build_options($options, $selected = false) {
		$ops = '';
		foreach($options as $option) {
			$ops .= ($option['id'] == $selected) ? '<option value="' . $option['id'] . '" selected="selected">' . valid($option['name']) . '</option>' : '<option value="' . $option['id'] . '">' . valid($option['name']) . '</option>';
		}
		return $ops;
	} // build_options

?>