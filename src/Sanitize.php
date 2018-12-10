<?php

namespace Dharmvijay\laravelSanitiseAndValidationTransformer;

use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Dharmvijay\laravelSanitiseAndValidationTransformer\APIResponse;
use Exception;

trait SanitizeRequest
{
    /**
     * Sanitization on inputs
     *
     * @param array $inputs
     *
     * @param $filters
     *
     * @return array
     */
    public function sanitize(array $inputs, $filters = [])
    {

        foreach ($inputs as $i => $item) {

            //Sanitize trim
            if(!is_array($item) && !empty($filters['trim']) && in_array($i,$filters['trim'])){
                $inputs[$i] = trim($inputs[$i]);
            }
            //Sanitize integers
            if(!empty($filters['integers']) && in_array($i,$filters['integers'])){
                $inputs[$i] = filter_var($inputs[$i], FILTER_SANITIZE_NUMBER_INT);
            }

            //Sanitize float
            if(!empty($filters['float']) && in_array($i,$filters['float'])){
                $inputs[$i] = filter_var($inputs[$i], FILTER_SANITIZE_NUMBER_FLOAT);
            }

            //Sanitize strings
            if(!empty($filters['strings']) && in_array($i,$filters['strings'])){
                $inputs[$i] = filter_var($inputs[$i], FILTER_SANITIZE_STRING);
            }

            //Sanitize strings_only
            //This will remove the tab and the line break
            //This will remove the é.
            if(!empty($filters['strings_only']) && in_array($i,$filters['strings_only'])){
                $inputs[$i] = filter_var($inputs[$i], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_BACKTICK);
            }

            //Sanitize emails
            if(!empty($filters['emails']) && in_array($i,$filters['emails'])){
                $inputs[$i] = filter_var($inputs[$i], FILTER_SANITIZE_EMAIL);
            }

            //Sanitize url
            if(!empty($filters['url']) && in_array($i,$filters['url'])){
                $inputs[$i] = filter_var($inputs[$i], FILTER_SANITIZE_URL);
            }

            //Sanitize encoded
            if(!empty($filters['encoded']) && in_array($i,$filters['encoded'])){
                $inputs[$i] = filter_var($inputs[$i], FILTER_SANITIZE_ENCODED);
            }

            //Sanitize Alnum
            //Strips non-alphanumeric characters from the value.
            if(!empty($filters['alnum']) && in_array($i,$filters['alnum'])){
                $inputs[$i] = preg_replace('/[^\p{L}\p{Nd}]/u', '',$inputs[$i]);
            }

            //Sanitize Word
            //Strips non-alphanumeric characters from the value.
            if(!empty($filters['word']) && in_array($i,$filters['word'])){
                $inputs[$i] = preg_replace('/[^\p{L}\p{Nd}_]/u', '',$inputs[$i]);
            }

            //Sanitize Alpha
            //Strips non-alphabetic characters from the value.
            if(!empty($filters['alpha']) && in_array($i,$filters['alpha'])){
                $inputs[$i] = preg_replace('/[^\p{L}]/u', '',$inputs[$i]);
            }

            //Sanitize booleans
            if(!empty($filters['booleans']) && in_array($i,$filters['booleans'])){
                $inputs[$i] = filter_var($inputs[$i], FILTER_VALIDATE_BOOLEAN);
            }

            //Sanitize date time
            if(!empty($filters['datetime']) && in_array($i,$filters['datetime'])){
                $format = 'Y-m-d H:i:s';
                $value = $inputs[$i];
                $datetime = $this->newDateTime($value);
                if ($datetime) {
                    $inputs[$i] = $datetime->format($format);
                }
            }

            //Sanitize uppercase
            if(!empty($filters['uppercase']) && in_array($i,$filters['uppercase'])){
                $inputs[$i] = $this->strtoupper($inputs[$i]);
            }

            //Sanitize ucfirst
            //Sanitizes a string to begin with uppercase.
            if(!empty($filters['ucfirst']) && in_array($i,$filters['ucfirst'])){
                $inputs[$i] = $this->ucfirst($inputs[$i]);
            }

            //Sanitize html
            if(!empty($filters['html']) && in_array($i,$filters['html'])){

                $string = strip_tags($inputs[$i], '<a><strong><em><hr><br><p><u><ul><ol><li><dl><dt><dd><table><thead><tr><th><tbody><td><tfoot>');
                $string = addslashes($string);
                $inputs[$i] = filter_var($string, FILTER_SANITIZE_STRING);
            }

            //Sanitize slug
            if(!empty($filters['slug']) && in_array($i,$filters['slug'])){
                $string = str_slug($inputs[$i]);
                $inputs[$i] = filter_var($string, FILTER_SANITIZE_URL);
            }




        }

        $request = new Request();
        $request->replace($inputs);
        return $inputs;
    }

