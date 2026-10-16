<?php
if (!function_exists('round_decimal')) {
    function round_decimal($input, $precision)
    {
        $result = 0;
        if (!empty($input)) {
            $result = ceil($input * pow(10, $precision)) / pow(10, $precision);
        }
        return $result;
    }
}
if (!function_exists('search_input_trim')) {
    function search_input_trim($input)
    {
        $result = "";
        if (!empty($input)) {
            $result = htmlentities($input);
            if (strpos($input, '"') !== false) {
                $result = addslashes($input);
            }
        }
        return json_encode(str_replace("\\", "", $result));
    }
}
if (!function_exists('num2words')) {

    function num2word($num)
    {
        $ones = array(
            0 => "ZERO",
            1 => "ONE",
            2 => "TWO",
            3 => "THREE",
            4 => "FOUR",
            5 => "FIVE",
            6 => "SIX",
            7 => "SEVEN",
            8 => "EIGHT",
            9 => "NINE",
            10 => "TEN",
            11 => "ELEVEN",
            12 => "TWELVE",
            13 => "THIRTEEN",
            14 => "FOURTEEN",
            15 => "FIFTEEN",
            16 => "SIXTEEN",
            17 => "SEVENTEEN",
            18 => "EIGHTEEN",
            19 => "NINETEEN"
        );
        $tens = array(
            0 => "ZERO",
            1 => "TEN",
            2 => "TWENTY",
            3 => "THIRTY",
            4 => "FORTY",
            5 => "FIFTY",
            6 => "SIXTY",
            7 => "SEVENTY",
            8 => "EIGHTY",
            9 => "NINETY"
        );
        $hundreds = array(
            "HUNDRED",
            "THOUSAND",
            "MILLION",
            "BILLION",
            "TRILLION",
            "QUARDRILLION"
        );
        $num = number_format($num, 2, ".", ",");
        $num_arr = explode(".", $num);
        $wholenum = $num_arr[0];
        $decnum = $num_arr[1];
        $whole_arr = array_reverse(explode(",", $wholenum));
        $final_result = "";
        foreach ($whole_arr as $key => $i) {
            $temp_result = "";
            $expand = false;
            while (substr($i, 0, 1) == "0") {
                $i = substr($i, 1, 5);
            }
            if ($i < 20) {
                if ($i > 0) {
                    $temp_result .= $ones[$i];
                }
            } elseif ($i < 100) {
                if (substr($i, 0, 1) != "0") {
                    $temp_result .= $tens[substr($i, 0, 1)];
                }
                if (substr($i, 1, 1) != "0") {
                    $temp_result .= " " . $ones[substr($i, 1, 1)];
                }
            } else {
                if (substr($i, 0, 1) != "0") {
                    $temp_result .= $ones[substr($i, 0, 1)] . " " . $hundreds[0];
                }
                if (substr($i, 1, 1) != "0") {
                    if (substr($i, 1, 1) == 1) {
                        $expand = true;
                    } else {
                        $temp_result .= " " . $tens[substr($i, 1, 1)];
                    }
                }
                if ($expand === true) {
                    if (substr($i, 1, 2) != "00") {
                        $temp_result .= " " . $ones[substr($i, 1, 2)];
                    }
                } else {
                    if (substr($i, 2, 1) != "0") {
                        $temp_result .= " " . $ones[substr($i, 2, 1)];
                    }
                }
            }
            if ($key > 0 && $temp_result !== "") {
                $temp_result .= " " . $hundreds[$key] . " ";
            }
            $final_result = $temp_result . " " . $final_result;
        }
        if ($decnum > 0) {
            $temp_result .= " and ";
            if ($decnum < 20) {
                $temp_result .= $ones[$decnum];
            } elseif ($decnum < 100) {
                $temp_result .= $tens[substr($decnum, 0, 1)];
                $temp_result .= " " . $ones[substr($decnum, 1, 1)];
            }
        }
        return $final_result;
    }
}
if (!function_exists('alpha2num')) {

    function alpha2num($alphabet)
    {
        $numeric = 0;
        $multiplier = 1;
        for ($i = strlen($alphabet) - 1; $i >= 0; $i--) {
            $numeric += ((ord($alphabet[$i]) - 64) * $multiplier);
            if ($i == strlen($alphabet)) {
                $multiplier = 26;
            } else {
                $multiplier *= 26;
            }
        }
        return $numeric;
    }
}
if (!function_exists('num2alpha')) {

    function num2alpha($numeric)
    {
        $result = '';
        while ($numeric > 0) {
            $remainder = ($numeric - 1) % 26;
            $result = chr(65 + $remainder) . $result;
            $numeric = ($numeric - $remainder) / 26;
        }
        return $result;
    }
}
if (!function_exists('draw_select_input')) {

    function draw_select_input($name, $options_array, $value_key, $value_name, $options = [])
    {
        $html = "";
        if (isset($options["selected"])) {
            $selected_key = $options["selected"];
        } else {
            $selected_key = false;
        }
        if (isset($options["id"])) {
            $id = "id='" . $options["id"] . "'";
        } else {
            $id = "";
        }
        if (isset($options["onchange"])) {
            $onchange = "onchange='" . $options["onchange"] . "'";
        } else {
            $onchange = "";
        }
        if (!empty($name)) {
            $html .= "<select name='$name' $id $onchange class='gs-text-input'>";
            if (is_array($options_array)) {
                foreach ($options_array as $option) {
                    if ($option->$value_key == $selected_key && $selected_key !== false) {
                        $html .= "<option value='" . $option->$value_key . "' selected> " . $option->$value_name . " </option>";
                    } else {
                        $html .= "<option value='" . $option->$value_key . "'> " . $option->$value_name . " </option>";
                    }
                }
            }
            $html .= "</select>";
        }
        return $html;
    }
}
if (!function_exists('draw_checkbox')) {

    function draw_checkbox($name, $id, $label, $checked = false, $options = [])
    {
        $checklist = "";
        $value = "";
        $html = "";
        if (!empty($checked)) {
            $checked = "checked";
        } else {
            $checked = "";
        }
        if (isset($options["checklist"]) && !empty($options["checklist"])) {
            $checklist = " check-list";
        }
        if (isset($options["value"]) && !empty($options["value"])) {
            $value = " value='" . $options["value"] . "'";
        }
        if (!empty($name) && !empty($id) && !empty($label)) {
            $html .= "<div class='gs-checkbox$checklist'>";
            $html .= "<input type='checkbox' name='$name' id='$id' $checked$value>";
            $html .= "<label for='$id'>$label</label>";
            $html .= "</div>";
        }
        return $html;
    }
}
if (!function_exists('get_last_x_month_list')) {

    function get_last_x_month_list($count, $inclusive = true)
    {
        $result = [];
        for ($i = $count; $i > 0; $i--) {
            $object = new stdClass();
            $object->month = date("n", strtotime("-$i MONTH"));
            $object->year = date("Y", strtotime("-$i MONTH"));
            $result[] = $object;
        }
        if ($inclusive === true) {
            $object = new stdClass();
            $object->month = date("n");
            $object->year = date("Y");
            $result[] = $object;
        }

        return $result;
    }
}
if (!function_exists('format_time_amount')) {

    function format_time_amount($second, $shortened = false)
    {
        $result = "";
        $minute = floor($second / 60) % 60;
        $hour = floor($second / 60 / 60) % 24;
        $day = floor($second / 60 / 60 / 24);
        $second = floor($second) % 60;
        if (!empty($day)) {
            $result .= "$day ";
            if ($shortened) {
                $result .= "d ";
            } else {
                if ($day > 1) {
                    $result .= "days ";
                } else {
                    $result .= "day ";
                }
            }
        }
        if (!empty($hour)) {
            $result .= "$hour ";
            if ($shortened) {
                $result .= "h ";
            } else {
                if ($hour > 1) {
                    $result .= "hours ";
                } else {
                    $result .= "hour ";
                }
            }
        }
        if (!empty($minute)) {
            $result .= "$minute ";
            if ($shortened) {
                $result .= "m ";
            } else {
                $result .= "minutes ";
            }
        }
        if (!empty($second)) {
            $result .= "$second ";
            if ($shortened) {
                $result .= "s ";
            } else {
                if ($second > 1) {
                    $result .= "seconds ";
                } else {
                    $result .= "second ";
                }
            }
        }
        return trim($result);
    }
}
if (!function_exists('num2words_id')) {

    function num2words_id($nilai)
    {
        if ($nilai < 0) {
            $prefix = "minus ";
        } else {
            $prefix = "";
        }
        $nilai = abs($nilai);
        $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " " . $huruf[$nilai];
        } elseif ($nilai < 20) {
            $temp = num2words_id($nilai - 10) . " belas";
        } elseif ($nilai < 100) {
            $temp = num2words_id($nilai / 10) . " puluh" . num2words_id($nilai % 10);
        } elseif ($nilai < 200) {
            $temp = " seratus" . num2words_id($nilai - 100);
        } elseif ($nilai < 1000) {
            $temp = num2words_id($nilai / 100) . " ratus" . num2words_id($nilai % 100);
        } elseif ($nilai < 2000) {
            $temp = " seribu" . num2words_id($nilai - 1000);
        } elseif ($nilai < 1000000) {
            $temp = num2words_id($nilai / 1000) . " ribu" . num2words_id($nilai % 1000);
        } elseif ($nilai < 1000000000) {
            $temp = num2words_id($nilai / 1000000) . " juta" . num2words_id($nilai % 1000000);
        } elseif ($nilai < 1000000000000) {
            $temp = num2words_id($nilai / 1000000000) . " milyar" . num2words_id(fmod($nilai, 1000000000));
        } elseif ($nilai < 1000000000000000) {
            $temp = num2words_id($nilai / 1000000000000) . " trilyun" . num2words_id(fmod($nilai, 1000000000000));
        }

        return $prefix . $temp;
    }
}
if (!function_exists('decimal_format')) {

    function decimal_format($number, $precision = 2, $include_zero = false)
    {
        $result = "";
        if (is_numeric($number)) {
            $temp = number_format($number, $precision);
            $temp = explode(".", $temp);
            if (count($temp) >= 2) {
                $fraction = (int) $temp[1];
                if ($fraction > 0) {
                    $result = $temp[0] . "." . $temp[1];
                } else {
                    $result = $temp[0];
                }
            } else {
                $result = $temp[0];
            }
        } else {
            $result = "-";
        }

        if (!$include_zero) {
            $array_number = explode(".", $result);

            if (count($array_number) == 2) {
                $last_number = end($array_number);
                $start = -1;
                for ($i = 1; $i <= $precision; $i++) {
                    $number = substr($last_number, -1);
                    if ($number == 0) {
                        $last_number = substr_replace($last_number, "", $start);
                    }
                }

                $result = $array_number[0];
                if (!empty($last_number)) {
                    $result = $array_number[0] . "." . $last_number;
                }
            }
        }

        return $result;
    }
}
if (!function_exists('numtobase26')) {

    function numtobase26($number)
    {
        $b26 = "";
        $number += 1;
        do {
            $val = ($number % 26) ?: 26;
            $number = ($number - $val) / 26;
            $b26 = chr($val + 64) . ($b26 ?: '');
        } while (0 < $number);
        return $b26;
    }
}
if (!function_exists('draw_browser_warning')) {

    function draw_browser_warning()
    {
        $html = "";
        $html .= "<div class='gs-browser-warning-overlay' id='browserWarningOverlay'>";
        $html .= "<div class='gs-browser-warning-container'>";
        $html .= "<h1>Unsupported Browser</h1>";
        $html .= "<p>You are accessing Gemstone using unsupported browser, some features might not work properly if you continue with this browser.<br><br>Please use latest version of Chrome or Firefox for best experience while using Gemstone.</p>";
        $html .= "<div class='gs-button warning' onclick='Gemstone.dismissBrowserWarning()'>Continue With This Browser Anyway</div>";
        $html .= "</div>";
        $html .= "</div>";
        return $html;
    }
}
if (!function_exists('formatSizeUnits')) {

    function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }
}
if (!function_exists('compressImage')) {
    // Compress image
    function compressImage($source, $destination, $quality)
    {
        $info = getimagesize($source);
        if ($info['mime'] == 'image/jpeg') {
            $image = imagecreatefromjpeg($source);
        } elseif ($info['mime'] == 'image/gif') {
            $image = imagecreatefromgif($source);
        } elseif ($info['mime'] == 'image/png') {
            $image = imagecreatefrompng($source);
        }
        imagejpeg($image, $destination, $quality);
    }
}
if (!function_exists("normalize_number")) {
    function normalize_number($value)
    {
        if (floor($value) != $value) {
            return rtrim(number_format($value, 3), '0'); // Display with decimal places
        } else {
            return number_format($value, 0); // Display without decimal places
        }
    }
}
if (!function_exists("draw_input")) {
    function draw_input(string $type, string $field_name, array $config = []): string
    {
        $required = "";
        if (isset($config["required"]) && $config["required"] == TRUE) {
            $required = "required";
        }
        $default = "";
        if (isset($config["default"])) {
            $default = $config["default"];
        }
        $rows = 3;
        if (isset($config["rows"])) {
            $rows = $config["rows"];
        }
        if (isset($config["bounced"]) && $config["bounced"] != FALSE) {
            $bounced = $config["bounced"];
        }
        $value = htmlentities($default);
        if (isset($bounced) && isset($bounced->$field_name)) {
            $value = $bounced->$field_name;
        }
        $checked = "";
        if (!empty($value)) {
            $checked = "checked";
        }
        $placeholder_text = "Enter " . str_replace("_", " ", $field_name) . "..";
        if (isset($config["placeholder"])) {
            $placeholder_text = $config["placeholder"];
        }
        $tooltip = "";
        if (isset($config["tooltip"])) {
            $tooltip = " <b class='gs-info-tooltip gs-caption wide' gs-caption='" . $config["tooltip"] . "'>?</b>";
        }
        $method = "";
        if (isset($config["method"])) {
            $method = $config["method"];
        }
        $input_label = ucwords(str_replace("_", " ", $field_name));
        if (isset($config["label"])) {
            $input_label = $config["label"];
        }
        $input_id = "";
        if (isset($config["id"])) {
            $input_id = $config["id"];
        }
        $html = "";
        if ($type == "hidden") {
            $html .= "<input type='{$type}' name='{$field_name}' value='{$value}' id='$input_id'>";
        } else {
            $full = "";
            if (isset($config["full"]) && $config["full"] == TRUE) {
                $full = "full";
            }
            $html .= "<div class='gs-input-group $full'>";
            if (in_array($type, ["text", "number", "email", "password", "date", "datetime", "textarea", "gsnumber", "time", "color"])) {
                if($type == "datetime"){
                    $type = "datetime-local";
                }
                $html .= "<div class='gs-form-label'>" . $input_label . "{$tooltip}</div>";
                if ($type == "textarea") {
                    $html .= "<textarea name='{$field_name}' rows='$rows' id='$input_id' class='gs-text-input {$required}' placeholder='Enter " . str_replace("_", " ", $field_name) . "..'>{$value}</textarea>";
                } else {
                    if ($type == "gsnumber") {
                        $type = "text";
                        $required .= " number-format";
                    }
                    if ($type == "password") {
                        $value = "";
                    }
                    $html .= "<input type='{$type}' name='{$field_name}' class='gs-text-input {$required}' placeholder='$placeholder_text' value='{$value}' id='$input_id'>";
                }
            } else if ($type == "file") {
                $html .= "<div class='gs-form-label'>{$input_label}{$tooltip}</div>";
                $html .= "<input type='file' name='{$field_name}' id='{$field_name}Input' class='gs-file-input'>";
                $html .= "<label for='{$field_name}Input' class='gs-file-input-label'>Choose file</label>";
            } else if ($type == "checkbox") {
                $html .= "<div class='gs-form-label'>" . $input_label . "{$tooltip}</div>";
                $html .= "<div class='gs-checkbox'>";
                $html .= "<input type='checkbox' name='{$field_name}' id='{$field_name}ID' value='1' $checked>";
                $html .= "<label for='{$field_name}ID'>" . $input_label . "</label>";
                $html .= "</div>";
            } else if ($type == "select") {
                $html .= "<div class='gs-form-label'>" . $input_label . "{$tooltip}</div>";
                $html .= "<select name='{$field_name}' class='gs-text-input' id={$input_id}>";
                if (isset($config["dropdown"]) && is_array($config["dropdown"]) && isset($config["dropdown_label"])) {
                    if (isset($config["allow_empty"]) && $config["allow_empty"] == 1) {
                        $html .= "<option value='NULL'>-</option>";
                    }
                    foreach ($config["dropdown"] as $item) {
                        if ($item->$field_name == $value) {
                            $html .= "<option value='{$item->$field_name}' selected>{$item->{$config["dropdown_label"]} }</option>";
                        } else {
                            $html .= "<option value='{$item->$field_name}'>{$item->{$config["dropdown_label"]} }</option>";
                        }
                    }
                }
                $html .= "</select>";
            } else if ($type == "quicksearch") {
                $container_id = random_string("alpha", 10);
                $html .= "<div class='gs-form-label'>" . $input_label . "{$tooltip}</div>";
                $html .= "<div style='position: relative;'>";
                $html .= "<input type='text' name='{$field_name}' class='gs-text-input' id='{$input_id}' value='{$value}' onkeyup='Gemstone.quickSearch(\"{$input_id}\", \"{$method}\", \"{$container_id}\")' autocomplete='off' onblur='Gemstone.checkEmpty(\"{$field_name}\", \"{$container_id}\")' placeholder='{$placeholder_text}'>";
                $html .= "<div class='gs-qs-container' id='{$container_id}'></div>";
                $html .= "</div>";
            }
            $html .= "</div>";
        }
        return $html;
    }
}

