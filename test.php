<?php
const MAX_NUMBER_COUNT = 3;
const ORDER_OF_A = 97;
const ORDER_OF_Z = 122;
const ORDER_OF_S = 115;

// converts old mobile keyboard codes for letters to normal string ("2,22,222") -> "abc"
function convertToString(string $codes)
{
    $result = "";
    foreach (explode(',', $codes) as $code) {
        if (!is_numeric($code)) {
            throw new Exception("Invalid code: $code");
        }
        if ($code === "0") {
            $result .= " ";
        } else if ($code === "7777") {
            $result .= "s";
        } else if ($code === "9999") {
            $result .= "z";
        } else {
            $num = $code[0];
            $num_count = strlen($code);
            $char_order = ($num - 1) * $num_count + (MAX_NUMBER_COUNT - $num_count) * ($num - 2) - 1;
            if ($char_order + ORDER_OF_A >= ORDER_OF_S) $char_order++; // the order goes astray after s => '7777'
            $result .= chr($char_order + ORDER_OF_A);
        }
    }
    return $result;
}

// converts string to old mobile keyboard codes for letters ("abc") -> "2,22,222"
function convertToNumeric(string $str)
{
    $str = strtolower($str);
    $last_index = strlen($str) - 1;
    $result = "";
    foreach (str_split($str) as $key => $char) {
        if ((ord($char) > ORDER_OF_Z || ord($char) < ORDER_OF_A) 
            && ord($char) != ord(" ")) {
            throw new Exception("Please, use only latin letters");
        }
        $char_order = ord($char) - ORDER_OF_A;
        if ($char === " ") {
            $result .= "0";
        } else if ($char === "s") {
            $result .= "7777";
        } else if ($char === "z") {
            $result .= "9999";
        } else {
            if (ord($char) > ORDER_OF_S) $char_order--; // the order goes astray after s => '7777'
            $keypad_num = ceil(($char_order + 1) / MAX_NUMBER_COUNT) + 1;
            $keypad_num_count = $char_order % MAX_NUMBER_COUNT + 1;
            $result .= str_repeat($keypad_num, $keypad_num_count);
        }
        $result .= ($last_index != $key) ? "," : "";
    }
    return $result;
}

try {
    echo convertToNumeric("Ela ni23e ma kota") . "\n"; // 33,555,2,0,66,444,33,0,6,2,0,55,666,8,2
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

try {
    echo convertToString("5,2,22,55yy,33,222,9999,66,444,55") . "\n"; // jablecznik
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo convertToString(convertToNumeric("test z x y t")) . "\n";

?>