    protected function newDateTime($value)
    {
        if ($value instanceof \DateTime) {
            return $value;
        }
        if (! is_scalar($value)) {
            return false;
        }
        if (trim($value) === '') {
            return false;
        }
        $datetime = date_create($value);
        // invalid dates (like 1979-02-29) show up as warnings.
        $errors = \DateTime::getLastErrors();
        if ($errors['warnings']) {
            return false;
        }
        // looks OK
        return $datetime;
    }

    /**
     *
     * Proxy to `mb_convert_case()` when available; fall back to
     * `utf8_decode()` and `strtolower()` otherwise.
     *
     * @param string $str String to convert case.
     *
     * @return string
     *
     */
    protected function strtolower($str)
    {
        if ($this->mbstring()) {
            return mb_convert_case($str, MB_CASE_LOWER, 'UTF-8');
        }
        return strtolower(utf8_decode($str));
    }
    /**
     *
     * Proxy to `mb_convert_case()` when available; fall back to
     * `utf8_decode()` and `strtoupper()` otherwise.
     *
     * @param string $str String to convert case.
     *
     * @return string
     *
     */
    protected function strtoupper($str)
    {
        if ($this->mbstring()) {
            return mb_convert_case($str, MB_CASE_UPPER, 'UTF-8');
        }
        return strtoupper(utf8_decode($str));
    }
    /**
     *
     * Proxy to `mb_convert_case()` when available; fall back to
     * `utf8_decode()` and `ucwords()` otherwise.
     *
     * @param string $str String to convert case.
     *
     * @return int
     *
     */
    protected function ucwords($str)
    {
        if ($this->mbstring()) {
            return mb_convert_case($str, MB_CASE_TITLE, 'UTF-8');
        }
        return ucwords(utf8_decode($str));
    }
    /**
     *
     * Proxy to `mb_convert_case()` when available; fall back to
     * `utf8_decode()` and `strtoupper()` otherwise.
     *
     * @param string $str String to convert case.
     *
     * @return int
     *
     */
    protected function ucfirst($str)
    {
        $len = $this->strlen($str);
        if ($len == 0) {
            return '';
        }
        if ($len > 1) {
            $head = $this->substr($str, 0, 1);
            $tail = $this->substr($str, 1, $len - 1);
            return $this->strtoupper($head) . $tail;
        }
        return $this->strtoupper($str);
    }
    /**
     *
     * Proxy to `mb_convert_case()` when available; fall back to
     * `utf8_decode()` and `strtolower()` otherwise.
     *
     * @param string $str String to convert case.
     *
     * @return int
     *
     */
    protected function lcfirst($str)
    {
        $len = $this->strlen($str);
        if ($len == 0) {
            // empty string
            return '';
        }
        if ($len > 1) {
            // more than a single character
            $head = $this->substr($str, 0, 1);
            $tail = $this->substr($str, 1, $len - 1);
            return $this->strtolower($head) . $tail;
        }
        return $this->strtolower($str);
    }
    /**
     *
     * Is the `mbstring` extension loaded?
     *
     * @return bool
     *
     */
    protected function mbstring()
    {
        return extension_loaded('mbstring');
    }
    /**
     *
     * Is the `iconv` extension loaded?
     *
     * @return bool
     *
     */
    protected function iconv()
    {
        return extension_loaded('iconv');
    }
    /**
     *
     * Proxy to `iconv_strlen()` or `mb_strlen()` when available; fall back to
     * `utf8_decode()` and `strlen()` otherwise.
     *
     * @param string $str Return the number of characters in this string.
     *
     * @return int
     *
     */
    protected function strlen($str)
    {
        if ($this->iconv()) {
            return $this->strlenIconv($str);
        }
        if ($this->mbstring()) {
            return mb_strlen($str, 'UTF-8');
        }
        return strlen(utf8_decode($str));
    }
    /**
     *
     * Wrapper for `iconv_substr()` to throw an exception on malformed UTF-8.
     *
     * @param string $str The string to work with.
     *
     * @param int $start Start at this position.
     *
     * @param int $length End after this many characters.
     *
     * @return string
     *
     * @throws Exception
     *
     */
    protected function substrIconv($str,$start,$length)
    {
        $level = error_reporting(0);
        $substr = iconv_substr($str,$start,$length, 'UTF-8');
        error_reporting($level);
        if ($substr !== false) {
            return $substr;
        }
        throw new \HttpRequestException('exception on malformed UTF-8');

    }