if (!function_exists("resize_image")) {
    function resize_image($source_path, $dest_path, $max_width, $max_height, $quality, $output_format = null)
    {
        list($img_width, $img_height, $type) = getimagesize($source_path);
        $scale = min($max_width / $img_width, $max_height / $img_height);
        $new_width = ceil($scale * $img_width);
        $new_height = ceil($scale * $img_height);
        $new_canvas = imagecreatetruecolor($new_width, $new_height);
        $mime = strtolower(image_type_to_mime_type($type));

        switch ($mime) {
            case 'image/jpeg':
                $new_image = imagecreatefromjpeg($source_path);
                break;
            case 'image/png':
                $new_image = imagecreatefrompng($source_path);
                // Preserve transparency
                imagealphablending($new_canvas, false);
                imagesavealpha($new_canvas, true);
                break;
            case 'image/webp':
                $new_image = imagecreatefromwebp($source_path);
                // Preserve transparency
                imagealphablending($new_canvas, false);
                imagesavealpha($new_canvas, true);
                break;
            default:
                log_message("error", "Unsupported image type: " . $mime);
                return false;
        }

        // Default output format to original format if not specified
        if ($output_format === null) {
            $output_format = $mime;
        } else {
            $output_format = strtolower($output_format);
        }

        // Resize Image
        if (imagecopyresampled($new_canvas, $new_image, 0, 0, 0, 0, $new_width, $new_height, $img_width, $img_height)) {
            switch ($output_format) {
                case 'image/jpeg':
                case 'jpeg':
                case 'jpg':
                    imagejpeg($new_canvas, $dest_path, $quality);
                    break;
                case 'image/png':
                case 'png':
                    // Ensure quality is within the valid range
                    $quality = ($quality > 9) ? floor($quality / 10) : $quality;
                    imagepng($new_canvas, $dest_path, $quality);
                    break;
                case 'image/webp':
                case 'webp':
                    imagewebp($new_canvas, $dest_path, $quality);
                    break;
                default:
                    log_message("error", "Unsupported output format: " . $output_format);
                    imagedestroy($new_canvas);
                    imagedestroy($new_image);
                    return false;
            }
            imagedestroy($new_canvas);
            imagedestroy($new_image); // Destroy the original image resource
            return true;
        } else {
            log_message("error", "Failed to resample image: " . $source_path);
        }

        imagedestroy($new_image); // Ensure the original image is destroyed in case of failure
        return false;
    }
}

