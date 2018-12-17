# Laravel json api response

Multi database architecture with class to switch current database connection laravel framework.

## Installation

Require this package with composer:

```
composer require "dharmvijay/laravel-sanitize @dev"
```

## Filter types list
[1]: `trim` //Sanitize trim

[2]: `integers` //Sanitize integers

[3]: `float` //Sanitize float

[4]: `strings` //Sanitize strings

[5]: `emails` //Sanitize emails

[6]: `url` //Sanitize url

[7]: `encoded` //Sanitize encoded

[8]: `alnum` //Sanitize alnum - Strips non-alphanumeric characters from the value.

[9]: `word` //Sanitize word

[10]: `alpha` //Sanitize alpha - Strips non-alphabetic characters from the value.

[11]: `booleans` //Sanitize booleans

[12]: `datetime` //Sanitize datetime - default date-time formate : Y-m-d H:i:s

[13]: `uppercase` //Sanitize uppercase

[14]: `lowercase` //Sanitize lowercase

[15]: `ucfirst` //Sanitize ucfirst

[16]: `lcfirst` //Sanitize lcfirst

[17]: `html` //Sanitize html

[18]: `slug` //Sanitize slug

[18]: `special_chars` //Sanitize special_chars



## Usage

*1.Use SanitizedRequest trait in your any request file*
`use SanitizedRequest;`

*2.Create a protected variable and name it $filters. Here declare field names in filter type keys.*
```
    protected $filters = [
         'strings' => ['field_name1', 'field_name2', ...],
         'integers' => ['field_name1', ...],
         'emails' => ['field_name1', ...],
         'booleans' => ['field_name2' ....],
         '...more filter types ...'
    ];
 ```

*3. Use sanitize method in rules method same as below.*
```
    public function rules()
    {
        $this->sanitize(parent::all(), $this->filters);
        //Some Rules here
    }

```

*Example Full File*
```
<?php

namespace App\Http\Requests\API\v4;

use App\Models\Users;
use Illuminate\Foundation\Http\FormRequest;
use Dharmvijay\LaravelSanitize\SanitizedRequest;

class CreateUsersRequest extends FormRequest
{
    use SanitizedRequest;

    protected $filters = [
        'strings' => ['username'],
        'integers' => ['created_by'],
        'emails' => ['email'],
        'booleans' => ['status'],
    ];
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->sanitize(parent::all(), $this->filters);
        $createRules = Users::$rules;
        $createRules['email'] = 'required|unique:users,email';

        return $createRules;
    }
}

```

*That's it. Thanks for considering, Welcome contributions, Contact me - dbp264@gmail.com*