    /**
     *
     * Wrapper for `iconv_strlen()` to throw an exception on malformed UTF-8.
     *
     * @param string $str Return the number of characters in this string.
     *
     * @return int
     *
     * @throws Exception
     *
     */
    protected function strlenIconv($str)
    {
        $level = error_reporting(0);
        $strlen = iconv_strlen($str, 'UTF-8');
        error_reporting($level);
        if ($strlen !== false) {
            return $strlen;
        }
        throw new \HttpRequestException('exception on malformed UTF-8');
    }
    /**
     *
     * Proxy to `iconv_substr()` or `mb_substr()` when the `mbstring` available;
     * polyfill via `preg_split()` and `array_slice()` otherwise.
     *
     * @param string $str The string to work with.
     *
     * @param int $start Start at this position.
     *
     * @param int $length End after this many characters.
     *
     * @return string

     *
     *
     */
    protected function substr($str, $start, $length = null)
    {
        if ($this->iconv()) {
            return $this->substrIconv($str, $start, $length);
        }
        if ($this->mbstring()) {
            return mb_substr($str, $start, $length, 'UTF-8');
        }
        $split = preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
        return implode('', array_slice($split, $start, $length));
    }
    /**
     *
     * Userland UTF-8-aware implementation of `str_pad()`.
     *
     * @param string $input The input string.
     *
     * @param int $pad_length If the value of pad_length is negative, less than,
     * or equal to the length of the input string, no padding takes place.
     *
     * @param string $pad_str Pad with this string. The pad_string may be
     * truncated if the required number of padding characters can't be evenly
     * divided by the pad_string's length.
     *
     * @param int $pad_type Optional argument pad_type can be STR_PAD_RIGHT,
     * STR_PAD_LEFT, or STR_PAD_BOTH. If pad_type is not specified it is
     * assumed to be STR_PAD_RIGHT.
     *
     * @return string
     *
     */
    protected function strpad($input, $pad_length, $pad_str = ' ', $pad_type = STR_PAD_RIGHT)
    {
        $input_len = $this->strlen($input);
        if ($pad_length <= $input_len) {
            return $input;
        }
        $pad_str_len = $this->strlen($pad_str);
        $pad_len = $pad_length - $input_len;
        if ($pad_type == STR_PAD_LEFT) {
            $repeat_times = ceil($pad_len / $pad_str_len);
            $prefix = str_repeat($pad_str, $repeat_times);
            return $this->substr($prefix, 0, floor($pad_len)) . $input;
        }
        if ($pad_type == STR_PAD_BOTH) {
            $pad_len /= 2;
            $pad_amount_left = floor($pad_len);
            $pad_amount_right = ceil($pad_len);
            $repeat_times_left = ceil($pad_amount_left / $pad_str_len);
            $repeat_times_right = ceil($pad_amount_right / $pad_str_len);
            $prefix = str_repeat($pad_str, $repeat_times_left);
            $padding_left = $this->substr($prefix, 0, $pad_amount_left);
            $suffix = str_repeat($pad_str, $repeat_times_right);
            $padding_right = $this->substr($suffix, 0, $pad_amount_right);
            return $padding_left . $input . $padding_right;
        }
        // STR_PAD_RIGHT
        $repeat_times = ceil($pad_len / $pad_str_len);
        $input .= str_repeat($pad_str, $repeat_times);
        return $this->substr($input, 0, $pad_length);
    }
    /**
     *
     * Does the value match the canonical UUID format?
     *
     * @param string $value The value to be checked.
     *
     * @return bool
     *
     */
    protected function isCanonical($value)
    {
        $regex = '/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i';
        return (bool) preg_match($regex, $value);
    }
    /**
     *
     * Is the value a hex-only UUID?
     *
     * @param string $value The value to be checked.
     *
     * @return bool
     *
     */
    protected function isHexOnly($value)
    {
        $regex = '/^[a-f0-9]{32}$/i';
        return (bool) preg_match($regex, $value);
    }
}