if (!function_exists("check_temp_file_uploaded")) {
    function check_temp_file_uploaded($field_name, $required = true)
    {
        $response = new GSResponse();
        $response->multiple = false;
        $response->valid = false;
        $response->uploaded = false;
        $response->uploaded_list = [];
        $response->error_list = [];
        if (isset($_FILES[$field_name])) {
            if (is_array($_FILES[$field_name]['name'])) {
                $response->is_multiple = true;
                $error_count = 0;
                $success_count = 0;
                $response->multiple = true;
                for ($i = 0; $i < count($_FILES[$field_name]['name']); $i++) {
                    if (is_uploaded_file($_FILES[$field_name]['tmp_name'][$i])) {
                        $success_count++;
                        $uploaded = new GSFile();
                        $uploaded->temp_name = $_FILES[$field_name]['tmp_name'][$i];
                        $uploaded->type = $_FILES[$field_name]['type'][$i];
                        $uploaded->name = $_FILES[$field_name]['name'][$i];
                        $uploaded->size = $_FILES[$field_name]['size'][$i];
                        $uploaded->extension = pathinfo($uploaded->name, PATHINFO_EXTENSION);
                        $uploaded->error = false;
                        $allowed = ALLOWED_UPLOAD_TYPE;
                        $allowed = explode("|", $allowed);
                        if (in_array($uploaded->extension, $allowed)) {
                            $response->uploaded_list[] = $uploaded;
                        } else {
                            $uploaded->error = "Extension not allowed.";
                            $response->error_list[] = $uploaded;
                        }
                    } else {
                        $error_count++;
                    }
                }
                if ($success_count > 0) {
                    $response->success = true;
                    $response->uploaded = true;
                    $response->valid = true;
                    if ($error_count > 0) {
                        $response->message = "Some files cannot be uploaded.";
                    }
                } else {
                    $response->message = "Upload failed.";
                }
            } else {
                if (!empty($_FILES[$field_name]['name'])) {
                    if (is_uploaded_file($_FILES[$field_name]['tmp_name'])) {
                        $response->success = true;
                        $response->uploaded = true;
                        $uploaded = new GSFile();
                        $uploaded->temp_name = $_FILES[$field_name]['tmp_name'];
                        $uploaded->type = $_FILES[$field_name]['type'];
                        $uploaded->name = $_FILES[$field_name]['name'];
                        $uploaded->size = $_FILES[$field_name]['size'];
                        $uploaded->extension = pathinfo($uploaded->name, PATHINFO_EXTENSION);
                        $allowed = ALLOWED_UPLOAD_TYPE;
                        $allowed = explode("|", $allowed);
                        if (in_array($uploaded->extension, $allowed)) {
                            $response->valid = true;
                            $response->uploaded_list[] = $uploaded;
                            $response->message = "File OK.";
                        } else {
                            $response->message = "Extension not allowed.";
                        }
                    } else {
                        $response->message = "File upload failed.";
                    }
                } else {
                    if ($required === false) {
                        $response->valid = true;
                    }
                    $response->message = "No file uploaded.";
                }
            }
        } else {
            $response->message = "Input field undefined.";
        }
        return $response;
    }
}

if (!function_exists("prepare_image_upload")) {
    function prepare_image_upload($custom_path = false, $new_file_name = false, $overwrite = false)
    {
        $config['allowed_types'] = ALLOWED_UPLOAD_TYPE;
        $config['max_size'] = MAX_UPLOAD_SIZE;
        $config['max_width'] = MAX_UPLOAD_WIDTH;
        $config['max_height'] = MAX_UPLOAD_HEIGHT;
        if ($custom_path !== false) {
            if (is_string($custom_path)) {
                $config['upload_path'] = $custom_path;
            }
        } else {
            $config['upload_path'] = './uploads/';
        }
        if ($new_file_name !== false && is_string($new_file_name)) {
            $config['file_name'] = $new_file_name;
        }
        if ($overwrite === true) {
            $config['overwrite'] = true;
        }
        $ci =& get_instance();
        $ci->load->library('upload', $config);
        $ci->upload->initialize($config);
    }
}

if (!function_exists("handle_image_upload")) {
    function handle_image_upload($config = [])
    {
        $config["input_name"] = isset($config["input_name"]) ? $config["input_name"] : "";
        $config["file_name"] = isset($config["file_name"]) ? $config["file_name"] : "image";
        $config["output_format"] = isset($config["output_format"]) ? $config["output_format"] : "webp";
        $config["folder_name"] = isset($config["folder_name"]) ? rtrim($config["folder_name"], '/') . '/' : "";
        $config["max_width"] = isset($config["max_width"]) ? $config["max_width"] : 200;
        $config["max_height"] = isset($config["max_height"]) ? $config["max_height"] : 200;
        $config["quality"] = isset($config["quality"]) ? $config["quality"] : 90;
        $config["required"] = isset($config["required"]) ? $config["required"] : false;
        $response = new GSResponse();
        $response->http_path = "";
        if ($config["required"] == false) {
            $response->success = true;
        }
        if (!empty($config["input_name"])) {
            $ci =& get_instance();
            $file_check = check_temp_file_uploaded($config["input_name"], false);
            if ($file_check->uploaded == true) {
                $file = $file_check->uploaded_list[0];
                $photo_path = base_url("uploads/temp/" . $config["file_name"] . "." . $file->extension);
                $temp_path = $_ENV['SERVER_PATH'] . "temp/" . $config["file_name"] . "." . $file->extension;
                $destination_path = $_ENV['SERVER_PATH'] . $config["folder_name"] . $config["file_name"] . "." . $config["output_format"];
                prepare_image_upload("./uploads/temp/", $config["file_name"], true);
                if ($ci->upload->do_upload($config["input_name"])) {
                    resize_image($photo_path, $destination_path, $config["max_width"], $config["max_height"], $config["quality"], $config["output_format"]);
                    $response->http_path = base_url("uploads/" . $config["folder_name"] . $config["file_name"] . "." . $config["output_format"]);
                    $response->success = true;
                    unlink($temp_path);
                } else {
                    $response->success = false;
                    $response->message = "Uploading image failed.";
                }
            } else {
                $response->message = "Uploading temp image failed. " . $file_check->message;
            }
        } else {
            $response->message = "No input fields provided.";
        }
        return $response;
    }
}